<?php
session_start();
require "ligabd.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if (!isset($_POST['other_user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do utilizador não fornecido']);
    exit;
}

$currentUserId = $_SESSION['id'];
$otherUserId = intval($_POST['other_user_id']);

// Verificar se já existe uma conversa entre os dois utilizadores
$sqlCheck = "SELECT id FROM conversas 
             WHERE (utilizador1_id = ? AND utilizador2_id = ?) 
             OR (utilizador1_id = ? AND utilizador2_id = ?)";
$stmtCheck = $con->prepare($sqlCheck);
$stmtCheck->bind_param("iiii", $currentUserId, $otherUserId, $otherUserId, $currentUserId);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // Conversa já existe
    $conversation = $result->fetch_assoc();
    echo json_encode(['success' => true, 'conversation_id' => $conversation['id']]);
} else {
    // Criar nova conversa
    $sqlCreate = "INSERT INTO conversas (utilizador1_id, utilizador2_id) VALUES (?, ?)";
    $stmtCreate = $con->prepare($sqlCreate);
    $stmtCreate->bind_param("ii", $currentUserId, $otherUserId);
    
    if ($stmtCreate->execute()) {
        $conversationId = $con->insert_id;
        echo json_encode(['success' => true, 'conversation_id' => $conversationId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar conversa']);
    }
}
?>