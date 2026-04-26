<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Soporte</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Chat con soporte</h2>
        </div>
    </x-slot>

    <div class="py-8" data-support-chat data-messages-url="{{ route('support.messages', [], false) }}" data-send-url="{{ route('support.messages.store', [], false) }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (!empty($supportUnavailable))
                <div class="cl-surface p-6 border-amber-200/70 bg-amber-50/60 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                    <div class="font-semibold">El chat de soporte no está disponible por el momento.</div>
                    <div class="mt-1 text-sm opacity-90">Intenta de nuevo más tarde.</div>
                </div>
            @endif
            <div class="cl-surface p-6">
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Responde aquí y un agente te atenderá lo antes posible.
                </div>
            </div>

            <div class="cl-surface p-0 overflow-hidden">
                <div id="support-messages" class="h-[60vh] overflow-y-auto p-6 space-y-3"></div>
                <div class="border-t border-gray-200/80 dark:border-gray-800/80 p-4">
                    <form id="support-form" method="POST" action="{{ route('support.messages.store', [], false) }}" class="flex items-center gap-3">
                        @csrf
                        <input id="support-input" name="body" type="text" class="flex-1 rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="Escribe tu mensaje…" maxlength="2000" required />
                        <button id="support-send" type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 transition">
                            Enviar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-support-chat]');
            if (!root) return;

            const messagesUrl = root.getAttribute('data-messages-url');
            const sendUrl = root.getAttribute('data-send-url');
            const messagesEl = document.getElementById('support-messages');
            const form = document.getElementById('support-form');
            const input = document.getElementById('support-input');
            const sendBtn = document.getElementById('support-send');

            const supportUnavailable = @json(!empty($supportUnavailable));
            const currentUser = @js([
                'id' => auth()->id(),
                'name' => auth()->user()?->name,
                'role' => auth()->user()?->role,
            ]);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            let lastId = 0;
            let polling = null;
            let sending = false;

            async function httpGet(url, params) {
                if (window.axios) {
                    return window.axios.get(url, { params });
                }

                const q = new URLSearchParams(params || {}).toString();
                const res = await fetch(q ? `${url}?${q}` : url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({}));
                return { data, status: res.status, ok: res.ok };
            }

            async function httpPost(url, payload) {
                if (window.axios) {
                    return window.axios.post(url, payload);
                }

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload || {}),
                });
                const data = await res.json().catch(() => ({}));
                return { data, status: res.status, ok: res.ok };
            }

            function esc(s) {
                return (s ?? '')
                    .toString()
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderMessage(m) {
                const who = m.user?.name ? `${m.user.name}` : 'Sistema';
                let time = '';
                if (m.created_at) {
                    const d = new Date(m.created_at);
                    time = Number.isNaN(d.getTime()) ? `${m.created_at}` : d.toLocaleString();
                }

                const isSelf = Number(m.user?.id || 0) === Number(currentUser?.id || 0);

                const row = document.createElement('div');
                row.className = `flex ${isSelf ? 'justify-end' : 'justify-start'}`;
                if (m._tempId) row.dataset.tempId = m._tempId;
                if (m.id) row.dataset.id = m.id;

                const bubble = document.createElement('div');
                bubble.className = isSelf
                    ? 'max-w-[78%] rounded-2xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm'
                    : 'max-w-[78%] rounded-2xl border border-gray-200/80 bg-white/70 px-4 py-2 text-sm text-gray-900 shadow-sm backdrop-blur dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white';

                bubble.innerHTML = `
                    ${isSelf ? '' : `<div class="text-[11px] font-semibold text-gray-700 dark:text-gray-200">${esc(who)}</div>`}
                    <div class="whitespace-pre-wrap">${esc(m.body)}</div>
                    ${time ? `<div class="mt-1 text-[10px] ${isSelf ? 'text-emerald-50/90' : 'text-gray-500 dark:text-gray-400'} ${isSelf ? 'text-right' : ''}">${esc(time)}</div>` : ''}
                `;

                row.appendChild(bubble);
                messagesEl.appendChild(row);
            }

            function scrollToBottom() {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            async function loadInitial() {
                messagesEl.innerHTML = '';
                for (const m of @json($messages)) {
                    renderMessage({
                        id: m.id,
                        body: m.body,
                        created_at: m.created_at,
                        user: m.user ? { id: m.user.id, name: m.user.name, role: m.user.role } : null,
                    });
                    lastId = Math.max(lastId, m.id);
                }
                scrollToBottom();
            }

            async function poll() {
                try {
                    const res = await httpGet(messagesUrl, { after_id: lastId });
                    const list = res?.data?.messages ?? [];
                    if (!Array.isArray(list) || list.length === 0) return;
                    for (const m of list) {
                        renderMessage(m);
                        lastId = Math.max(lastId, Number(m.id || 0));
                    }
                    scrollToBottom();
                } catch (e) {
                }
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (sending) return;
                const body = input.value.trim();
                if (!body) return;
                if (supportUnavailable) return;

                sending = true;
                sendBtn.disabled = true;
                try {
                    const tempId = `tmp_${Date.now()}_${Math.random().toString(16).slice(2)}`;
                    renderMessage({
                        _tempId: tempId,
                        body,
                        created_at: new Date().toISOString(),
                        user: currentUser,
                    });
                    scrollToBottom();

                    const res = await httpPost(sendUrl, { body });
                    const saved = res?.data;
                    if (saved?.id) {
                        lastId = Math.max(lastId, Number(saved.id || 0));
                        const pendingEl = messagesEl.querySelector(`[data-temp-id="${tempId}"]`);
                        if (pendingEl) pendingEl.remove();
                        renderMessage({
                            id: saved.id,
                            body: saved.body ?? body,
                            created_at: saved.created_at ?? new Date().toISOString(),
                            user: currentUser,
                        });
                        scrollToBottom();
                    } else {
                        await poll();
                    }
                    input.value = '';
                } catch (err) {
                    console.error(err);
                    await poll();
                } finally {
                    sending = false;
                    sendBtn.disabled = false;
                    input.focus();
                }
            });

            loadInitial().then(() => {
                if (supportUnavailable) return;
                polling = setInterval(poll, 2000);
            });

            window.addEventListener('beforeunload', () => {
                if (polling) clearInterval(polling);
            });
        })();
    </script>
</x-app-layout>
