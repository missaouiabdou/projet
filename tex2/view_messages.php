<?php
            try {
                // Use the connection established in connection.php
                // Assuming connection.php defines $conn or $pdo
                require_once '../php/connection.php'; // Adjust path if needed
                // Use $conn if defined in connection.php, otherwise fallback to creating a new $pdo
                $db = $conn ?? $pdo ?? null;
                if (!$db) {
                    throw new Exception('Database connection not established.');
                }


                // Simplified query to fetch messages and sender details
                $stmt = $db->query("
                    SELECT m.ID_MSG, m.CONT_MSG AS message, m.DATE_MSG AS sent_at,
                           COALESCE(e.NOM_ENC, s.NOM_STG) AS sender_name,
                           CASE WHEN m.ID_ENC IS NOT NULL THEN 'encadrant' ELSE 'stagiaire' END AS sender_type
                    FROM messagerie m
                    LEFT JOIN encadrant e ON m.ID_ENC = e.ID_ENC
                    LEFT JOIN stagiaire s ON m.ID_STG = s.ID_STG
                    WHERE m.ID_ENC IS NOT NULL OR m.ID_STG IS NOT NULL -- Ensure message has a sender
                    ORDER BY sent_at ASC
                ");

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // Use FETCH_ASSOC
                    // Check if sender_name exists and is not null before comparison
                    $senderName = $row['sender_name'] ?? 'Unknown';
                    $sessionNom = $_SESSION['nom'] ?? '';
                    $isCurrentUser = ($senderName !== 'Unknown' && $sessionNom !== '' && $senderName === $sessionNom);

                    $initials = strtoupper(substr($senderName, 0, 2));
                    $time = date('g:i A', strtotime($row['sent_at'])); // Format time (e.g., 3:15 PM)
                    $messageId = $row['ID_MSG']; // Get the message ID

                    // Add bottom margin for spacing between messages
                    echo '<div class="flex items-start space-x-3 mb-4 ' . ($isCurrentUser ? 'justify-end' : '') . ' message-animation" data-message-id="' . $messageId . '">';

                    // Avatar for other users
                    if (!$isCurrentUser) {
                        echo '<div class="flex-shrink-0">';
                        // Slightly larger, different background/text color for avatar
                        echo '<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm font-medium">' . htmlspecialchars($initials) . '</div>';
                        echo '</div>';
                    }

                    // Message Bubble Container
                    echo '<div class="flex-1 ' . ($isCurrentUser ? 'flex flex-col items-end' : '') . '">';
                    // Message Bubble Styling: Different background/text for current user, more rounding
                    $bubbleClasses = $isCurrentUser
                        ? 'bg-blue-500 text-white' // User's messages
                        : 'bg-gray-100 text-gray-800'; // Others' messages
                    echo '<div class="relative ' . $bubbleClasses . ' p-3 rounded-xl shadow-md max-w-md message-container">'; // Increased max-width slightly

                    // Message Header (Name and Time)
                    echo '<div class="flex items-baseline space-x-2 mb-1 ' . ($isCurrentUser ? 'justify-end' : '') . '">';
                    if (!$isCurrentUser) {
                        // Bolder name for others
                        echo '<span class="font-semibold text-sm">' . htmlspecialchars($senderName) . '</span>';
                    }
                    // Consistent time display
                    echo '<span class="text-xs ' . ($isCurrentUser ? 'text-blue-100' : 'text-gray-400') . '">' . $time . '</span>';
                    if ($isCurrentUser) {
                         // No need to explicitly say "Me" if the bubble style indicates it
                         // echo '<span class="font-medium text-sm">Me</span>';
                    }
                    echo '</div>';

                    // Message Content
                    echo '<p class="text-sm message-content">' . nl2br(htmlspecialchars($row['message'])) . '</p>'; // Use nl2br to respect newlines

                    // Edit/Delete Menu for Current User
                    if ($isCurrentUser) {
                        // Improved styling for menu buttons
                        echo '<div class="message-menu absolute -left-8 top-1/2 transform -translate-y-1/2 flex flex-col space-y-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">'; // Position left, vertical, appears on hover
                        echo '<button class="p-1 text-gray-500 hover:text-blue-600 bg-white rounded-full shadow transition edit-message-btn">';
                        echo '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM4 14a1 1 0 100 2h12a1 1 0 100-2H4z"></path></svg>'; // Edit Icon
                        echo '</button>';
                        echo '<button class="p-1 text-gray-500 hover:text-red-600 bg-white rounded-full shadow transition delete-message-btn">';
                        echo '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'; // Trash Icon
                        echo '</button>';
                        echo '</div>';
                        // Add group class to parent for hover effect
                        echo '<script>document.currentScript.closest(".message-animation").classList.add("group");</script>';
                    }

                    echo '</div>'; // Close message-container div
                    echo '</div>'; // Close flex-1 div

                    // Avatar for current user (Optional, as bubble color indicates sender)
                    // if ($isCurrentUser) {
                    //     echo '<div class="flex-shrink-0">';
                    //     echo '<div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-semibold">ME</div>';
                    //     echo '</div>';
                    // }

                    echo '</div>'; // Close main message div (message-animation)
                }
            } catch(PDOException $e) {
                // Improved error display
                echo '<div class="text-red-500 p-4">Error fetching messages: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        ?>