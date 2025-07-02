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
$userIds = isset($_POST['user_ids']) ? json_decode($_POST['user_ids']) : [];
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
    // Obter informações completas da publicação
    $sqlPost = "SELECT p.*, u.nick, u.nome_completo, p.tipo 
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
    
    // Obter mídias da publicação se existirem
    $medias = [];
    $sqlMedias = "SELECT url, tipo FROM publicacao_medias WHERE publicacao_id = ? ORDER BY ordem ASC";
    $stmtMedias = $con->prepare($sqlMedias);
    $stmtMedias->bind_param("i", $postId);
    $stmtMedias->execute();
    $mediasResult = $stmtMedias->get_result();
    
    while ($media = $mediasResult->fetch_assoc()) {
        $medias[] = $media;
    }
    
    // Obter dados da enquete se for do tipo poll
    $pollData = null;
    if ($post['tipo'] === 'poll') {
        $sqlPoll = "SELECT p.id, p.pergunta, p.data_expiracao 
                   FROM polls p 
                   WHERE p.publicacao_id = ?";
        $stmtPoll = $con->prepare($sqlPoll);
        $stmtPoll->bind_param("i", $postId);
        $stmtPoll->execute();
        $pollResult = $stmtPoll->get_result();
        
        if ($pollResult->num_rows > 0) {
            $poll = $pollResult->fetch_assoc();
            $poll['expirada'] = strtotime($poll['data_expiracao']) < time();
            
            // Obter opções da enquete
            $sqlOptions = "SELECT opcao_texto, votos FROM poll_opcoes WHERE poll_id = ? ORDER BY ordem ASC";
            $stmtOptions = $con->prepare($sqlOptions);
            $stmtOptions->bind_param("i", $poll['id']);
            $stmtOptions->execute();
            $optionsResult = $stmtOptions->get_result();
            
            $options = [];
            $totalVotes = 0;
            while ($option = $optionsResult->fetch_assoc()) {
                $options[] = $option;
                $totalVotes += $option['votos'];
            }
            
            $poll['opcoes'] = $options;
            $poll['total_votos'] = $totalVotes;
            $pollData = $poll;
        }
    }
    
    // Criar mensagem de partilha formatada
    $shareMessage = "📤 Publicação partilhada por " . $_SESSION['nome_completo'] . "\n\n";
    
    if (!empty($message)) {
        $shareMessage .= "💬 " . $message . "\n\n";
    }
    
    $shareMessage .= "👤 @" . $post['nick'] . " (" . $post['nome_completo'] . ")\n";
    $shareMessage .= "📅 " . date('d/m/Y H:i', strtotime($post['data_criacao'])) . "\n\n";
    
    if (!empty($post['conteudo'])) {
        $shareMessage .= "📝 " . $post['conteudo'] . "\n\n";
    }
    
    // Adicionar informações sobre mídias
    if (!empty($medias)) {
        $mediaTypes = array_count_values(array_column($medias, 'tipo'));
        $mediaInfo = [];
        
        if (isset($mediaTypes['image'])) {
            $mediaInfo[] = "🖼️ " . $mediaTypes['image'] . " imagem" . ($mediaTypes['image'] > 1 ? 'ns' : '');
        }
        
        if (isset($mediaTypes['video'])) {
            $mediaInfo[] = "🎬 " . $mediaTypes['video'] . " vídeo" . ($mediaTypes['video'] > 1 ? 's' : '');
        }
        
        $shareMessage .= implode(", ", $mediaInfo) . "\n\n";
    }
    
    // Adicionar informações sobre enquete
    if ($pollData) {
        $shareMessage .= "📊 Enquete: " . $pollData['pergunta'] . "\n";
        $shareMessage .= "⏱️ " . ($pollData['expirada'] ? "Enquete encerrada" : "Enquete ativa") . "\n";
        $shareMessage .= "🗳️ Total de votos: " . $pollData['total_votos'] . "\n\n";
        
        foreach ($pollData['opcoes'] as $option) {
            $percentage = $pollData['total_votos'] > 0 ? round(($option['votos'] / $pollData['total_votos']) * 100) : 0;
            $shareMessage .= "▫️ " . $option['opcao_texto'] . " (" . $percentage . "%)\n";
        }
        
        $shareMessage .= "\n";
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