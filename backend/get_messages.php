<?php
ob_start(); // Inicia buffer de saída
session_start();
require "ligabd.php";

// Verificar autenticação primeiro
if (!isset($_SESSION['id'])) {
    ob_end_clean(); // Limpa buffer
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if (!isset($_GET['conversation_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'ID da conversa não fornecido']);
    exit;
}

$currentUserId = $_SESSION['id'];
$conversationId = intval($_GET['conversation_id']);

// Verificar se o utilizador faz parte da conversa
$sqlCheck = "SELECT utilizador1_id, utilizador2_id FROM conversas 
             WHERE id = ? AND (utilizador1_id = ? OR utilizador2_id = ?)";
$stmtCheck = $con->prepare($sqlCheck);
$stmtCheck->bind_param("iii", $conversationId, $currentUserId, $currentUserId);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Conversa não encontrada']);
    exit;
}

$conversation = $result->fetch_assoc();
$otherUserId = ($conversation['utilizador1_id'] == $currentUserId) ?
    $conversation['utilizador2_id'] : $conversation['utilizador1_id'];

// Buscar informações do outro utilizador
$sqlUser = "SELECT u.id, u.nick, u.nome_completo, p.foto_perfil 
            FROM utilizadores u 
            LEFT JOIN perfis p ON u.id = p.id_utilizador 
            WHERE u.id = ?";
$stmtUser = $con->prepare($sqlUser);
$stmtUser->bind_param("i", $otherUserId);
$stmtUser->execute();
$otherUser = $stmtUser->get_result()->fetch_assoc();

// Buscar mensagens
$sqlMessages = "SELECT * FROM mensagens 
                WHERE conversa_id = ? 
                ORDER BY data_envio ASC";
$stmtMessages = $con->prepare($sqlMessages);
$stmtMessages->bind_param("i", $conversationId);
$stmtMessages->execute();
$result = $stmtMessages->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Contar mensagens não lidas
$sqlUnread = "SELECT COUNT(*) as unread_count FROM mensagens 
              WHERE conversa_id = ? AND remetente_id != ? AND lida = 0";
$stmtUnread = $con->prepare($sqlUnread);
$stmtUnread->bind_param("ii", $conversationId, $currentUserId);
$stmtUnread->execute();
$unreadResult = $stmtUnread->get_result()->fetch_assoc();

echo json_encode([
    'success' => true,
    'messages' => $messages,
    'other_user' => $otherUser,
    'unread_count' => $unreadResult['unread_count']
]);


?>
<script>
    // Polling para atualizar contagem de mensagens não lidas
    let unreadPolling = null;
    const POLL_INTERVAL = 5000; // 5 segundos

    function startUnreadPolling() {
        // Se já estiver rodando, limpar primeiro
        if (unreadPolling) clearInterval(unreadPolling);

        // Verificar imediatamente e depois em intervalos
        checkUnreadMessages();
        unreadPolling = setInterval(checkUnreadMessages, POLL_INTERVAL);
    }

    function checkUnreadMessages() {
        fetch('../backend/get_unread_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUnreadCountDisplay(data.total_unread);
                }
            })
            .catch(error => console.error('Erro ao verificar mensagens não lidas:', error));
    }

    function updateUnreadCountDisplay(totalUnread) {
        const badge = document.getElementById('unread-count-badge');
        if (!badge) return;

        const currentCount = parseInt(badge.textContent) || 0;

        // Só atualizar se mudou
        if (currentCount !== totalUnread) {
            const change = totalUnread - currentCount;
            updateUnreadCount(change);
        }
    }

    // Iniciar quando a página carregar
    document.addEventListener('DOMContentLoaded', startUnreadPolling);

    // Pausar quando a aba não estiver visível
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') {
            if (unreadPolling) clearInterval(unreadPolling);
        } else {
            startUnreadPolling();
        }
    });
</script>