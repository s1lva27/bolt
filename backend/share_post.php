<?php
session_start();
require "ligabd.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$currentUserId = $_SESSION['id'];
$postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$userIds = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da publicação inválido']);
    exit;
}

if (empty($userIds) || !is_array($userIds)) {
    echo json_encode(['success' => false, 'message' => 'Selecione pelo menos um utilizador']);
    exit;
}

try {
    // Verificar se a publicação existe
    $sqlPost = "SELECT p.*, u.nick, u.nome_completo 
                FROM publicacoes p 
                JOIN utilizadores u ON p.id_utilizador = u.id 
                WHERE p.id_publicacao = ? AND p.deletado_em = '0000-00-00 00:00:00'";
    $stmtPost = $con->prepare($sqlPost);
    $stmtPost->bind_param("i", $postId);
    $stmtPost->execute();
    $postResult = $stmtPost->get_result();
    
    if ($postResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Publicação não encontrada']);
        exit;
    }
    
    $post = $postResult->fetch_assoc();
    
    // Criar mensagem de partilha
    $shareMessage = "📤 Publicação partilhada por " . $_SESSION['nome_completo'] . "\n\n";
    
    if (!empty($message)) {
        $shareMessage .= "💬 " . $message . "\n\n";
    }
    
    $shareMessage .= "👤 @" . $post['nick'] . " (" . $post['nome_completo'] . ")\n";
    $shareMessage .= "📅 " . date('d/m/Y H:i', strtotime($post['data_criacao'])) . "\n\n";
    
    if (!empty($post['conteudo'])) {
        $shareMessage .= "📝 " . $post['conteudo'] . "\n\n";
    }
    
    $shareMessage .= "🔗 Ver publicação: " . $_SERVER['HTTP_HOST'] . "/frontend/perfil.php?id=" . $post['id_utilizador'] . "#post-" . $postId;
    
    $successCount = 0;
    $errors = [];
    
    foreach ($userIds as $userId) {
        $userId = intval($userId);
        
        if ($userId <= 0 || $userId === $currentUserId) {
            continue;
        }
        
        // Verificar se o utilizador existe
        $sqlUser = "SELECT id FROM utilizadores WHERE id = ?";
        $stmtUser = $con->prepare($sqlUser);
        $stmtUser->bind_param("i", $userId);
        $stmtUser->execute();
        
        if ($stmtUser->get_result()->num_rows === 0) {
            continue;
        }
        
        // Verificar se já existe conversa
        $sqlConversation = "SELECT id FROM conversas 
                           WHERE (utilizador1_id = ? AND utilizador2_id = ?) 
                           OR (utilizador1_id = ? AND utilizador2_id = ?)";
        $stmtConversation = $con->prepare($sqlConversation);
        $stmtConversation->bind_param("iiii", $currentUserId, $userId, $userId, $currentUserId);
        $stmtConversation->execute();
        $conversationResult = $stmtConversation->get_result();
        
        $conversationId = null;
        
        if ($conversationResult->num_rows > 0) {
            $conversation = $conversationResult->fetch_assoc();
            $conversationId = $conversation['id'];
        } else {
            // Criar nova conversa
            $sqlCreateConversation = "INSERT INTO conversas (utilizador1_id, utilizador2_id) VALUES (?, ?)";
            $stmtCreateConversation = $con->prepare($sqlCreateConversation);
            $stmtCreateConversation->bind_param("ii", $currentUserId, $userId);
            
            if ($stmtCreateConversation->execute()) {
                $conversationId = $con->insert_id;
            }
        }
        
        if ($conversationId) {
            // Enviar mensagem
            $sqlMessage = "INSERT INTO mensagens (conversa_id, remetente_id, conteudo) VALUES (?, ?, ?)";
            $stmtMessage = $con->prepare($sqlMessage);
            $stmtMessage->bind_param("iis", $conversationId, $currentUserId, $shareMessage);
            
            if ($stmtMessage->execute()) {
                // Atualizar última atividade da conversa
                $sqlUpdateConversation = "UPDATE conversas SET ultima_atividade = NOW() WHERE id = ?";
                $stmtUpdateConversation = $con->prepare($sqlUpdateConversation);
                $stmtUpdateConversation->bind_param("i", $conversationId);
                $stmtUpdateConversation->execute();
                
                $successCount++;
            } else {
                $errors[] = "Erro ao enviar para utilizador ID: $userId";
            }
        } else {
            $errors[] = "Erro ao criar conversa com utilizador ID: $userId";
        }
    }
    
    if ($successCount > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "Publicação partilhada com $successCount utilizador(es)",
            'shared_count' => $successCount
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao partilhar publicação',
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    error_log('Erro ao partilhar publicação: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>