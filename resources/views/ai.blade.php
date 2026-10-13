<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eva AI Assistant</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .typing-dot {
            animation: typing 1.4s infinite ease-in-out both;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-950 text-white font-sans antialiased h-screen flex flex-col overflow-hidden selection:bg-cyan-500 selection:text-white">

    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-800 bg-gray-900/50 backdrop-blur-md sticky top-0 z-10">
        <div class="flex items-center space-x-3">
            <div class="relative w-10 h-10 rounded-full bg-cyan-900/30 flex items-center justify-center border border-cyan-500/30 shadow-[0_0_15px_rgba(6,182,212,0.3)]">
                <div class="w-3 h-3 bg-cyan-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.8)]"></div>
            </div>
            <div>
                <h1 class="text-lg font-semibold tracking-wide text-gray-100">EVA <span class="text-cyan-400">AI</span></h1>
                <p class="text-xs text-gray-400">System Online</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <span class="px-2 py-1 text-xs rounded bg-gray-800 border border-gray-700 text-gray-400">v1.0.0</span>
        </div>
    </header>

    <!-- Chat Area -->
    <main class="flex-1 overflow-y-auto p-6 scroll-smooth" id="chat-container">
        <div class="max-w-3xl mx-auto space-y-6" id="messages">
            <!-- System Welcome -->
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full bg-cyan-900/50 flex-shrink-0 flex items-center justify-center border border-cyan-500/20 mt-1">
                    <span class="text-cyan-400 text-xs font-bold">AI</span>
                </div>
                <div class="bg-gray-900/80 border border-gray-800 rounded-2xl p-4 max-w-[85%] shadow-lg">
                    <p class="text-gray-300 leading-relaxed">Hello! I am Eva. I'm online and ready to assist you. You can speak to me or type here.</p>
                </div>
            </div>
        </div>
        <!-- Typing Indicator (Hidden by default) -->
        <div id="typing-indicator" class="hidden max-w-3xl mx-auto mt-4 px-14">
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-cyan-500 rounded-full typing-dot"></div>
                <div class="w-2 h-2 bg-cyan-500 rounded-full typing-dot"></div>
                <div class="w-2 h-2 bg-cyan-500 rounded-full typing-dot"></div>
            </div>
        </div>
        <div class="h-4"></div>
    </main>

    <!-- Input Area -->
    <footer class="p-6 bg-gradient-to-t from-gray-950 via-gray-950 to-transparent">
        <div class="max-w-3xl mx-auto">
            <form id="chat-form" class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-2xl opacity-20 group-hover:opacity-30 transition blur"></div>
                <div class="relative flex items-end gap-2 bg-gray-900 rounded-xl p-2 border border-gray-800 focus-within:border-cyan-500/50 focus-within:ring-1 focus-within:ring-cyan-900 transition-all shadow-xl">
                    <img src="https://ui-avatars.com/api/?name=User&background=374151&color=fff" class="w-8 h-8 rounded-full mb-1 ml-1 opacity-50" alt="User">
                    <textarea 
                        id="user-input"
                        rows="1" 
                        class="w-full bg-transparent text-gray-100 placeholder-gray-500 border-none focus:ring-0 resize-none py-3 max-h-32 scrollbar-hide"
                        placeholder="Message Eva..." 
                        required
                    ></textarea>
                    <button type="submit" class="p-2 mb-1 rounded-lg bg-cyan-600 text-white hover:bg-cyan-500 transition-colors shadow-lg disabled:opacity-50 disabled:cursor-not-allowed" id="send-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </div>
            </form>
            <p class="text-center text-gray-600 text-xs mt-3">AI controls your PC. Use with caution.</p>
        </div>
    </footer>

    <script>
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const messagesContainer = document.getElementById('messages');
        const mainContainer = document.getElementById('chat-container');
        const typingIndicator = document.getElementById('typing-indicator');
        const sendBtn = document.getElementById('send-btn');

        // Auto-resize textarea
        userInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if(this.value === '') this.style.height = 'auto';
        });

        // Submit on Enter (without Shift)
        userInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        function appendMessage(text, sender) {
            const div = document.createElement('div');
            div.className = 'flex gap-4 ' + (sender === 'user' ? 'flex-row-reverse' : '');
            
            const avatar = document.createElement('div');
            avatar.className = `w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center mt-1 ` + 
                (sender === 'user' ? 'bg-gray-700' : 'bg-cyan-900/50 border border-cyan-500/20');
            
            if (sender === 'eva') {
                avatar.innerHTML = '<span class="text-cyan-400 text-xs font-bold">AI</span>';
            } else {
                avatar.innerHTML = '<span class="text-gray-300 text-xs font-bold">ME</span>';
            }

            const bubble = document.createElement('div');
            bubble.className = `max-w-[85%] rounded-2xl p-4 shadow-md ` + 
                (sender === 'user' ? 'bg-gray-800 text-white border border-gray-700' : 'bg-gray-900/80 text-gray-300 border border-gray-800');
            
            // Format line breaks
            bubble.innerHTML = text.replace(/\n/g, '<br>');

            div.appendChild(avatar);
            div.appendChild(bubble);
            messagesContainer.appendChild(div);
            
            // Scroll to bottom
            mainContainer.scrollTo({ top: mainContainer.scrollHeight, behavior: 'smooth' });
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = userInput.value.trim();
            if (!text) return;

            // UI Updates
            appendMessage(text, 'user');
            userInput.value = '';
            userInput.style.height = 'auto';
            typingIndicator.classList.remove('hidden');
            mainContainer.scrollTo({ top: mainContainer.scrollHeight, behavior: 'smooth' });
            
            try {
                // Send to Python Flask Server with 3-minute timeout
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 180000); // 3 min timeout

                let response;
                let retries = 2;
                
                for (let attempt = 1; attempt <= retries; attempt++) {
                    try {
                        response = await fetch('http://localhost:5000/chat', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: text }),
                            signal: controller.signal
                        });
                        break; // Success, exit retry loop
                    } catch (fetchErr) {
                        if (attempt === retries) throw fetchErr;
                        console.log(`Retry ${attempt}/${retries}...`);
                        await new Promise(r => setTimeout(r, 2000));
                    }
                }

                clearTimeout(timeoutId);
                const data = await response.json();
                
                typingIndicator.classList.add('hidden');
                
                if (data.response) {
                    appendMessage(data.response, 'eva');
                } else {
                    appendMessage("Error: No response from Eva.", 'eva');
                }

            } catch (error) {
                typingIndicator.classList.add('hidden');
                if (error.name === 'AbortError') {
                    appendMessage("Response took too long. Try a shorter question.", 'eva');
                } else {
                    appendMessage("Connection failed. Is the assistant running?", 'eva');
                }
                console.error(error);
            }
        });
    </script>
</body>
</html>
