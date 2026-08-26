@extends('layout.layoutDashboard')

@section('title', 'Chat')

@push('styles')
<style>
    /* Reset & Base */
    * { box-sizing: border-box; }

    .chat-page-wrapper {
        display: flex;
        height: calc(100vh - 120px);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        background: #fff;
        margin: 0 10px;
    }

    /* ===== SIDEBAR ===== */
    .chat-sidebar {
        width: 320px;
        min-width: 280px;
        background: #fff;
        border-right: 1px solid #e9edef;
        display: flex;
        flex-direction: column;
    }
    .chat-sidebar-header {
        padding: 16px 20px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e9edef;
    }
    .chat-sidebar-header h6 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #111b21;
    }
    .chat-search-wrapper {
        padding: 8px 12px;
        background: #f0f2f5;
    }
    .chat-search-input {
        width: 100%;
        border: none;
        border-radius: 20px;
        padding: 8px 14px 8px 36px;
        font-size: 0.875rem;
        background: #fff;
        outline: none;
        color: #111b21;
    }
    .chat-search-wrapper .search-icon {
        position: absolute;
        left: 24px;
        top: 50%;
        transform: translateY(-50%);
        color: #8696a0;
        font-size: 0.8rem;
    }
    .chat-search-wrapper {
        position: relative;
    }
    .contact-list {
        flex: 1;
        overflow-y: auto;
    }
    .contact-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        cursor: pointer;
        border-bottom: 1px solid #f0f2f5;
        transition: background 0.15s;
    }
    .contact-item:hover { background: #f5f6f6; }
    .contact-item.active { background: #e9f0ff; }
    .contact-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6b7cff 0%, #25d366 100%);
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 14px;
    }
    .contact-info { flex: 1; overflow: hidden; }
    .contact-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #111b21;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .contact-preview {
        font-size: 0.78rem;
        color: #8696a0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }
    .contact-badge {
        background: #25d366;
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        display: none; /* shown programmatically */
    }

    /* ===== CHAT AREA ===== */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #efeae2;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d9d4cb' fill-opacity='0.3'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .chat-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #8696a0;
        background: #f0f2f5;
    }
    .chat-empty i { font-size: 3.5rem; margin-bottom: 16px; color: #dde2e9; }
    .chat-empty h5 { font-weight: 400; color: #41525d; }
    .chat-empty p { font-size: 0.85rem; margin-top: 4px; }
    .chat-area { display: none; flex-direction: column; height: 100%; }
    .chat-area.visible { display: flex; }

    .chat-area-header {
        padding: 12px 20px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e9edef;
    }
    .chat-area-header .contact-avatar { margin-right: 14px; }
    .chat-area-header .hd-name { font-weight: 700; color: #111b21; font-size: 0.95rem; }
    .chat-area-header .hd-status { font-size: 0.78rem; color: #667781; }

    .chat-messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .msg-bubble {
        max-width: 65%;
        padding: 8px 12px 6px;
        border-radius: 8px;
        position: relative;
        word-break: break-word;
        font-size: 0.88rem;
        line-height: 1.5;
    }
    .msg-incoming {
        background: #fff;
        align-self: flex-start;
        border-top-left-radius: 0;
    }
    .msg-outgoing {
        background: #d9fdd3;
        align-self: flex-end;
        border-top-right-radius: 0;
    }
    .msg-time {
        font-size: 0.68rem;
        color: #8696a0;
        text-align: right;
        display: block;
        margin-top: 2px;
    }
    .msg-date-separator {
        text-align: center;
        margin: 10px 0;
    }
    .msg-date-separator span {
        background: #fff;
        color: #667781;
        font-size: 0.75rem;
        border-radius: 6px;
        padding: 4px 12px;
    }

    .chat-input-footer {
        padding: 10px 16px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .chat-input-box {
        flex: 1;
        border: none;
        border-radius: 20px;
        padding: 10px 18px;
        font-size: 0.9rem;
        outline: none;
        background: #fff;
        resize: none;
        max-height: 100px;
        overflow-y: auto;
    }
    .btn-send-chat {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #25d366;
        border: none;
        color: white;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .btn-send-chat:hover { background: #1faa52; }

    /* Scrollbar styling */
    .contact-list::-webkit-scrollbar,
    .chat-messages-area::-webkit-scrollbar { width: 5px; }
    .contact-list::-webkit-scrollbar-thumb,
    .chat-messages-area::-webkit-scrollbar-thumb { background: #cdd1d4; border-radius: 4px; }

    /* Loading indicator */
    .typing-indicator span {
        height: 8px; width: 8px;
        border-radius: 50%;
        background: #8696a0;
        display: inline-block;
        animation: blink 1.4s infinite both;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes blink {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.4; }
        40% { transform: scale(1.1); opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="chat-page-wrapper">

        {{-- SIDEBAR --}}
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div>
                    <h6><i class="fas fa-comments mr-2" style="color:#25d366;"></i>Chat</h6>
                    <small class="text-muted" style="font-size:0.75rem;">Login sebagai: <strong>{{ session('auth')['nama_user'] ?? $authId }}</strong></small>
                </div>
            </div>
            <div class="chat-search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="chat-search-input" id="searchContact" placeholder="Cari atau mulai chat baru...">
            </div>
            <div class="contact-list" id="contactList">
                @foreach($users as $user)
                <div class="contact-item"
                     data-id="{{ $user->id }}"
                     data-name="{{ $user->name }}"
                     onclick="openChat('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                    <div class="contact-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $user->name }}</div>
                        <div class="contact-preview" id="preview-{{ $user->id }}">Ketuk untuk buka obrolan</div>
                    </div>
                    <div class="contact-badge" id="badge-{{ $user->id }}">0</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- MAIN CHAT --}}
        <div class="chat-main" id="chatMain">
            {{-- Empty State --}}
            <div class="chat-empty" id="chatEmpty">
                <i class="far fa-comments"></i>
                <h5>Selamat Datang di Chat Rumah Sakit</h5>
                <p>Pilih nama pengguna di sebelah kiri untuk mulai berkomunikasi.</p>
            </div>

            {{-- Chat Area (hidden initially) --}}
            <div class="chat-area" id="chatArea">
                <div class="chat-area-header">
                    <div class="contact-avatar" id="hdAvatar">?</div>
                    <div>
                        <div class="hd-name" id="hdName">-</div>
                        <div class="hd-status" id="hdStatus">Online</div>
                    </div>
                </div>
                <div class="chat-messages-area" id="chatMessagesArea">
                    {{-- messages here --}}
                </div>
                <div class="chat-input-footer">
                    <textarea class="chat-input-box" id="chatInputBox" rows="1" placeholder="Ketik pesan..." onkeydown="handleKeydown(event)"></textarea>
                    <button class="btn-send-chat" onclick="sendMessage()" title="Kirim">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Audio element untuk suara notifikasi iPhone -->
<audio id="chatNotificationSound" src="{{ asset('audio/iphone-notif.mp3') }}" preload="auto"></audio>
@endsection

@push('scripts')
<script>
// ============================================================
//  CHAT - Polling Real-time (WhatsApp style)
// ============================================================
const AUTH_ID       = "{{ $authId }}";
let activeUserId    = null;
let activeUserName  = null;
let unreadCounts    = {};
let lastMessageId   = 0;
let pollTimer       = null;
let isPolling       = false;

// ── Init: get latest message id so we only track NEW ones ──
(async function initLastId() {
    try {
        const r = await fetch('{{ url("/chat/poll") }}?last_id=0');
        const d = await r.json();
        lastMessageId = d.max_id || 0;
    } catch(e) {}
})();

// ── Notification permission ──
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// ── iPhone Messenger Sound ──
function playNotifSound() {
    let sound = document.getElementById("chatNotificationSound");
    if(sound) {
        sound.currentTime = 0; // Reset agar bisa diputar cepat berulang-ulang
        sound.play().catch(function(error) {
            console.log("Autoplay diblokir oleh browser: " + error);
        });
    }
}

// ── Browser notification ──
function showBrowserNotif(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const n = new Notification(title, {
            body: body.length > 80 ? body.substring(0, 80) + '...' : body,
            icon: '/favicon.ico',
            tag: 'chat-notif',
            renotify: true,
        });
        n.onclick = () => { window.focus(); n.close(); };
        setTimeout(() => n.close(), 5000);
    }
}

// ── Sidebar badge ──
function updateSidebarBadge() {
    const total  = Object.values(unreadCounts).reduce((a, b) => a + b, 0);
    const badge  = document.getElementById('sidebarChatBadge');
    if (!badge) return;
    badge.textContent = total > 99 ? '99+' : total;
    badge.style.display = total > 0 ? 'inline-flex' : 'none';
}

// ── Move contact to top of list ──
function moveContactToTop(userId) {
    const el   = document.querySelector(`.contact-item[data-id="${userId}"]`);
    const list = document.getElementById('contactList');
    if (el && list && list.firstChild !== el) list.insertBefore(el, list.firstChild);
}

// ── POLLING: check for new messages every 3s ──
async function poll() {
    if (isPolling) return;
    isPolling = true;
    try {
        const params = new URLSearchParams({ last_id: lastMessageId });
        if (activeUserId) params.append('active_user', activeUserId);

        const r    = await fetch(`{{ url('/chat/poll') }}?${params}`);
        const data = await r.json();

        // Update lastMessageId
        if (data.max_id > lastMessageId) lastMessageId = data.max_id;

        // ── New messages in active chat ──
        if (data.new_messages && data.new_messages.length > 0) {
            const area = document.getElementById('chatMessagesArea');
            data.new_messages.forEach(msg => {
                if (msg.sender_id != AUTH_ID) {
                    appendMessage(area, msg.message, 'incoming', msg.created_at);
                    playNotifSound();
                }
            });
            scrollToBottom();
        }

        // ── Unread from OTHER conversations ──
        if (data.unread && data.unread.length > 0) {
            data.unread.forEach(u => {
                const senderId = u.sender_id;
                const count    = parseInt(u.cnt);
                const preview  = u.last_message;
                const name     = document.querySelector(`.contact-item[data-id="${senderId}"]`)
                                    ?.getAttribute('data-name') || 'Seseorang';

                playNotifSound();
                showBrowserNotif(`💬 ${name}`, preview);

                unreadCounts[senderId] = (unreadCounts[senderId] || 0) + count;
                const badge = document.getElementById(`badge-${senderId}`);
                if (badge) {
                    badge.style.display = 'flex';
                    badge.textContent   = unreadCounts[senderId] > 99 ? '99+' : unreadCounts[senderId];
                }

                const prev = document.getElementById(`preview-${senderId}`);
                if (prev) prev.textContent = preview;

                moveContactToTop(senderId);
                updateSidebarBadge();
            });
        }

    } catch(e) { /* silent */ }
    finally { isPolling = false; }
}

// Start polling every 3 seconds
function startPolling() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(poll, 3000);
}
startPolling();

// ── Search/filter contacts ──
document.getElementById('searchContact').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.contact-item').forEach(el => {
        el.style.display = el.getAttribute('data-name').toLowerCase().includes(q) ? 'flex' : 'none';
    });
});

// ── Open chat ──
function openChat(userId, userName) {
    activeUserId   = userId;
    activeUserName = userName;

    document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
    const contactEl = document.querySelector(`.contact-item[data-id="${userId}"]`);
    if (contactEl) contactEl.classList.add('active');

    document.getElementById('chatEmpty').style.display = 'none';
    document.getElementById('chatArea').classList.add('visible');
    document.getElementById('hdAvatar').textContent = userName.charAt(0).toUpperCase();
    document.getElementById('hdName').textContent   = userName;
    document.getElementById('hdStatus').textContent = 'Staff Rumah Sakit';

    const badge = document.getElementById(`badge-${userId}`);
    if (badge) { badge.style.display = 'none'; badge.textContent = '0'; }
    if (unreadCounts[userId]) { delete unreadCounts[userId]; updateSidebarBadge(); }

    fetchMessages(userId);
}

// ── Fetch full message history ──
async function fetchMessages(userId) {
    const area = document.getElementById('chatMessagesArea');
    area.innerHTML = `<div style="text-align:center;padding:20px;">
        <div class="typing-indicator"><span></span><span></span><span></span></div>
    </div>`;

    try {
        const r        = await fetch(`{{ url('/chat/messages') }}/${userId}`);
        const messages = await r.json();
        area.innerHTML = '';

        if (messages.length === 0) {
            area.innerHTML = '<div style="text-align:center;color:#8696a0;font-size:0.85rem;margin-top:20px;">Belum ada pesan. Mulai percakapan! 👋</div>';
        } else {
            let lastDate = null;
            messages.forEach(msg => {
                const d       = new Date(msg.created_at);
                const dateStr = d.toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'});
                if (dateStr !== lastDate) {
                    const sep = document.createElement('div');
                    sep.className = 'msg-date-separator';
                    sep.innerHTML = `<span>${dateStr}</span>`;
                    area.appendChild(sep);
                    lastDate = dateStr;
                }
                appendMessage(area, msg.message, msg.sender_id == AUTH_ID ? 'outgoing' : 'incoming', msg.created_at);
                if (msg.id > lastMessageId) lastMessageId = msg.id;
            });
        }
        scrollToBottom();
    } catch (e) {
        area.innerHTML = '<div style="text-align:center;color:red;padding:20px;">Gagal memuat pesan.</div>';
    }
}

// ── Append bubble ──
function appendMessage(container, text, type, time) {
    const bubble  = document.createElement('div');
    bubble.className = `msg-bubble msg-${type}`;
    const d       = new Date(time);
    const timeStr = d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    bubble.innerHTML = `${escapeHtml(text)}<span class="msg-time">${timeStr}${type === 'outgoing'
        ? ' <i class="fas fa-check-double" style="color:#53bdeb;font-size:0.65rem;"></i>' : ''}</span>`;
    container.appendChild(bubble);
}

function escapeHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

function scrollToBottom() {
    const area = document.getElementById('chatMessagesArea');
    area.scrollTop = area.scrollHeight;
}

// ── Send message ──
async function sendMessage() {
    const inputEl = document.getElementById('chatInputBox');
    const text    = inputEl.value.trim();
    if (!text || !activeUserId) return;

    inputEl.value = '';
    inputEl.style.height = 'auto';

    const area = document.getElementById('chatMessagesArea');
    appendMessage(area, text, 'outgoing', new Date().toISOString());
    scrollToBottom();

    const prev = document.getElementById(`preview-${activeUserId}`);
    if (prev) prev.textContent = '✓ ' + text;

    const fd = new FormData();
    fd.append('receiver_id', activeUserId);
    fd.append('message', text);
    fd.append('_token', '{{ csrf_token() }}');

    try {
        const r    = await fetch('{{ url("/chat/messages") }}', {method:'POST', body:fd});
        const data = await r.json();
        if (data.message && data.message.id > lastMessageId) {
            lastMessageId = data.message.id;
        }
    } catch(e) {
        console.error('Gagal kirim:', e);
    }
}

// ── Textarea auto-resize ──
document.getElementById('chatInputBox').addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 100) + 'px';
});

function handleKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}
</script>
@endpush
