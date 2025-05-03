<?php
session_start();
include "../php/connection.php";

// Configuration sécurisée de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'g2_stage_etudiant_medcine');
define('DB_USER', 'root');
define('DB_PASS', 'hiba');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <title>Group Chat - Connect & Collaborate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer>
        let ws;
let typingTimeout;
let nickname = '<?php echo isset($_SESSION["nom"]) ? $_SESSION["nom"] : ""; ?>';

// Initialize chat when window loads
window.onload = () => {
    // Get DOM elements after the page has loaded
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const userName = document.getElementById('userName');
    const userInitials = document.getElementById('userInitials');
    const typingIndicator = document.getElementById('typingIndicator');
    const onlineUsers = document.getElementById('onlineUsers');
    const onlineCount = document.getElementById('onlineCount');
    const sendMessageBtn = document.getElementById('sendMessageBtn');
    
    if (userName) userName.innerHTML = nickname;
    if (userInitials) userInitials.textContent = nickname.substring(0, 2).toUpperCase();
    if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Add event listeners
    if (sendMessageBtn) {
        sendMessageBtn.addEventListener('click', sendMessage);
    }
    
    if (messageInput) {
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        messageInput.addEventListener('input', () => {
            if (ws && ws.readyState === WebSocket.OPEN) {
                clearTimeout(typingTimeout);
                ws.send(JSON.stringify({
                    type: 'typing',
                    user: nickname,
                    isTyping: true
                }));
                
                typingTimeout = setTimeout(() => {
                    ws.send(JSON.stringify({
                        type: 'typing',
                        user: nickname,
                        isTyping: false
                    }));
                }, 1000);
            }
        });
    }
    
    connectWebSocket();
};

function connectWebSocket() {
    ws = new WebSocket('ws://localhost:8080');

    ws.onopen = () => {
        console.log('Connected to WebSocket server');
        ws.send(JSON.stringify({
            type: 'join',
            user: nickname
        }));
    };

    ws.onerror = (error) => {
        console.error('WebSocket Error:', error);
        setTimeout(connectWebSocket, 3000);
    };

    ws.onmessage = (event) => {
        try {
            const data = JSON.parse(event.data);
            switch(data.type) {
                case 'message':
                    // First save the message to get an ID
                    if (data.user === nickname) {
                        saveMessageToDatabase(data.user, data.message)
                            .then(response => {
                                if (response.success && response.messageId) {
                                    // Now append with the correct ID
                                    appendMessage(data.user, data.message, data.time, response.messageId);
                                }
                            })
                            .catch(error => console.error('Error saving message:', error));
                    } else {
                        // For messages from other users, append directly
                        appendMessage(data.user, data.message, data.time, data.messageId);
                    }
                    break;
                case 'edit':
                    // Update the message in the UI if it exists
                    const editedMessage = document.querySelector(`[data-message-id="${data.messageId}"]`);
                    if (editedMessage) {
                        const messageContent = editedMessage.querySelector('.message-content');
                        if (messageContent) {
                            messageContent.textContent = data.message;
                        }
                    }
                    break;
                case 'delete':
                    // Remove the message from the UI if it exists
                    const deletedMessage = document.querySelector(`[data-message-id="${data.messageId}"]`);
                    if (deletedMessage) {
                        deletedMessage.remove();
                    }
                    break;
                case 'typing':
                    handleTypingIndicator(data.user, data.isTyping);
                    break;
                case 'userList':
                    updateUserList(data.users);
                    break;
                case 'update':
                    const messageElement = document.querySelector(`[data-message-id="${data.messageId}"]`);
                    if (messageElement) {
                        messageElement.querySelector('.message-content').textContent = data.newContent;
                    }
                    break;
            }
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    };

    ws.onclose = () => {
        console.log('Disconnected - attempting reconnect...');
        setTimeout(connectWebSocket, 3000);
    };
}

// Fonction pour enregistrer le message dans la base de données
function saveMessageToDatabase(user, message) {
    return fetch('save_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user=${encodeURIComponent(user)}&message=${encodeURIComponent(message)}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.error || 'Error saving message');
        }
        return data;
    })
    .catch(error => {
        console.error('Error:', error);
        throw error;
    });
}

// Add message sending functionality
function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    if (!messageInput) return;
    
    const message = messageInput.value.trim();
    if (message && ws && ws.readyState === WebSocket.OPEN) {
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        ws.send(JSON.stringify({
            type: 'message',
            user: nickname,
            message: message,
            time: time
        }));
        messageInput.value = '';
        
        // Stop typing indicator
        ws.send(JSON.stringify({
            type: 'typing',
            user: nickname,
            isTyping: false
        }));
    }
}

// Handle typing indicator
function handleTypingIndicator(user, isTyping) {
    if (user !== nickname) {
        if (isTyping) {
            typingIndicator.classList.remove('hidden');
            typingIndicator.innerHTML = `<div class="typing-indicator text-sm text-gray-500">${user} is typing<span></span><span></span><span></span></div>`;
        } else {
            typingIndicator.classList.add('hidden');
        }
    }
}

// Update online users list
function updateUserList(users) {
    onlineUsers.innerHTML = '';
    onlineCount.textContent = `${users.length} users online`;
    
    users.forEach(user => {
        if (user !== nickname) {
            const userElement = document.createElement('div');
            userElement.className = 'flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition';
            userElement.innerHTML = `
                <div class="relative">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-sm font-semibold">${user.substring(0, 2).toUpperCase()}</div>
                    <span class="absolute bottom-0 right-0 w-2 h-2 bg-green-500 rounded-full border-2 border-white online-pulse"></span>
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-sm">${user}</h4>
                    <p class="text-xs text-gray-500">Online</p>
                </div>
            `;
            onlineUsers.appendChild(userElement);
        }
    });
}

// Append message to chat - Updated function
function appendMessage(sender, message, time, messageId, files = []) {
    console.log('Appending message with ID:', messageId);
    const isCurrentUser = sender === nickname;
    const initials = sender.substring(0, 2).toUpperCase();

    const messageElement = document.createElement('div');
    messageElement.className = `flex items-start space-x-3 ${isCurrentUser ? 'justify-end' : ''} message-animation`;
    messageElement.setAttribute('data-message-id', messageId);

    let messageHTML = '';
    if (!isCurrentUser) {
        messageHTML += `
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-sm font-semibold">${initials}</div>
            </div>
        `;
    }

    messageHTML += `
        <div class="flex-1 ${isCurrentUser ? 'flex flex-col items-end' : ''}">
            <div class="relative ${isCurrentUser ? 'bg-indigo-100' : 'bg-white'} p-3 rounded-lg shadow-sm max-w-xs message-container">
                <div class="flex items-baseline space-x-2 ${isCurrentUser ? 'justify-end' : ''}">
                    ${!isCurrentUser ? `<span class="font-medium text-sm">${sender}</span>` : ''}
                    <span class="text-xs text-gray-400">${time}</span>
                    ${isCurrentUser ? '<span class="font-medium text-sm">Me</span>' : ''}
                </div>
                <p class="text-sm mt-1 message-content">${message}</p>
                ${isCurrentUser ? `
                <div class="message-menu absolute right-2 top-2 flex space-x-1">
                    <button class="p-1 text-gray-400 hover:text-indigo-600 rounded-full transition edit-message-btn">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button class="p-1 text-gray-400 hover:text-indigo-600 rounded-full transition delete-message-btn">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
                ` : ''}
            </div>
        </div>
    `;

    if (isCurrentUser) {
        messageHTML += `
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-semibold">ME</div>
            </div>
        `;
    }

    messageElement.innerHTML = messageHTML;
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.appendChild(messageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Attach event listeners for edit and delete buttons
    if (isCurrentUser) {
        const editBtn = messageElement.querySelector('.edit-message-btn');
        const deleteBtn = messageElement.querySelector('.delete-message-btn');

        editBtn?.addEventListener('click', () => editMessage(messageElement));
        deleteBtn?.addEventListener('click', () => deleteMessage(messageElement));
    }
}

function editMessage(messageElement) {
    const messageId = messageElement.getAttribute('data-message-id');
    const messageContent = messageElement.querySelector('.message-content');
    const originalText = messageContent.textContent;
    
    // Replace the text with an input field
    messageContent.innerHTML = `<input type="text" class="w-full p-1 rounded border bg-white text-gray-900" value="${originalText}">`;
    const input = messageContent.querySelector('input');
    input.focus();
    
    function saveEdit() {
        const newText = input.value.trim();
        if (newText && newText !== originalText) {
            fetch('update_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    messageId: messageId,
                    newContent: newText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageContent.textContent = newText;
                    ws.send(JSON.stringify({
                        type: 'edit',
                        messageId: messageId,
                        message: newText
                    }));
                } else {
                    throw new Error(data.error || 'Failed to update message');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageContent.textContent = originalText;
            });
        } else {
            messageContent.textContent = originalText;
        }
    }
    
    input.onblur = saveEdit;
    input.onkeypress = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveEdit();
        }
    };
}

function deleteMessage(messageElement) {
    const messageId = messageElement.getAttribute('data-message-id');
    
    fetch('delete_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ messageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageElement.closest('.flex').remove();
            ws.send(JSON.stringify({
                type: 'delete',
                messageId: messageId
            }));
        } else {
            throw new Error(data.error || 'Failed to delete message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
    </script>
    <style>
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Message bubble animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message-animation {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        /* Online status pulse */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
            100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
        }
        .online-pulse {
            animation: pulse 2s infinite;
        }

        /* Typing indicator */
        @keyframes typing {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #888;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1s infinite;
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        /* Message reactions */
        .reaction {
            position: absolute;
            bottom: -20px;
            right: 0;
            background: white;
            border-radius: 15px;
            padding: 2px 8px;
            font-size: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Message menu */
        .message-menu {
            position: absolute;
            right: 0;
            top: 0;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .message-container:hover .message-menu {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-indigo-600 text-white">
            <div class="flex items-center space-x-3">
                <i class="fas fa-users text-xl"></i>
                <h1 class="text-xl font-bold">Group Connect</h1>
            </div>
            <button id="newGroupBtn" class="p-2 rounded-full hover:bg-indigo-700 transition">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        
        <!-- Search -->
        <div class="p-3 border-b border-gray-200">
        <div class="flex space-x-2">
       <button
        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
        onclick="window.location.href='chat_group.php?group=niveau1';"
    >
        Group Niveau 1
    </button>
    <button
    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
    onclick="window.location.href='private/index.php';"
>
    Encadrant 1
</button>
</div>
        </div>
        
        <!-- User Profile -->
        <div class="p-3 border-b border-gray-200 flex items-center space-x-3 bg-gray-50">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold" id="userInitials">ME</div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white online-pulse"></span>
            </div>
            <div class="flex-1">
                <h4 class="font-medium" id="userName">My Profile</h4>
                <p class="text-xs text-gray-500">Online</p>
            </div>
            <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-cog"></i>
            </button>
        </div>

        <!-- Online Users -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-3">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Online Now</h3>
            <div id="onlineUsers" class="space-y-2">
                <!-- Online users will be added here dynamically -->
            </div>
        </div>
    </div>
    
    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Group/Prof Selection -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">GC</div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white online-pulse"></span>
                </div>
                <div>
                    <h2 class="font-bold">Global Chat</h2>
                    <p class="text-xs text-gray-500" id="onlineCount">0 users online</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition">
                    <i class="fas fa-search"></i>
                </button>
                <button class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
        
        <!-- Messages -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50 custom-scrollbar space-y-4">
          <?php include 'view_messages.php';
          ?>
            <!-- Messages will be appended here dynamically -->
        </div>
        
        <!-- Typing Indicator -->
        
        
        <!-- Message Input -->
        <div class="p-3 border-t border-gray-200 bg-white">
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-500 hover:text-indigo-600 rounded-full transition">
                    <i class="fas fa-paperclip"></i>
                </button>
                <button class="p-2 text-gray-500 hover:text-indigo-600 rounded-full transition">
                    <i class="fas fa-image"></i>
                </button>
                <div class="flex-1 relative">
                    <input type="text" id="messageInput" placeholder="Type a message..." 
                           class="w-full pl-4 pr-10 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <button class="absolute right-2 top-2 text-gray-400 hover:text-indigo-600">
                        <i class="far fa-smile"></i>
                    </button>
                </div>
                <button id="sendMessageBtn"  class="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Emoji Picker Modal -->
    

    <!-- File Upload Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" id="fileModal">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-bold text-lg">Upload File</h3>
                <button id="closeFileModal" class="p-1 text-gray-500 hover:text-gray-700 rounded-full transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <form id="fileUploadForm" class="space-y-4">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-500">Drag and drop files here or click to browse</p>
                        <input type="file" id="fileInput" class="hidden" multiple>
                        <button type="button" onclick="document.getElementById('fileInput').click()" 
                                class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                            Browse Files
                        </button>
                    </div>
                    <div id="selectedFiles" class="space-y-2">
                        <!-- Selected files will be shown here -->
                    </div>
                    <div id="uploadProgress" class="hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="progressText" class="text-sm text-gray-500 mt-1">Uploading...</p>
                    </div>
                </form>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end">
                <button id="uploadFilesBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                    Upload
                </button>
            </div>
        </div>
    </div>
</body>
</html>
