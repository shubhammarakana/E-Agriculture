<!-- Footer -->
<?php
$current_uri = $_SERVER['REQUEST_URI'];
$is_admin_or_vendor = (strpos($current_uri, '/admin/') !== false || strpos($current_uri, '/vendor/') !== false);
if (!$is_admin_or_vendor):
    ?>
    <footer id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3>Agri<span class="highlight">AI</span></h3>
                    <p>Building a sustainable future for farmers through technology and fair trade.</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>about.php">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>how_it_works.php">How It Works</a></li>
                        <li><a href="<?php echo BASE_URL; ?>sitemap.php">Site Map</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Contact</h4>
                    <ul>
                        <li><a href="#"><i class="fa-solid fa-envelope"></i> support@agriai.com</a></li>
                        <li><a href="#"><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</a></li>
                        <li><a href="#"><i class="fa-solid fa-location-dot"></i> Tech Valley, CA</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date("Y"); ?> AI-Powered Agriculture E-Marketplace. All rights reserved.</p>
            </div>
        </div>
    </footer>
<?php endif; ?>



<script>
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your session.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Logout'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo BASE_URL; ?>logout.php';
            }
        })
    }
</script>
<!-- Chatbot Widget -->
<div id="agri-chatbot-widget" class="chatbot-widget">
    <button id="chatbot-toggle-btn" class="chatbot-toggle-btn" onclick="toggleChatbot()">
        <i class="fas fa-robot"></i>
    </button>
    <div id="chatbot-window" class="chatbot-window hidden">
        <div class="chatbot-header">
            <h4>AgriAI Support</h4>
            <button onclick="toggleChatbot()" class="chatbot-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chat-message bot-message">
                Hello! I'm AgriAI Support. How can I help you today?
            </div>
        </div>
        <div class="chatbot-input-area">
            <input type="text" id="chatbot-input" placeholder="Type a message..."
                onkeypress="handleChatbotKeyPress(event)">
            <button onclick="sendChatMessage()" class="chatbot-send-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
    /* Chatbot Widget Styles */
    .chatbot-widget {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    .chatbot-toggle-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #16a34a;
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4);
        font-size: 24px;
        cursor: pointer;
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chatbot-toggle-btn:hover {
        transform: scale(1.1);
    }

    .chatbot-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: opacity 0.3s ease, transform 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .chatbot-window.hidden {
        opacity: 0;
        pointer-events: none;
        transform: translateY(20px);
    }

    .chatbot-header {
        background: #16a34a;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .chatbot-close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    .chatbot-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f9fafb;
    }

    .chat-message {
        padding: 10px 14px;
        border-radius: 12px;
        max-width: 80%;
        font-size: 14px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .bot-message {
        background: white;
        color: #374151;
        align-self: flex-start;
        border: 1px solid #e5e7eb;
        border-bottom-left-radius: 4px;
    }

    .user-message {
        background: #16a34a;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    .chatbot-input-area {
        padding: 15px;
        display: flex;
        gap: 10px;
        border-top: 1px solid #e5e7eb;
        background: white;
    }

    #chatbot-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 20px;
        outline: none;
        font-size: 14px;
        font-family: inherit;
    }

    #chatbot-input:focus {
        border-color: #16a34a;
    }

    .chatbot-send-btn {
        background: #16a34a;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .chatbot-send-btn:hover {
        background: #15803d;
    }

    .typing-indicator {
        padding: 12px 14px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        border-bottom-left-radius: 4px;
        align-self: flex-start;
        display: flex;
        gap: 4px;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background: #9ca3af;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) {
        animation-delay: -0.32s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(0);
        }

        40% {
            transform: scale(1);
        }
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .chatbot-window {
            width: calc(100vw - 40px);
            right: -10px;
            bottom: 70px;
        }
    }
</style>

<script>
    const sessionId = 'session_' + Math.random().toString(36).substring(2, 10);

    function toggleChatbot() {
        const window = document.getElementById('chatbot-window');
        window.classList.toggle('hidden');
        if (!window.classList.contains('hidden')) {
            document.getElementById('chatbot-input').focus();
        }
    }

    function handleChatbotKeyPress(event) {
        if (event.key === 'Enter') {
            sendChatMessage();
        }
    }

    function addMessage(text, isUser = false) {
        const messagesDiv = document.getElementById('chatbot-messages');
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${isUser ? 'user-message' : 'bot-message'}`;

        // Convert Markdown-like text or line breaks
        text = text.replace(/\n/g, '<br>');
        msgDiv.innerHTML = text;

        messagesDiv.appendChild(msgDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function showTypingIndicator() {
        const messagesDiv = document.getElementById('chatbot-messages');
        const indicator = document.createElement('div');
        indicator.id = 'typing-indicator';
        indicator.className = 'typing-indicator';
        indicator.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
        messagesDiv.appendChild(indicator);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    async function sendChatMessage() {
        const inputField = document.getElementById('chatbot-input');
        const message = inputField.value.trim();

        if (!message) return;

        // Add user message to UI
        addMessage(message, true);
        inputField.value = '';

        // Show typing indicator
        showTypingIndicator();

        try {
            // Call PHP backend because Python is not installed
            const response = await fetch('<?php echo BASE_URL; ?>chatbot_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    session_id: sessionId
                })
            });

            removeTypingIndicator();

            if (response.ok) {
                const data = await response.json();
                addMessage(data.response || 'Sorry, I received an empty response.');
            } else {
                addMessage('Sorry, I encountered an error connecting to the server. Please try again later.');
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            removeTypingIndicator();
            addMessage('Sorry, I could not connect to the server. Please check your internet connection and try again.');
        }
    }
</script>
</body>

</html>