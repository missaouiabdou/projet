document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');
    const ws = new WebSocket('ws://localhost:8080');

    // Configuration utilisateur (à adapter selon l'authentification)
    var currentUser = {
        ID_ENC: null, // Remplacer par la valeur réelle si encadrant
        ID_STG: 123  // Remplacer par l'ID réel de l'étudiant
    };

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    ws.onmessage = function(event) {
        const packet = JSON.parse(event.data);
        const message = packet.data;
        
        // Déterminer le type d'utilisateur
        const isStudent = message.ID_STG !== null;
        const isCurrentUser = isStudent ? 
            (message.ID_STG === currentUser.ID_STG) : 
            (message.ID_ENC === currentUser.ID_ENC);

        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-end ${isCurrentUser ? 'justify-end' : 'justify-start'} mb-4`;

        messageDiv.innerHTML = `
            <div class="max-w-md">
                <div class="${isCurrentUser ? 'bg-blue-500 text-white' : 'bg-gray-200'} rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold">
                            ${isStudent ? `Étudiant #${message.ID_STG}` : `Encadrant #${message.ID_ENC}`}
                        </span>
                        <span class="text-xs ${isCurrentUser ? 'text-blue-100' : 'text-gray-500'}">
                            ${formatDate(message.DATE_MSG)}
                        </span>
                    </div>
                    <div class="break-words">${message.CONT_MSG}</div>
                </div>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    function sendMessage() {
        const content = messageInput.value.trim();
        
        if (content) {
            const messageData = {
                CONT_MSG: content,
                ID_STG: currentUser.ID_STG,
                ID_ENC: currentUser.ID_ENC
            };

            ws.send(JSON.stringify(messageData));
            messageInput.value = '';
        }
    }

    // Gestion des événements
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    document.querySelector('button').addEventListener('click', (e) => {
        e.preventDefault();
        sendMessage();
    });
});