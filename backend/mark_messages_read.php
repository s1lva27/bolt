<?php
session_start();
require "ligabd.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if (!isset($_POST['conversation_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID da conversa não fornecido']);
    exit;
}

$currentUserId = $_SESSION['id'];
$conversationId = intval($_POST['conversation_id']);

// Marcar como lidas todas as mensagens da conversa que não foram enviadas pelo utilizador atual
$sql = "UPDATE mensagens 
        SET lida = 1 
        WHERE conversa_id = ? 
        AND remetente_id != ? 
        AND lida = 0";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $conversationId, $currentUserId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao marcar mensagens como lidas']);
}
?>