<?php
session_start();
if (!isset($_SESSION['nom'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Private Chat with Encadrant 1</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="max-w-xl mx-auto w-full flex flex-col h-screen">
        <header class="bg-green-600 text-white px-6 py-4 rounded-b-lg shadow flex items-center justify-between">
            <h1 class="text-xl font-bold">Private Chat: Encadrant 1</h1>
            <a href="../index.php" class="text-green-200 hover:text-white transition">Back</a>
        </header>
        <main id="messagesContainer" class="flex-1 overflow-y-auto px-4 py-6 space-y-4 bg-white rounded-lg shadow mt-4"></main>
        <form class="flex items-center px-4 py-3 bg-white rounded-b-lg shadow mt-2" onsubmit="return false;">
            <input
                id="messageInput"
                type="text"
                placeholder="Type your message..."
                class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500"
                autocomplete="off"
                required
            >
            <button
                type="button"
                class="ml-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
                id="sendMessageBtn"
            >
                Send
            </button>
        </form>
    </div>
    <script>
let recipient = new URLSearchParams(window.location.search).get('user') || 'Encadrant 1';
let ws;
let nickname = '<?php echo addslashes($_SESSION["nom"]); ?>';

// Append message to chat container
function appendMessage(user, message, time) {
    const messagesContainer = document.getElementById('messagesContainer');
    const div = document.createElement('div');
    div.className = "my-2";
    div.innerHTML = `<strong>${user}:</strong> ${message} <span class="text-xs text-gray-400">${time || ''}</span>`;
    messagesContainer.appendChild(div);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function connectWebSocket() {
    ws = new WebSocket('ws://localhost:8080');
    ws.onopen = () => {
        ws.send(JSON.stringify({
            type: 'join',
            user: nickname
        }));
    };
    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.type === 'private_message' && 
            ((data.user === nickname && data.recipient === recipient) ||
             (data.user === recipient && data.recipient === nickname))) {
            appendMessage(data.user + ' (private)', data.message, data.time);
        } else if (data.type === 'error') {
            alert(data.message);
        }
    };
    ws.onerror = (err) => {
        alert("WebSocket error. Please try again later.");
        console.error(err);
    };
    ws.onclose = () => {
        setTimeout(connectWebSocket, 3000); // Try to reconnect
    };
}

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    if (message && ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'private_message',
            user: nickname,
            recipient: recipient,
            message: message,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }));
        messageInput.value = '';
    }
}

window.onload = () => {
    connectWebSocket();
    document.getElementById('sendMessageBtn').onclick = sendMessage;
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });
};
</script>
</body>
</html>