<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>LAN Messenger</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#0f172a;
    height:100vh;
    overflow:hidden;
}
.app{
    height:100vh;
    display:grid;
    grid-template-columns:320px 1fr;
}

/* ===== SIDEBAR ===== */
.sidebar{
    background:#020617;
    color:#fff;
    padding:15px;
    display:flex;
    flex-direction:column;
}
.profile{
    padding:12px;
    border-bottom:1px solid #1e293b;
}
.profile small{opacity:.6}

.user-list{
    flex:1;
    overflow-y:auto;
    margin-top:10px;
}
.user{
    padding:12px;
    border-radius:10px;
    cursor:pointer;
    margin-bottom:6px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.user:hover{background:#1e293b}
.user.active{background:#2563eb}
.user small{opacity:.6;font-size:11px}

.badge{
    background:red;
    color:#fff;
    font-size:11px;
    padding:2px 6px;
    border-radius:20px;
}

/* ===== CHAT ===== */
.chat{
    background:#f8fafc;
    display:flex;
    flex-direction:column;
    height:100vh;
}
.chat-header{
    padding:15px;
    background:#fff;
    border-bottom:1px solid #ddd;
    font-weight:600;
}
.typing{
    font-size:12px;
    color:#666;
    padding-left:15px;
}

.chat-box{
    flex:1;
    padding:15px;
    overflow-y:auto;
}

.bubble{
    max-width:65%;
    padding:10px 14px;
    margin-bottom:10px;
    border-radius:16px;
    font-size:14px;
}
.me{
    background:#2563eb;
    color:#fff;
    margin-left:auto;
}
.other{
    background:#e5e7eb;
}

.chat-input{
    background:#fff;
    padding:10px;
    display:flex;
    gap:10px;
    border-top:1px solid #ddd;
    position:sticky;
    bottom:0;
}
textarea{
    flex:1;
    resize:none;
    padding:10px;
    border-radius:10px;
    max-height:120px;
    overflow-y:auto;
}
button{
    background:#2563eb;
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    cursor:pointer;
}
button:disabled{opacity:.5}
</style>
</head>

<body>

<audio id="notif">
    <source src="https://notificationsounds.com/storage/sounds/file-sounds-1150-pristine.mp3">
</audio>

<div class="app">

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <div class="profile">
        <b>{{ $hostname }}</b><br>
        <small>{{ $ip }}</small>
    </div>

    <div class="user-list">
    @foreach($clients as $c)
        @if($c->ip != $ip)
        <div class="user" id="u{{ str_replace('.','',$c->ip) }}"
             onclick="openChat('{{ $c->ip }}','{{ $c->hostname }}')">
            <div>
                {{ $c->hostname }}<br>
                <small id="prev{{ str_replace('.','',$c->ip) }}">Belum ada pesan</small>
            </div>
            <span class="badge" id="badge{{ str_replace('.','',$c->ip) }}" style="display:none">1</span>
        </div>
        @endif
    @endforeach
    </div>
</div>

<!-- ===== CHAT ===== -->
<div class="chat">
    <div class="chat-header" id="header">💬 Pilih pengguna</div>
    <div class="typing" id="typing"></div>
    <div class="chat-box" id="chatBox"></div>

    <div class="chat-input">
        <textarea id="msg" placeholder="Tulis pesan..." disabled></textarea>
        <button id="send" disabled>Kirim</button>
    </div>
</div>

</div>

<script>
let activeIP = null;
let lastMsgId = 0;

const chatBox = document.getElementById('chatBox');
const msg = document.getElementById('msg');
const btn = document.getElementById('send');
const header = document.getElementById('header');
const sound = document.getElementById('notif');

function openChat(ip,name){
    activeIP = ip;
    header.innerHTML = "💬 " + name;
    msg.disabled = false;
    btn.disabled = false;

    document.querySelectorAll('.user').forEach(u=>u.classList.remove('active'));
    document.getElementById('u'+ip.replaceAll('.','')).classList.add('active');
    document.getElementById('badge'+ip.replaceAll('.','')).style.display='none';

    fetchChat();
}

/* ===== FETCH CHAT + NOTIF ===== */
function fetchChat(){
    fetch("{{ url('/lan/fetch') }}")
    .then(r=>r.json())
    .then(data=>{
        chatBox.innerHTML='';

        data.forEach(m=>{
            if(
                m.id > lastMsgId &&
                m.to_ip === "{{ $ip }}" &&
                m.from_ip !== activeIP
            ){
                document.getElementById('badge'+m.from_ip.replaceAll('.','')).style.display='inline';
                document.getElementById('prev'+m.from_ip.replaceAll('.','')).innerText=m.message;

                sound.currentTime=0;
                sound.play();
            }

            if(activeIP && (m.from_ip===activeIP || m.from_ip==="{{ $ip }}")){
                let d=document.createElement('div');
                d.className='bubble '+(m.from_ip==="{{ $ip }}"?'me':'other');
                d.innerHTML=m.message;
                chatBox.appendChild(d);
            }

            if(m.id > lastMsgId) lastMsgId=m.id;
        });

        chatBox.scrollTop = chatBox.scrollHeight;
    });
}

/* ===== AUTO RESIZE TEXTAREA ===== */
msg.addEventListener('input',()=>{
    msg.style.height='auto';
    msg.style.height=Math.min(msg.scrollHeight,120)+'px';
});

/* ===== SEND ===== */
btn.onclick=()=>{
    if(!msg.value.trim()) return;

    fetch("{{ url('/lan/send') }}",{
        method:"POST",
        headers:{
            "X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content,
            "Content-Type":"application/json"
        },
        body:JSON.stringify({message:msg.value,to_ip:activeIP})
    }).then(()=>{
        msg.value='';
        msg.style.height='auto';
        fetchChat();
    });
};

/* ===== AUTO REFRESH ===== */
setInterval(fetchChat,3000);

/* ===== FULLSCREEN KIOSK ===== */
document.documentElement.requestFullscreen().catch(()=>{});
</script>

</body>
</html>
