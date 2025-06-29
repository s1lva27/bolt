<?php
session_start();
include "ligabd.php";

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$pollId = intval($_POST['poll_id'] ?? 0);
$opcaoId = intval($_POST['opcao_id'] ?? 0);

if ($pollId <= 0 || $opcaoId <= 0) {
    echo json_encode(['success' => false, 'message' => 'IDs inválidos']);
    exit;
}

// Verificar se a poll ainda está ativa
$stmt = $con->prepare("SELECT p.id, p.data_expiracao 
                      FROM polls p
                      WHERE p.id = ? AND p.data_expiracao > NOW()");
$stmt->bind_param("i", $pollId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Poll não encontrada ou já expirada']);
    exit;
}

// Verificar se a opção pertence à poll
$stmt = $con->prepare("SELECT id FROM poll_opcoes WHERE id = ? AND poll_id = ?");
$stmt->bind_param("ii", $opcaoId, $pollId);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Opção inválida']);
    exit;
}

// Verificar se o usuário já votou
$stmt = $con->prepare("SELECT id FROM poll_votos WHERE poll_id = ? AND utilizador_id = ?");
$stmt->bind_param("ii", $pollId, $_SESSION['id']);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Você já votou nesta poll']);
    exit;
}

// Iniciar transação
$con->begin_transaction();

try {
    // Registrar o voto
    $stmt = $con->prepare("INSERT INTO poll_votos (poll_id, opcao_id, utilizador_id, data_voto) 
                          VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $pollId, $opcaoId, $_SESSION['id']);
    $stmt->execute();

    // Atualizar contagem na opção
    $stmt = $con->prepare("UPDATE poll_opcoes SET votos = votos + 1 WHERE id = ?");
    $stmt->bind_param("i", $opcaoId);
    $stmt->execute();

    // Atualizar total de votos na poll
    $stmt = $con->prepare("UPDATE polls SET total_votos = total_votos + 1 WHERE id = ?");
    $stmt->bind_param("i", $pollId);
    $stmt->execute();

    $con->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Voto registrado com sucesso'
    ]);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao registrar voto: ' . $e->getMessage()
    ]);
}
?>