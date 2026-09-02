@extends('layout.layoutDashboard')

@section('title', 'Chat')

@push('styles')
<style>
    /* Reset & Base */
    * { box-sizing: border-box; }

    .chat-page-wrapper {
        display: flex;
        height: calc(100vh - 150px);
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.06), 0 2px 5px 0 rgba(0,0,0,.2);
        background: #f0f2f5;
        margin-bottom: 20px;
    }

    /* ===== SIDEBAR ===== */
    .chat-sidebar {
        width: 30%;
        min-width: 300px;
        max-width: 415px;
        background: #ffffff;
        border-right: 1px solid #d1d7db;
        display: flex;
        flex-direction: column;
    }
    .chat-sidebar-header {
        height: 59px;
        padding: 10px 16px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #d1d7db;
        flex-shrink: 0;
    }
    .chat-sidebar-header .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .chat-sidebar-header .user-profile-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #fff;
        background-color: #00a884;
    }
    .chat-sidebar-header .user-name-label {
        font-weight: 600;
        color: #111b21;
        font-size: 0.95rem;
    }
    .chat-search-wrapper {
        padding: 8px 12px;
        background: #ffffff;
        border-bottom: 1px solid #f0f2f5;
    }
    .chat-search-inner {
        display: flex;
        align-items: center;
        background: #f0f2f5;
        border-radius: 8px;
        padding: 0 12px;
        height: 35px;
    }
    .chat-search-inner .search-icon {
        color: #54656f;
        font-size: 0.85rem;
        margin-right: 14px;
    }
    .chat-search-input {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 0.9rem;
        outline: none;
        color: #111b21;
    }
    .chat-search-input::placeholder {
        color: #54656f;
    }
    .contact-list {
        flex: 1;
        overflow-y: auto;
        background: #ffffff;
    }
    .contact-item {
        display: flex;
        align-items: center;
        height: 72px;
        padding: 0 12px 0 12px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .contact-item:hover { background: #f5f6f6; }
    .contact-item.active { background: #f0f2f5; }
    .contact-avatar {
        width: 49px;
        height: 49px;
        border-radius: 50%;
        color: #fff;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 14px;
        background-color: #6b7cff;
    }
    .contact-info-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-bottom: 1px solid #f2f2f2;
        height: 100%;
        padding-right: 12px;
        overflow: hidden;
    }
    .contact-item:last-child .contact-info-wrapper {
        border-bottom: none;
    }
    .contact-row-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2px;
    }
    .contact-name {
        font-weight: 400;
        font-size: 1.05rem;
        color: #111b21;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .contact-time {
        font-size: 0.75rem;
        color: #667781;
    }
    .contact-row-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .contact-preview {
        font-size: 0.85rem;
        color: #667781;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        margin-right: 10px;
    }
    .contact-badge {
        background: #25d366;
        color: white;
        border-radius: 10px;
        min-width: 20px;
        height: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        display: none; /* shown programmatically */
    }

    /* ===== CHAT AREA ===== */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #efeae2;
        background-image: url("https://web.whatsapp.com/img/bg-chat-tile-dark_a4be512e7195b6b733d9110b408f075d.png");
        background-size: initial;
        position: relative;
    }
    .chat-main::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d9d4cb' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
        z-index: 0;
    }
    .chat-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f0f2f5;
        z-index: 1;
        text-align: center;
        border-bottom: 6px solid #25d366;
    }
    .chat-empty img { width: 320px; max-width: 100%; margin-bottom: 28px; opacity: 0.7; }
    .chat-empty h5 { font-weight: 300; font-size: 2rem; color: #41525d; margin-bottom: 16px; }
    .chat-empty p { font-size: 0.9rem; color: #667781; max-width: 400px; line-height: 1.5; }
    
    .chat-area { display: none; flex-direction: column; height: 100%; z-index: 1; }
    .chat-area.visible { display: flex; }

    .chat-area-header {
        height: 59px;
        padding: 10px 16px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #d1d7db;
        flex-shrink: 0;
    }
    .chat-area-header .contact-avatar { 
        width: 40px; height: 40px; margin-right: 15px; font-size: 1rem;
    }
    .chat-area-header .hd-name { font-weight: 400; color: #111b21; font-size: 1.05rem; }
    .chat-area-header .hd-status { font-size: 0.8rem; color: #667781; margin-top: 2px; }

    .chat-messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px 6%;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .msg-bubble {
        max-width: 65%;
        padding: 6px 7px 8px 9px;
        border-radius: 7.5px;
        position: relative;
        word-break: break-word;
        font-size: 0.9rem;
        line-height: 1.35;
        box-shadow: 0 1px 0.5px rgba(11,20,26,.13);
        display: flex;
        flex-direction: column;
    }
    .msg-incoming {
        background: #ffffff;
        align-self: flex-start;
        border-top-left-radius: 0;
    }
    .msg-incoming::before {
        content: "";
        position: absolute;
        top: 0; left: -8px;
        width: 8px; height: 13px;
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 8 13" width="8" height="13" xmlns="http://www.w3.org/2000/svg"><path fill="%23ffffff" d="M1.533 3.568L8 12.193V1H2.812C1.042 1 .474 2.156 1.533 3.568z"/></svg>') center/contain no-repeat;
    }
    .msg-outgoing {
        background: #d9fdd3;
        align-self: flex-end;
        border-top-right-radius: 0;
    }
    .msg-outgoing::before {
        content: "";
        position: absolute;
        top: 0; right: -8px;
        width: 8px; height: 13px;
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 8 13" width="8" height="13" xmlns="http://www.w3.org/2000/svg"><path fill="%23d9fdd3" d="M5.188 1H0v11.193l6.467-8.625C7.526 2.156 6.958 1 5.188 1z"/></svg>') center/contain no-repeat;
    }
    
    .msg-content {
        color: #111b21;
        display: inline-block;
    }
    .msg-meta {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        float: right;
        margin-top: 5px;
        margin-left: 10px;
        height: 15px;
    }
    .msg-time {
        font-size: 0.68rem;
        color: #667781;
    }
    .msg-date-separator {
        text-align: center;
        margin: 12px 0;
    }
    .msg-date-separator span {
        background: #ffffff;
        color: #54656f;
        font-size: 0.75rem;
        border-radius: 7.5px;
        padding: 5px 12px;
        box-shadow: 0 1px 0.5px rgba(11,20,26,.13);
        display: inline-block;
    }

    .chat-input-footer {
        padding: 10px 16px;
        background: #f0f2f5;
        display: flex;
        align-items: flex-end;
        gap: 12px;
        min-height: 62px;
    }
    .input-icon-btn {
        padding: 10px 0;
        color: #54656f;
        cursor: pointer;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
    }
    .chat-input-box {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 0.95rem;
        outline: none;
        background: #ffffff;
        resize: none;
        max-height: 100px;
        min-height: 42px;
        overflow-y: auto;
        line-height: 1.5;
        color: #111b21;
        margin-bottom: 2px;
    }
    .btn-send-chat {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: transparent;
        border: none;
        color: #54656f;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color 0.2s;
        flex-shrink: 0;
        margin-bottom: 2px;
    }
    .btn-send-chat:hover { color: #00a884; }

    /* Attachment Popover WhatsApp Style */
    .attachment-popover {
        position: absolute;
        bottom: 70px;
        left: 50px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 5px 0 rgba(11,20,26,.26), 0 2px 10px 0 rgba(11,20,26,.16);
        padding: 16px 20px;
        display: none;
        flex-direction: column;
        gap: 20px;
        z-index: 100;
        animation: popoverFadeIn 0.2s ease-out;
    }
    .attachment-popover.active {
        display: flex;
    }
    .attach-option {
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        transition: transform 0.15s;
    }
    .attach-option:hover {
        transform: scale(1.02);
    }
    .attach-icon {
        width: 53px;
        height: 53px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.4rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .attach-icon.doc { background: #7F66FF; }
    .attach-icon.photo { background: #007DFC; }
    .attach-label {
        font-size: 1rem;
        color: #111b21;
        font-weight: 400;
    }
    @keyframes popoverFadeIn {
        from { opacity: 0; transform: translateY(10px) scale(0.95); transform-origin: bottom left; }
        to { opacity: 1; transform: translateY(0) scale(1); transform-origin: bottom left; }
    }

    /* Scrollbar styling */
    .contact-list::-webkit-scrollbar,
    .chat-messages-area::-webkit-scrollbar { width: 6px; }
    .contact-list::-webkit-scrollbar-track,
    .chat-messages-area::-webkit-scrollbar-track { background: transparent; }
    .contact-list::-webkit-scrollbar-thumb,
    .chat-messages-area::-webkit-scrollbar-thumb { background: rgba(11,20,26,.2); border-radius: 4px; }

    /* Loading indicator */
    .typing-indicator span {
        height: 8px; width: 8px;
        border-radius: 50%;
        background: #8696a0;
        display: inline-block;
        animation: blink 1.4s infinite both;
        margin: 0 2px;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes blink {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.4; }
        40% { transform: scale(1.1); opacity: 1; }
    }
    /* Emoji picker styling */
    .picmo-popup { z-index: 1000; }
</style>
<!-- PicMo (Emoji picker) CDN -->
<script src="https://unpkg.com/@picmo/picker@5.8.1/dist/umd/picmo.umd.js"></script>
<script src="https://unpkg.com/@picmo/popup-picker@5.8.1/dist/umd/picmo-popup.umd.js"></script>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="chat-page-wrapper">

        {{-- SIDEBAR --}}
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="user-profile">
                    <div class="user-profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-name-label">
                        {{ session('auth')['nama_user'] ?? $authId }}
                    </div>
                </div>
            </div>
            <div class="chat-search-wrapper">
                <div class="chat-search-inner">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="chat-search-input" id="searchContact" placeholder="Cari atau mulai chat baru">
                </div>
            </div>
            <div class="contact-list" id="contactList">
                @foreach($users as $user)
                <div class="contact-item"
                     data-id="{{ $user->id }}"
                     data-name="{{ $user->name }}"
                     onclick="openChat('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                    <div class="contact-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="contact-info-wrapper">
                        <div class="contact-row-top">
                            <div class="contact-name">{{ $user->name }}</div>
                            <div class="contact-time"></div>
                        </div>
                        <div class="contact-row-bottom">
                            <div class="contact-preview" id="preview-{{ $user->id }}">Ketuk untuk buka obrolan</div>
                            <div class="contact-badge" id="badge-{{ $user->id }}">0</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- MAIN CHAT --}}
        <div class="chat-main" id="chatMain">
            {{-- Empty State --}}
            <div class="chat-empty" id="chatEmpty">
                <i class="fab fa-whatsapp" style="font-size: 5rem; color: #d0d5d8;"></i>
                <h5>WhatsApp for Web</h5>
                <p>Send and receive messages without keeping your phone online.<br>Use WhatsApp on up to 4 linked devices and 1 phone at the same time.</p>
            </div>

            {{-- Chat Area (hidden initially) --}}
            <div class="chat-area" id="chatArea">
                <div class="chat-area-header">
                    <div class="contact-avatar" id="hdAvatar" style="background-color: #6b7cff;">?</div>
                    <div>
                        <div class="hd-name" id="hdName">-</div>
                        <div class="hd-status" id="hdStatus">Online</div>
                    </div>
                </div>
                <div class="chat-messages-area" id="chatMessagesArea">
                    {{-- messages here --}}
                </div>
                <div class="chat-input-footer" style="position: relative;">
                    <!-- Preview Area -->
                    <div id="attachmentPreview" style="display:none; position: absolute; bottom: 100%; left: 0; right: 0; background: #f0f2f5; padding: 15px; border-top: 1px solid #d1d7db; z-index: 10;">
                        <div style="position: relative; display: inline-block; background: #fff; padding: 8px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <img id="attachmentPreviewImg" src="" style="max-height: 120px; border-radius: 4px; display: none;">
                            <div id="attachmentPreviewDoc" style="display: none; padding: 10px; font-size: 0.9rem; color: #111b21;">
                                <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #54656f; vertical-align: middle; margin-right: 8px;"></i> <span id="attachmentPreviewName"></span>
                            </div>
                            <button onclick="clearAttachment()" style="position: absolute; top: -10px; right: -10px; background: #54656f; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;">&times;</button>
                        </div>
                    </div>

                    <button class="input-icon-btn" id="btnEmoji"><i class="fas fa-smile"></i></button>
                    <button class="input-icon-btn" id="btnAttachment" onclick="toggleAttachmentPopover()"><i class="fas fa-paperclip"></i></button>
                    
                    <!-- Attachment Popover -->
                    <div class="attachment-popover" id="attachmentPopover">
                        <div class="attach-option" onclick="triggerAttach('document')">
                            <div class="attach-icon doc"><i class="fas fa-file-alt"></i></div>
                            <div class="attach-label">Dokumen</div>
                        </div>
                        <div class="attach-option" onclick="triggerAttach('image')">
                            <div class="attach-icon photo"><i class="fas fa-image"></i></div>
                            <div class="attach-label">Foto & Video</div>
                        </div>
                    </div>

                    <input type="file" id="chatAttachmentInput" style="display: none;" onchange="handleAttachment(event)">
                    
                    <textarea class="chat-input-box" id="chatInputBox" rows="1" placeholder="Ketik pesan" onkeydown="handleKeydown(event)"></textarea>
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
    const AUTH_ID = '{{ $authId }}';
    let activeUserId = null;
    let lastMessageId = 0;
    let lastDate = null;
    let isPolling = false;
    let unreadCounts = {};
    let pollTimer = null;

    function playNotifSound() {
        const a = document.getElementById('chatNotificationSound');
        if (a) {
            a.currentTime = 0;
            a.play().catch(e => {}); // Ignore autoplay blocked errors
        }
    }

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
                    appendMessage(area, msg.message, 'incoming', msg.created_at, msg.attachment_path, msg.attachment_type);
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
                appendMessage(area, msg.message, msg.sender_id == AUTH_ID ? 'outgoing' : 'incoming', msg.created_at, msg.attachment_path, msg.attachment_type);
                if (msg.id > lastMessageId) lastMessageId = msg.id;
            });
        }
        scrollToBottom();
    } catch (e) {
        area.innerHTML = '<div style="text-align:center;color:red;padding:20px;">Gagal memuat pesan.</div>';
    }
}

// ── Append bubble ──
function appendMessage(container, text, type, time, attachmentPath = null, attachmentType = null) {
    const bubble  = document.createElement('div');
    bubble.className = `msg-bubble msg-${type}`;
    const d       = new Date(time);
    const timeStr = d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    
    let checkIcon = type === 'outgoing' ? ' <i class="fas fa-check-double" style="color:#53bdeb;font-size:0.65rem;"></i>' : '';
    
    let attachmentHtml = '';
    if (attachmentPath) {
        let fileUrl = `{{ asset('storage') }}/${attachmentPath}`;
        if (attachmentType === 'image') {
            attachmentHtml = `<div style="margin-bottom: 4px;"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" style="max-width: 100%; max-height: 250px; border-radius: 6px; display: block;"></a></div>`;
        } else {
            attachmentHtml = `<div style="margin-bottom: 4px; padding: 10px 12px; background: rgba(0,0,0,0.04); border-radius: 6px;"><a href="${fileUrl}" target="_blank" style="color: #111b21; text-decoration: none; font-weight: 500; font-size: 0.85rem;"><i class="fas fa-file-alt" style="margin-right:6px; color:#54656f;"></i> Buka Dokumen</a></div>`;
        }
    }

    let textHtml = text ? `<div class="msg-content">${escapeHtml(text)}</div>` : '';

    bubble.innerHTML = `
        ${attachmentHtml}
        ${textHtml}
        <div class="msg-meta">
            <span class="msg-time">${timeStr}</span>
            ${checkIcon}
        </div>
    `;
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
let selectedFile = null;

function toggleAttachmentPopover() {
    document.getElementById('attachmentPopover').classList.toggle('active');
}

function triggerAttach(type) {
    document.getElementById('attachmentPopover').classList.remove('active');
    const input = document.getElementById('chatAttachmentInput');
    if (type === 'image') {
        input.accept = 'image/*,video/*';
    } else {
        input.accept = '*/*';
    }
    input.click();
}

document.addEventListener('click', function(e) {
    const pop = document.getElementById('attachmentPopover');
    const btn = document.getElementById('btnAttachment');
    if (pop && btn && pop.classList.contains('active') && !pop.contains(e.target) && !btn.contains(e.target)) {
        pop.classList.remove('active');
    }
});

function handleAttachment(event) {
    const file = event.target.files[0];
    if (!file) return;
    selectedFile = file;

    const previewArea = document.getElementById('attachmentPreview');
    const previewImg = document.getElementById('attachmentPreviewImg');
    const previewDoc = document.getElementById('attachmentPreviewDoc');
    const previewName = document.getElementById('attachmentPreviewName');

    previewArea.style.display = 'block';
    if (file.type.startsWith('image/')) {
        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = 'block';
        previewDoc.style.display = 'none';
    } else {
        previewImg.style.display = 'none';
        previewName.textContent = file.name;
        previewDoc.style.display = 'block';
    }
}

function clearAttachment() {
    selectedFile = null;
    document.getElementById('chatAttachmentInput').value = '';
    document.getElementById('attachmentPreview').style.display = 'none';
}

async function sendMessage() {
    const inputEl = document.getElementById('chatInputBox');
    const text    = inputEl.value.trim();
    if ((!text && !selectedFile) || !activeUserId) return;

    inputEl.value = '';
    inputEl.style.height = 'auto';
    
    const fileToSend = selectedFile; // Store locally
    
    // We can't perfectly optimistically render the attachment without knowing the server path, 
    // so we will show a placeholder or just wait for poll. But for text, we can show it:
    const area = document.getElementById('chatMessagesArea');
    if (!fileToSend) {
        appendMessage(area, text, 'outgoing', new Date().toISOString());
        scrollToBottom();
    } else {
        // Clear preview immediately, but keep the local file reference to send
        clearAttachment();
    }

    const prev = document.getElementById(`preview-${activeUserId}`);
    if (prev) prev.textContent = fileToSend ? 'Terkirim file' : ('✓ ' + text);

    const fd = new FormData();
    fd.append('receiver_id', activeUserId);
    if (text) fd.append('message', text);
    if (fileToSend) fd.append('attachment', fileToSend);
    fd.append('_token', '{{ csrf_token() }}');

    try {
        const r    = await fetch('{{ url("/chat/messages") }}', {method:'POST', body:fd});
        const data = await r.json();
        if (data.message && data.message.id > lastMessageId) {
            lastMessageId = data.message.id;
        }
        if (fileToSend) {
            // Append the actual saved message from server
            appendMessage(area, data.message.message, 'outgoing', data.message.created_at, data.message.attachment_path, data.message.attachment_type);
            scrollToBottom();
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

// ── Emoji Picker ──
document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('btnEmoji');
    const input = document.getElementById('chatInputBox');
    if (window.picmoPopup) {
        const picker = window.picmoPopup.createPopup({
            animate: false
        }, {
            referenceElement: trigger,
            triggerElement: trigger,
            position: 'top-start'
        });

        trigger.addEventListener('click', () => {
            picker.toggle();
        });

        picker.addEventListener('emoji:select', (selection) => {
            input.value += selection.emoji;
            input.focus();
        });
    }
});
</script>
@endpush
