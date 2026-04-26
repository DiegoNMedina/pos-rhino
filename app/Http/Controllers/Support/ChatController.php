<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ChatController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            return view('support.chat', [
                'conversation' => null,
                'messages' => collect(),
                'supportUnavailable' => true,
            ]);
        }

        $conversation = SupportConversation::query()->firstOrCreate(
            ['store_id' => $user->store->id],
            ['status' => 'open', 'last_message_at' => now()]
        );

        $messages = SupportMessage::query()
            ->with('user')
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->limit(50)
            ->get();

        return view('support.chat', [
            'conversation' => $conversation,
            'messages' => $messages,
            'supportUnavailable' => false,
        ]);
    }

    public function messages(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            return response()->json([
                'messages' => [],
                'error' => 'Soporte no disponible por el momento.',
            ], 503);
        }

        $conversation = SupportConversation::query()
            ->where('store_id', $user->store->id)
            ->first();

        if (! $conversation) {
            return response()->json(['messages' => []]);
        }

        $afterId = (int) $request->query('after_id', 0);

        $messages = SupportMessage::query()
            ->with('user')
            ->where('conversation_id', $conversation->id)
            ->when($afterId > 0, fn ($q) => $q->where('id', '>', $afterId))
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(function (SupportMessage $m) {
                return [
                    'id' => $m->id,
                    'body' => $m->body,
                    'created_at' => $m->created_at ? $m->created_at->toISOString() : null,
                    'user' => $m->user ? ['id' => $m->user->id, 'name' => $m->user->name, 'role' => $m->user->role] : null,
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function storeMessage(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            return response()->json([
                'error' => 'Soporte no disponible por el momento.',
            ], 503);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = SupportConversation::query()->firstOrCreate(
            ['store_id' => $user->store->id],
            ['status' => 'open', 'last_message_at' => now()]
        );

        $message = SupportMessage::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $conversation->last_message_at = now();
        $conversation->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at ? $message->created_at->toISOString() : null,
            ]);
        }

        return redirect()->back();
    }
}
