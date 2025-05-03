<?php
session_start();
require_once "../php/connection.php";

// Example: Only users with ID_NIV = 1 can access this group
if (!isset($_SESSION['id_niv']) || $_SESSION['id_niv'] != 1) {
    // Not authorized
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Access Denied</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded shadow text-center">
            <h1 class="text-2xl font-bold text-red-600 mb-4">Access Denied</h1>
            <p class="text-gray-700">You do not have permission to access this group chat.</p>
            <a href="index.php" class="mt-4 inline-block text-indigo-600 hover:underline">Back to Home</a>
        </div>
    </body>
    </html>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Chat - Niveau 1</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <?php
    // Fetch messages associated with stagiaires having ID_NIV = 1
    // Also fetch sender details for display
    $sql = "
        SELECT
            m.ID_MSG,
            m.CONT_MSG AS message, -- Renamed CONT_MSG to message for consistency
            m.DATE_MSG AS sent_at, -- Renamed DATE_MSG to sent_at for consistency
            COALESCE(e.NOM_ENC, s.NOM_STG) AS sender_name,
            CASE WHEN m.ID_ENC IS NOT NULL THEN 'encadrant' ELSE 'stagiaire' END AS sender_type,
            s.ID_NIV -- Include ID_NIV if needed elsewhere, otherwise optional
        FROM
            messagerie m
        LEFT JOIN
            encadrant e ON m.ID_ENC = e.ID_ENC
        LEFT JOIN
            stagiaire s ON m.ID_STG = s.ID_STG
        WHERE
            s.ID_NIV=1 -- Show messages associated with stagiaires where ID_NIV is 1
        ORDER BY
            m.DATE_MSG ASC -- Use m.DATE_MSG or the alias sent_at
    ";

    // Prepare and execute the modified query
    $stmt = $conn->prepare($sql);
    $stmt->execute(); // Execute without parameters
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
    ?>
    <div class="max-w-2xl mx-auto w-full flex flex-col h-screen">
        <!-- Header -->
        <header class="bg-indigo-600 text-white px-6 py-4 rounded-b-lg shadow flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i class="fas fa-users text-2xl"></i>
                <h1 class="text-xl font-bold">Group Chat - Niveau 1</h1>
            </div>
            <a href="index.php" class="text-indigo-200 hover:text-white transition">Back</a>
        </header>

        <!-- Messages Area -->
        <main class="flex-1 overflow-y-auto px-4 py-6 space-y-4 bg-white rounded-lg shadow mt-4" id="messagesContainer">
            <?php foreach ($messages as $row): ?>
                <?php
                    $senderName = $row['sender_name'] ?? 'Unknown';
                    $sessionNom = $_SESSION['nom'] ?? '';
                    $isCurrentUser = ($senderName !== 'Unknown' && $sessionNom !== '' && $senderName === $sessionNom);
                    $initials = strtoupper(substr($senderName, 0, 2));
                    $time = date('g:i A', strtotime($row['sent_at']));
                    $messageId = $row['ID_MSG'];
                ?>
                <div class="flex <?= $isCurrentUser ? 'justify-end' : 'justify-start' ?>">
                    <?php if (!$isCurrentUser): ?>
                        <div class="flex-shrink-0 mr-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-lg font-bold"><?= htmlspecialchars($initials) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="max-w-xs <?= $isCurrentUser ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-900' ?> rounded-xl p-4 shadow relative group" data-message-id="<?= $messageId ?>">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold"><?= htmlspecialchars($senderName) ?></span>
                            <span class="text-xs <?= $isCurrentUser ? 'text-indigo-200' : 'text-gray-400' ?>"><?= $time ?></span>
                        </div>
                        <p class="text-sm message-content"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                        <?php if ($isCurrentUser): ?>
                            <div class="absolute -right-10 top-1/2 transform -translate-y-1/2 flex flex-col space-y-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button class="p-1 text-gray-500 hover:text-indigo-600 bg-white rounded-full shadow transition edit-message-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="p-1 text-gray-500 hover:text-red-600 bg-white rounded-full shadow transition delete-message-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($isCurrentUser): ?>
                        <div class="flex-shrink-0 ml-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg font-bold"><?= htmlspecialchars($initials) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (empty($messages)): ?>
                <p class="text-center text-gray-400 mt-8">No messages found for this group.</p>
            <?php endif; ?>
        </main>

        <!-- Message Input -->
        <form class="flex items-center px-4 py-3 bg-white rounded-b-lg shadow mt-2" method="post" action="save_message.php">
            <input type="hidden" name="group" value="niveau1">
            <input
                id="messageInput"
                name="message"
                type="text"
                placeholder="Type your message..."
                class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                autocomplete="off"
                required
            >
            <button
                type="submit"
                class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
                id="sendMessageBtn"
            >
                Send
            </button>
        </form>
        <div id="typingIndicator" class="text-sm text-gray-500 mt-2"></div>
    </div>
    <!-- FontAwesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
let ws;
let typingTimeout;
let nickname = '<?php echo isset($_SESSION["nom"]) ? $_SESSION["nom"] : ""; ?>';

function appendMessage(user, message, time, messageId, isCurrentUser = false) {
    const messagesContainer = document.getElementById('messagesContainer');
    const initials = user.substring(0, 2).toUpperCase();
    
    const div = document.createElement('div');
    div.className = `flex ${isCurrentUser ? 'justify-end' : 'justify-start'}`;
    
    const html = `
        ${!isCurrentUser ? `
            <div class="flex-shrink-0 mr-2">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-lg font-bold">${initials}</div>
            </div>
        ` : ''}
        <div class="max-w-xs ${isCurrentUser ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-900'} rounded-xl p-4 shadow relative group" data-message-id="${messageId}">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold">${user}</span>
                <span class="text-xs ${isCurrentUser ? 'text-indigo-200' : 'text-gray-400'}">${time}</span>
            </div>
            <p class="text-sm message-content">${message}</p>
            ${isCurrentUser ? `
                <div class="absolute -right-10 top-1/2 transform -translate-y-1/2 flex flex-col space-y-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <button class="p-1 text-gray-500 hover:text-indigo-600 bg-white rounded-full shadow transition edit-message-btn" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="p-1 text-gray-500 hover:text-red-600 bg-white rounded-full shadow transition delete-message-btn" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            ` : ''}
        </div>
        ${isCurrentUser ? `
            <div class="flex-shrink-0 ml-2">
                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg font-bold">${initials}</div>
            </div>
        ` : ''}
    `;
    
    div.innerHTML = html;
    messagesContainer.appendChild(div);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Add event listeners for edit and delete buttons if it's current user's message
    if (isCurrentUser) {
        const messageElement = div.querySelector('[data-message-id]');
        const editBtn = messageElement.querySelector('.edit-message-btn');
        const deleteBtn = messageElement.querySelector('.delete-message-btn');
        
        if (editBtn) editBtn.onclick = () => editMessage(messageElement);
        if (deleteBtn) deleteBtn.onclick = () => deleteMessage(messageElement);
    }
}

function saveMessageToDatabase(user, message) {
    return fetch('save_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user=${encodeURIComponent(user)}&message=${encodeURIComponent(message)}&group=niveau1`
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

// Initialize WebSocket connection
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
                                    const time = new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                                    appendMessage(data.user, data.message, time, response.messageId, true);
                                }
                            })
                            .catch(error => console.error('Error saving message:', error));
                    } else {
                        // For messages from other users, append directly
                        const time = new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                        appendMessage(data.user, data.message, time, data.messageId, false);
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
                        deletedMessage.closest('.flex').remove();
                    }
                    break;
                case 'typing':
                    if (data.user !== nickname) {
                        const typingIndicator = document.getElementById('typingIndicator');
                        typingIndicator.textContent = data.isTyping ? `${data.user} is typing...` : '';
                    }
                    break;
            }
        } catch (error) {
            console.error('Error processing message:', error);
        }
    };
}

// Initialize when page loads
window.onload = () => {
    connectWebSocket();
    
    // Set up message input handler
    const messageInput = document.getElementById('messageInput');
    const sendMessageBtn = document.getElementById('sendMessageBtn');
    
    if (messageInput && sendMessageBtn) {
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
        
        sendMessageBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sendMessage();
        });
    }
};

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (message && ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'message',
            user: nickname,
            message: message
        }));
        messageInput.value = '';
    }
}
    </script>
</body>
</html>