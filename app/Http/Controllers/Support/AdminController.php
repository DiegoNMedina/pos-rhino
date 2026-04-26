<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            $conversations = new LengthAwarePaginator([], 0, 25, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('support.admin.index', [
                'conversations' => $conversations,
                'q' => $q,
                'supportUnavailable' => true,
            ]);
        }

        $conversations = SupportConversation::query()
            ->with('store')
            ->when($q !== '', function ($builder) use ($q) {
                $builder->whereHas('store', function ($inner) use ($q) {
                    $inner
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('code', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('support.admin.index', [
            'conversations' => $conversations,
            'q' => $q,
            'supportUnavailable' => false,
        ]);
    }

    public function show(SupportConversation $conversation)
    {
        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            abort(503);
        }

        $messages = SupportMessage::query()
            ->with('user')
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->limit(50)
            ->get();

        return view('support.admin.show', [
            'conversation' => $conversation->load('store'),
            'messages' => $messages,
        ]);
    }

    public function messages(Request $request, SupportConversation $conversation)
    {
        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            return response()->json([
                'messages' => [],
                'error' => 'Soporte no disponible por el momento.',
            ], 503);
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

    public function storeMessage(Request $request, SupportConversation $conversation)
    {
        $user = $request->user();
        abort_unless($user, 403);

        if (! Schema::hasTable('support_conversations') || ! Schema::hasTable('support_messages')) {
            return response()->json([
                'error' => 'Soporte no disponible por el momento.',
            ], 503);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

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
