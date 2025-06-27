<?php
session_start();
require "../backend/ligabd.php";

// Verificar se o utilizador está autenticado
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$currentUserId = $_SESSION["id"];

// Buscar conversas do utilizador
$sqlConversas = "SELECT c.id, c.utilizador1_id, c.utilizador2_id, c.ultima_atividade,
                        u1.nick as nick1, u1.nome_completo as nome1, p1.foto_perfil as foto1,
                        u2.nick as nick2, u2.nome_completo as nome2, p2.foto_perfil as foto2,
                        (SELECT conteudo FROM mensagens WHERE conversa_id = c.id ORDER BY data_envio DESC LIMIT 1) as ultima_mensagem,
                        (SELECT COUNT(*) FROM mensagens WHERE conversa_id = c.id AND remetente_id != $currentUserId AND lida = 0) as mensagens_nao_lidas
                 FROM conversas c
                 JOIN utilizadores u1 ON c.utilizador1_id = u1.id
                 JOIN utilizadores u2 ON c.utilizador2_id = u2.id
                 LEFT JOIN perfis p1 ON u1.id = p1.id_utilizador
                 LEFT JOIN perfis p2 ON u2.id = p2.id_utilizador
                 WHERE c.utilizador1_id = $currentUserId OR c.utilizador2_id = $currentUserId
                 ORDER BY c.ultima_atividade DESC";

$resultConversas = mysqli_query($con, $sqlConversas);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Orange</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/style_mensagens.css">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon_orange.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Corrigir posição da lupa */
        .search-users {
            position: relative;
        }

        .search-users::before {
            content: "\f002";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 2;
            pointer-events: none;
        }

        .search-users input {
            width: 100%;
            padding: var(--space-md) var(--space-md) var(--space-md) 45px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            background: var(--bg-input);
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: var(--space-md);
            transition: border-color 0.2s ease;
        }

        .search-users input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(255, 87, 34, 0.2);
        }

        /* Melhorar aparência das mensagens */
        .message {
            margin-bottom: var(--space-md);
            animation: fadeInMessage 0.3s ease;
        }

        @keyframes fadeInMessage {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Indicador de digitação */
        .typing-indicator {
            display: none;
            padding: var(--space-sm);
            color: var(--text-muted);
            font-style: italic;
            font-size: 0.9rem;
        }

        /* Status de conexão */
        .connection-status {
            position: fixed;
            top: 70px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1000;
            transition: all 0.3s ease;
            display: none;
        }

        .connection-status.online {
            background: #10b981;
            color: white;
        }

        .connection-status.offline {
            background: #ef4444;
            color: white;
        }

        /* Melhorar scroll das mensagens */
        .messages-container {
            scroll-behavior: smooth;
        }

        /* Indicador de mensagem não lida */
        .message.unread {
            background: rgba(255, 87, 34, 0.05);
            border-left: 3px solid var(--color-primary);
            padding-left: calc(var(--space-md) - 3px);
        }
    </style>
</head>

<body>
    <?php require "parciais/header.php" ?>

    <!-- Status de conexão -->
    <div id="connectionStatus" class="connection-status">
        <i class="fas fa-wifi"></i> Conectado
    </div>

    <div class="container">
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a></li>
                    <li><a href="#"><i class="fas fa-briefcase"></i> <span>Trabalho</span></a></li>
                    <li><a href="mensagens.php" class="active"><i class="fas fa-comments"></i> <span>Mensagens</span></a></li>
                    <li><a href="#"><i class="fas fa-bell"></i> <span>Notificações</span></a></li>
                    <li><a href="#"><i class="fas fa-network-wired"></i> <span>Conexões</span></a></li>
                    <li><a href="itens_salvos.php"><i class="fas fa-bookmark"></i> <span>Itens Salvos</span></a></li>
                    <li><a href="pesquisar.php"><i class="fas fa-search"></i> <span>Pesquisar</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="messages-container">
            <div class="messages-layout">
                <!-- Lista de Conversas -->
                <div class="conversations-list">
                    <div class="conversations-header">
                        <h2><i class="fas fa-comments"></i> Mensagens</h2>
                        <button class="new-message-btn" onclick="openNewMessageModal()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="conversations" id="conversationsList">
                        <?php if (mysqli_num_rows($resultConversas) > 0): ?>
                            <?php while ($conversa = mysqli_fetch_assoc($resultConversas)): ?>
                                <?php
                                // Determinar qual é o outro utilizador
                                $outroUtilizador = ($conversa['utilizador1_id'] == $currentUserId) ? 
                                    ['id' => $conversa['utilizador2_id'], 'nick' => $conversa['nick2'], 'nome' => $conversa['nome2'], 'foto' => $conversa['foto2']] :
                                    ['id' => $conversa['utilizador1_id'], 'nick' => $conversa['nick1'], 'nome' => $conversa['nome1'], 'foto' => $conversa['foto1']];
                                ?>
                                <div class="conversation-item" data-conversation-id="<?php echo $conversa['id']; ?>" onclick="openConversation(<?php echo $conversa['id']; ?>, <?php echo $outroUtilizador['id']; ?>)">
                                    <img src="images/perfil/<?php echo $outroUtilizador['foto'] ?: 'default-profile.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($outroUtilizador['nome']); ?>" class="conversation-avatar">
                                    <div class="conversation-info">
                                        <div class="conversation-header">
                                            <h4><?php echo htmlspecialchars($outroUtilizador['nome']); ?></h4>
                                            <span class="conversation-time">
                                                <?php echo date('H:i', strtotime($conversa['ultima_atividade'])); ?>
                                            </span>
                                        </div>
                                        <p class="last-message">
                                            <?php echo htmlspecialchars(substr($conversa['ultima_mensagem'] ?: 'Iniciar conversa...', 0, 50)); ?>
                                            <?php if (strlen($conversa['ultima_mensagem']) > 50) echo '...'; ?>
                                        </p>
                                    </div>
                                    <?php if ($conversa['mensagens_nao_lidas'] > 0): ?>
                                        <div class="unread-badge"><?php echo $conversa['mensagens_nao_lidas']; ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-conversations">
                                <i class="fas fa-comments"></i>
                                <h3>Nenhuma conversa ainda</h3>
                                <p>Comece uma nova conversa com alguém!</p>
                                <button class="start-conversation-btn" onclick="openNewMessageModal()">
                                    Iniciar Conversa
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Área de Chat -->
                <div class="chat-area" id="chatArea">
                    <div class="no-chat-selected">
                        <i class="fas fa-comments"></i>
                        <h3>Selecione uma conversa</h3>
                        <p>Escolha uma conversa da lista para começar a enviar mensagens</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Nova Mensagem -->
    <div id="newMessageModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nova Mensagem</h3>
                <button class="close-btn" onclick="closeNewMessageModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="search-users">
                    <input type="text" id="userSearch" placeholder="Pesquisar utilizadores..." onkeyup="searchUsers()">
                    <div id="userResults" class="user-results"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentConversationId = null;
        let currentOtherUserId = null;
        let messagePolling = null;
        let lastMessageCount = 0;
        let isTyping = false;
        let typingTimeout = null;
        let connectionStatus = 'online';

        // Verificar conexão
        function checkConnection() {
            const statusEl = document.getElementById('connectionStatus');
            
            if (navigator.onLine) {
                if (connectionStatus !== 'online') {
                    connectionStatus = 'online';
                    statusEl.className = 'connection-status online';
                    statusEl.innerHTML = '<i class="fas fa-wifi"></i> Conectado';
                    statusEl.style.display = 'block';
                    setTimeout(() => statusEl.style.display = 'none', 2000);
                }
            } else {
                connectionStatus = 'offline';
                statusEl.className = 'connection-status offline';
                statusEl.innerHTML = '<i class="fas fa-wifi-slash"></i> Sem conexão';
                statusEl.style.display = 'block';
            }
        }

        // Verificar conexão periodicamente
        setInterval(checkConnection, 5000);
        window.addEventListener('online', checkConnection);
        window.addEventListener('offline', checkConnection);

        function openConversation(conversationId, otherUserId) {
            currentConversationId = conversationId;
            currentOtherUserId = otherUserId;
            
            // Marcar conversa como ativa
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');
            
            // Marcar mensagens como lidas
            fetch('../backend/mark_messages_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `conversation_id=${conversationId}`
            });

            loadMessages();
            
            // Parar polling anterior e iniciar novo
            if (messagePolling) clearInterval(messagePolling);
            messagePolling = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    loadMessages(false); // false = não fazer scroll automático
                }
            }, 2000); // Reduzido para 2 segundos
        }

        function loadMessages(scrollToBottom = true) {
            if (!currentConversationId) return;

            fetch(`../backend/get_messages.php?conversation_id=${currentConversationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const newMessageCount = data.messages.length;
                        const hasNewMessages = newMessageCount > lastMessageCount;
                        
                        displayMessages(data.messages, data.other_user, scrollToBottom && hasNewMessages);
                        lastMessageCount = newMessageCount;
                        
                        // Atualizar lista de conversas se houver novas mensagens
                        if (hasNewMessages) {
                            updateConversationsList();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar mensagens:', error);
                });
        }

        function displayMessages(messages, otherUser, scrollToBottom = true) {
            const chatArea = document.getElementById('chatArea');
            const messagesContainer = document.getElementById('messagesContainer');
            
            // Se é a primeira vez carregando, criar toda a estrutura
            if (!messagesContainer) {
                chatArea.innerHTML = `
                    <div class="chat-header">
                        <img src="images/perfil/${otherUser.foto_perfil || 'default-profile.jpg'}" 
                             alt="${otherUser.nome_completo}" class="chat-avatar">
                        <div class="chat-user-info">
                            <h3>${otherUser.nome_completo}</h3>
                            <p>@${otherUser.nick}</p>
                        </div>
                    </div>
                    <div class="messages-container" id="messagesContainer">
                        ${generateMessagesHTML(messages)}
                    </div>
                    <div class="typing-indicator" id="typingIndicator">
                        <i class="fas fa-ellipsis-h"></i> Digitando...
                    </div>
                    <div class="message-input-container">
                        <form onsubmit="sendMessage(event)">
                            <input type="text" id="messageInput" placeholder="Escreva uma mensagem..." required>
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                `;
                
                if (scrollToBottom) {
                    setTimeout(() => scrollToBottomSmooth(), 100);
                }
            } else {
                // Apenas atualizar as mensagens
                const currentScrollTop = messagesContainer.scrollTop;
                const currentScrollHeight = messagesContainer.scrollHeight;
                
                messagesContainer.innerHTML = generateMessagesHTML(messages);
                
                if (scrollToBottom) {
                    scrollToBottomSmooth();
                } else {
                    // Manter posição de scroll se não for para ir para o fim
                    messagesContainer.scrollTop = currentScrollTop;
                }
            }
        }

        function generateMessagesHTML(messages) {
            return messages.map(message => `
                <div class="message ${message.remetente_id == <?php echo $currentUserId; ?> ? 'sent' : 'received'}">
                    <div class="message-content">
                        <p>${message.conteudo}</p>
                        <span class="message-time">${formatTime(message.data_envio)}</span>
                    </div>
                </div>
            `).join('');
        }

        function scrollToBottomSmooth() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

        function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const content = messageInput.value.trim();
            
            if (!content || !currentConversationId) return;

            // Limpar input imediatamente para melhor UX
            messageInput.value = '';

            fetch('../backend/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `conversation_id=${currentConversationId}&content=${encodeURIComponent(content)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Carregar mensagens imediatamente após enviar
                    loadMessages(true);
                    updateConversationsList();
                } else {
                    // Se falhou, restaurar o texto
                    messageInput.value = content;
                    alert('Erro ao enviar mensagem. Tente novamente.');
                }
            })
            .catch(error => {
                console.error('Erro ao enviar mensagem:', error);
                messageInput.value = content;
                alert('Erro ao enviar mensagem. Verifique sua conexão.');
            });
        }

        function openNewMessageModal() {
            document.getElementById('newMessageModal').style.display = 'flex';
            document.getElementById('userSearch').focus();
            document.body.style.overflow = 'hidden';
        }

        function closeNewMessageModal() {
            document.getElementById('newMessageModal').style.display = 'none';
            document.getElementById('userSearch').value = '';
            document.getElementById('userResults').innerHTML = '';
            document.body.style.overflow = 'auto';
        }

        function searchUsers() {
            const query = document.getElementById('userSearch').value.trim();
            
            if (query.length < 2) {
                document.getElementById('userResults').innerHTML = '';
                return;
            }

            fetch(`../backend/search_users.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(users => {
                    const resultsDiv = document.getElementById('userResults');
                    resultsDiv.innerHTML = users.map(user => `
                        <div class="user-result" onclick="startConversation(${user.id})">
                            <img src="images/perfil/${user.foto_perfil || 'default-profile.jpg'}" 
                                 alt="${user.nome_completo}" class="user-avatar">
                            <div class="user-info">
                                <h4>${user.nome_completo}</h4>
                                <p>@${user.nick}</p>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Erro na pesquisa:', error);
                });
        }

        function startConversation(userId) {
            fetch('../backend/create_conversation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `other_user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeNewMessageModal();
                    openConversation(data.conversation_id, userId);
                    updateConversationsList();
                }
            })
            .catch(error => {
                console.error('Erro ao criar conversa:', error);
            });
        }

        function updateConversationsList() {
            // Recarregar apenas a lista de conversas sem reload da página
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newConversationsList = doc.getElementById('conversationsList');
                    if (newConversationsList) {
                        document.getElementById('conversationsList').innerHTML = newConversationsList.innerHTML;
                        
                        // Reativar conversa atual
                        if (currentConversationId) {
                            const activeItem = document.querySelector(`[data-conversation-id="${currentConversationId}"]`);
                            if (activeItem) {
                                activeItem.classList.add('active');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar lista:', error);
                });
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInHours = (now - date) / (1000 * 60 * 60);

            if (diffInHours < 24) {
                return date.toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
            } else {
                return date.toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit' });
            }
        }

        // Fechar modal ao clicar fora
        document.getElementById('newMessageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNewMessageModal();
            }
        });

        // Cleanup ao sair da página
        window.addEventListener('beforeunload', function() {
            if (messagePolling) clearInterval(messagePolling);
        });

        // Pausar polling quando a página não está visível
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                if (messagePolling) clearInterval(messagePolling);
            } else if (currentConversationId) {
                // Retomar polling quando voltar à página
                messagePolling = setInterval(() => {
                    loadMessages(false);
                }, 2000);
            }
        });

        // Inicializar verificação de conexão
        checkConnection();
    </script>
</body>
</html>