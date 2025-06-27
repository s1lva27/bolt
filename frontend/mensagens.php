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
        .messages-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
        }

        .messages-loading i {
            font-size: 2rem;
            margin-bottom: 1rem;
            animation: spin 1s linear infinite;
        }

        .error-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--color-danger);
            text-align: center;
            padding: 2rem;
        }

        .error-loading i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .error-loading button {
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        /* Melhorar aparência das mensagens e evitar flickering */
        .message {
            margin-bottom: var(--space-md);
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .message.loading {
            opacity: 0.7;
        }

        .messages-container {
            scroll-behavior: smooth;
            min-height: 200px;
        }

        /* Indicador de digitação melhorado */
        .typing-indicator {
            display: none;
            padding: var(--space-sm);
            color: var(--text-muted);
            font-style: italic;
            font-size: 0.9rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }
        }

        /* Status de conexão melhorado */
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

        /* Indicador de mensagem não lida */
        .message.unread {
            background: rgba(255, 87, 34, 0.05);
            border-left: 3px solid var(--color-primary);
            padding-left: calc(var(--space-md) - 3px);
        }

        /* Loading state para mensagens */
        .messages-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-muted);
        }

        .messages-loading i {
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Otimização para evitar reflow */
        .chat-area {
            contain: layout style paint;
        }

        .messages-container {
            contain: layout style paint;
            will-change: scroll-position;
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
        <?php require("parciais/sidebar.php"); ?>

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
                                <div class="conversation-item" data-conversation-id="<?php echo $conversa['id']; ?>"
                                    onclick="openConversation(<?php echo $conversa['id']; ?>, <?php echo $outroUtilizador['id']; ?>)">
                                    <img src="images/perfil/<?php echo $outroUtilizador['foto'] ?: 'default-profile.jpg'; ?>"
                                        alt="<?php echo htmlspecialchars($outroUtilizador['nome']); ?>"
                                        class="conversation-avatar">
                                    <div class="conversation-info">
                                        <div class="conversation-header">
                                            <h4><?php echo htmlspecialchars($outroUtilizador['nome']); ?></h4>
                                            <span class="conversation-time">
                                                <?php echo date('H:i', strtotime($conversa['ultima_atividade'])); ?>
                                            </span>
                                        </div>
                                        <p class="last-message">
                                            <?php echo htmlspecialchars(substr($conversa['ultima_mensagem'] ?: 'Iniciar conversa...', 0, 50)); ?>
                                            <?php if (strlen($conversa['ultima_mensagem']) > 50)
                                                echo '...'; ?>
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

        const AppState = {
            currentConversationId: null,
            currentOtherUserId: null,
            messagePolling: null,
            lastMessageCount: 0,
            isTyping: false,
            typingTimeout: null,
            connectionStatus: 'online',
            messagesCache: new Map(),
            isLoadingMessages: false,
            updatingConversations: false // Adicione esta linha
        };

        document.addEventListener('DOMContentLoaded', function () {
            updateConversationsList();
        });

        // Verificar conexão
        function checkConnection() {
            const statusEl = document.getElementById('connectionStatus');

            if (navigator.onLine) {
                if (AppState.connectionStatus !== 'online') {
                    AppState.connectionStatus = 'online';
                    statusEl.className = 'connection-status online';
                    statusEl.innerHTML = '<i class="fas fa-wifi"></i> Conectado';
                    statusEl.style.display = 'block';
                    setTimeout(() => statusEl.style.display = 'none', 2000);
                }
            } else {
                AppState.connectionStatus = 'offline';
                statusEl.className = 'connection-status offline';
                statusEl.innerHTML = '<i class="fas fa-wifi-slash"></i> Sem conexão';
                statusEl.style.display = 'block';
            }
        }

        // Verificar conexão periodicamente
        setInterval(checkConnection, 5000);
        window.addEventListener('online', checkConnection);
        window.addEventListener('offline', checkConnection);

        // Marcar mensagens como lidas
        fetch('../backend/mark_messages_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `conversation_id=${conversationId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.marked_as_read > 0) {
                    // Disparar evento para atualizar a sidebar
                    const event = new CustomEvent('unreadCountUpdated', {
                        detail: { change: -data.marked_as_read }
                    });
                    document.dispatchEvent(event);

                    // Para atualizar em outras abas
                    localStorage.setItem('unreadCountUpdate', JSON.stringify({
                        change: -data.marked_as_read,
                        timestamp: Date.now()
                    }));
                }
            });

        function openConversation(conversationId, otherUserId) {
            // Se já está na mesma conversa, não fazer nada
            if (AppState.currentConversationId === conversationId) return;

            // Atualizar o estado da aplicação
            AppState.currentConversationId = conversationId;
            AppState.currentOtherUserId = otherUserId;
            AppState.lastMessageCount = 0;

            // Marcar conversa como ativa na UI
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-conversation-id') == conversationId) {
                    item.classList.add('active');
                }
            });

            // Mostrar estado de carregamento
            const chatArea = document.getElementById('chatArea');
            chatArea.innerHTML = `
        <div class="messages-loading">
            <i class="fas fa-spinner"></i> Carregando conversa...
        </div>
    `;

            // Parar qualquer polling anterior
            if (AppState.messagePolling) {
                clearInterval(AppState.messagePolling);
            }

            // Carregar as mensagens imediatamente
            loadMessages(true);

            // Iniciar polling para atualizações periódicas
            AppState.messagePolling = setInterval(() => {
                if (document.visibilityState === 'visible' && !AppState.isLoadingMessages) {
                    loadMessages(false);
                }
            }, 3000);

            // Marcar mensagens como lidas
            markMessagesAsRead(conversationId);
        }

        function markMessagesAsRead(conversationId) {
            fetch('../backend/mark_messages_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `conversation_id=${conversationId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Disparar evento para atualizar a sidebar
                        const event = new CustomEvent('unreadCountUpdated', {
                            detail: { change: -data.marked_as_read }
                        });
                        document.dispatchEvent(event);

                        // Para atualizar em outras abas
                        localStorage.setItem('unreadCountUpdate', JSON.stringify({
                            change: -data.marked_as_read,
                            timestamp: Date.now()
                        }));

                        // Atualizar o badge na lista de conversas
                        updateConversationBadge(conversationId, 0);
                    }
                });
        }

        function updateConversationBadge(conversationId, newCount) {
            const badge = document.querySelector(`[data-conversation-id="${conversationId}"] .unread-badge`);
            if (!badge) return;

            if (newCount > 0) {
                badge.textContent = newCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function loadMessages(scrollToBottom = true) {
            if (!AppState.currentConversationId || AppState.isLoadingMessages) return;

            AppState.isLoadingMessages = true;

            fetch(`../backend/get_messages.php?conversation_id=${AppState.currentConversationId}`)
                .then(response => {
                    // Primeiro verifique se a resposta é OK
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor');
                    }

                    // Tentar parsear como JSON
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Resposta inválida:', text);
                            throw new Error('Resposta do servidor não é JSON válido');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        // Processar mensagens...
                    } else {
                        throw new Error(data.message || 'Erro ao carregar mensagens');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    // Mostrar mensagem de erro ao usuário
                })
                .finally(() => {
                    AppState.isLoadingMessages = false;
                });
        }

        function displayMessages(messages, otherUser, scrollToBottom = true) {
            const chatArea = document.getElementById('chatArea');

            // Criar cabeçalho do chat
            const chatHeader = `
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

            chatArea.innerHTML = chatHeader;

            if (scrollToBottom) {
                setTimeout(() => {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 100);
            }
        }

        function generateMessagesHTML(messages) {
            return messages.map(message => `
                <div class="message ${message.remetente_id == <?php echo $currentUserId; ?> ? 'sent' : 'received'}">
                    <div class="message-content">
                        <p>${escapeHtml(message.conteudo)}</p>
                        <span class="message-time">${formatTime(message.data_envio)}</span>
                    </div>
                </div>
            `).join('');
        }

        function isScrolledToBottom(element) {
            return element.scrollHeight - element.clientHeight <= element.scrollTop + 1;
        }

        function scrollToBottomSmooth() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function sendMessage(event) {
            event.preventDefault();

            const messageInput = document.getElementById('messageInput');
            const content = messageInput.value.trim();

            if (!content || !AppState.currentConversationId) return;

            // Limpar input imediatamente para melhor UX
            messageInput.value = '';

            // Adicionar mensagem temporária para feedback imediato
            addTemporaryMessage(content);

            fetch('../backend/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `conversation_id=${AppState.currentConversationId}&content=${encodeURIComponent(content)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Limpar cache para forçar reload das mensagens
                        AppState.messagesCache.delete(`conv_${AppState.currentConversationId}`);
                        loadMessages(true);
                        updateConversationsList();
                    } else {
                        // Se falhou, restaurar o texto e remover mensagem temporária
                        messageInput.value = content;
                        removeTemporaryMessage();
                        alert('Erro ao enviar mensagem. Tente novamente.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao enviar mensagem:', error);
                    messageInput.value = content;
                    removeTemporaryMessage();
                    alert('Erro ao enviar mensagem. Verifique sua conexão.');
                });
        }

        function addTemporaryMessage(content) {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                const tempMessage = document.createElement('div');
                tempMessage.className = 'message sent loading';
                tempMessage.id = 'temp-message';
                tempMessage.innerHTML = `
                    <div class="message-content">
                        <p>${escapeHtml(content)}</p>
                        <span class="message-time">Enviando...</span>
                    </div>
                `;
                messagesContainer.appendChild(tempMessage);
                scrollToBottomSmooth();
            }
        }

        function removeTemporaryMessage() {
            const tempMessage = document.getElementById('temp-message');
            if (tempMessage) {
                tempMessage.remove();
            }
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na rede');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeNewMessageModal();

                        // Adiciona a nova conversa diretamente à lista sem recarregar
                        const conversationsList = document.getElementById('conversationsList');

                        // Remove a mensagem "Nenhuma conversa" se existir
                        const noConversations = conversationsList.querySelector('.no-conversations');
                        if (noConversations) {
                            noConversations.remove();
                        }

                        // Cria o HTML para a nova conversa
                        const conversationHtml = `
                <div class="conversation-item active" data-conversation-id="${data.conversation_id}" 
                     onclick="openConversation(${data.conversation_id}, ${data.other_user.id})">
                    <img src="images/perfil/default-profile.jpg" 
                         alt="${data.other_user.nome}" class="conversation-avatar">
                    <div class="conversation-info">
                        <div class="conversation-header">
                            <h4>${data.other_user.nome}</h4>
                            <span class="conversation-time">Agora</span>
                        </div>
                        <p class="last-message">Iniciar conversa...</p>
                    </div>
                </div>
            `;

                        // Insere no início da lista
                        conversationsList.insertAdjacentHTML('afterbegin', conversationHtml);

                        // Abre a conversa
                        openConversation(data.conversation_id, data.other_user.id);
                    } else {
                        alert(data.message || 'Erro ao criar conversa');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro de conexão. A conversa pode ter sido criada - verifique sua lista de conversas.');
                });
        }

        function updateConversationsList() {
            // Verificar se já está atualizando para evitar chamadas redundantes
            if (AppState.updatingConversations) return;
            AppState.updatingConversations = true;

            fetch(`../backend/get_conversations.php`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const conversationsList = document.getElementById('conversationsList');

                        // Verificar se houve mudanças antes de atualizar o DOM
                        const currentHTML = conversationsList.innerHTML;
                        let newHTML = '';

                        if (data.conversations.length === 0) {
                            newHTML = `
                        <div class="no-conversations">
                            <i class="fas fa-comments"></i>
                            <h3>Nenhuma conversa ainda</h3>
                            <p>Comece uma nova conversa com alguém!</p>
                            <button class="start-conversation-btn" onclick="openNewMessageModal()">
                                Iniciar Conversa
                            </button>
                        </div>
                    `;
                        } else {
                            newHTML = data.conversations.map(conversation => {
                                const otherUser = conversation.other_user;
                                return `
                            <div class="conversation-item" data-conversation-id="${conversation.id}" onclick="openConversation(${conversation.id}, ${otherUser.id})">
                                <img src="images/perfil/${otherUser.foto || 'default-profile.jpg'}" 
                                     alt="${otherUser.nome}" class="conversation-avatar">
                                <div class="conversation-info">
                                    <div class="conversation-header">
                                        <h4>${otherUser.nome}</h4>
                                        <span class="conversation-time">
                                            ${formatTime(conversation.ultima_atividade)}
                                        </span>
                                    </div>
                                    <p class="last-message">
                                        ${conversation.ultima_mensagem ? escapeHtml(conversation.ultima_mensagem.substring(0, 50)) : 'Iniciar conversa...'}
                                        ${conversation.ultima_mensagem && conversation.ultima_mensagem.length > 50 ? '...' : ''}
                                    </p>
                                </div>
                                ${conversation.mensagens_nao_lidas > 0 ?
                                        `<div class="unread-badge">${conversation.mensagens_nao_lidas}</div>` : ''}
                            </div>
                        `;
                            }).join('');
                        }

                        // Só atualizar o DOM se o conteúdo mudou
                        if (currentHTML !== newHTML) {
                            conversationsList.innerHTML = newHTML;

                            // Manter a conversa ativa selecionada
                            if (AppState.currentConversationId) {
                                const activeItem = document.querySelector(`[data-conversation-id="${AppState.currentConversationId}"]`);
                                if (activeItem) {
                                    activeItem.classList.add('active');
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar lista de conversas:', error);
                })
                .finally(() => {
                    AppState.updatingConversations = false;
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
        document.getElementById('newMessageModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeNewMessageModal();
            }
        });

        // Cleanup ao sair da página
        window.addEventListener('beforeunload', function () {
            if (AppState.messagePolling) clearInterval(AppState.messagePolling);
        });

        // Pausar polling quando a página não está visível
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                if (AppState.messagePolling) clearInterval(AppState.messagePolling);
            } else if (AppState.currentConversationId) {
                // Retomar polling quando voltar à página
                AppState.messagePolling = setInterval(() => {
                    if (!AppState.isLoadingMessages) {
                        loadMessages(false);
                    }
                }, 3000);
            }
        });

        // Inicializar verificação de conexão
        checkConnection();

        // Debounce para pesquisa
        let searchTimeout;
        document.getElementById('userSearch').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchUsers, 300);
        });
    </script>
</body>

</html>