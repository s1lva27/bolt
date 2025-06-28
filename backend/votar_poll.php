<?php
session_start();
require 'ligabd.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['poll_id']) || !isset($_POST['opcao_id'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$pollId = intval($_POST['poll_id']);
$opcaoId = intval($_POST['opcao_id']);
$userId = $_SESSION['id'];

try {
    // Verificar se a poll ainda está ativa
    $sqlCheck = "SELECT data_expiracao FROM polls WHERE id = ?";
    $stmtCheck = $con->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $pollId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Poll não encontrada']);
        exit;
    }
    
    $poll = $result->fetch_assoc();
    if (strtotime($poll['data_expiracao']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Esta enquete já expirou']);
        exit;
    }

    // Verificar se o usuário já votou
    $sqlVoteCheck = "SELECT id FROM poll_votos WHERE poll_id = ? AND utilizador_id = ?";
    $stmtVoteCheck = $con->prepare($sqlVoteCheck);
    $stmtVoteCheck->bind_param("ii", $pollId, $userId);
    $stmtVoteCheck->execute();
    
    if ($stmtVoteCheck->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Você já votou nesta enquete']);
        exit;
    }

    // Verificar se a opção pertence à poll
    $sqlOpcaoCheck = "SELECT id FROM poll_opcoes WHERE id = ? AND poll_id = ?";
    $stmtOpcaoCheck = $con->prepare($sqlOpcaoCheck);
    $stmtOpcaoCheck->bind_param("ii", $opcaoId, $pollId);
    $stmtOpcaoCheck->execute();
    
    if ($stmtOpcaoCheck->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Opção inválida']);
        exit;
    }

    // Iniciar transação
    mysqli_begin_transaction($con);

    // Registrar voto
    $sqlVote = "INSERT INTO poll_votos (poll_id, opcao_id, utilizador_id) VALUES (?, ?, ?)";
    $stmtVote = $con->prepare($sqlVote);
    $stmtVote->bind_param("iii", $pollId, $opcaoId, $userId);
    $stmtVote->execute();

    // Atualizar contador da opção
    $sqlUpdateOpcao = "UPDATE poll_opcoes SET votos = votos + 1 WHERE id = ?";
    $stmtUpdateOpcao = $con->prepare($sqlUpdateOpcao);
    $stmtUpdateOpcao->bind_param("i", $opcaoId);
    $stmtUpdateOpcao->execute();

    // Atualizar total de votos da poll
    $sqlUpdatePoll = "UPDATE polls SET total_votos = total_votos + 1 WHERE id = ?";
    $stmtUpdatePoll = $con->prepare($sqlUpdatePoll);
    $stmtUpdatePoll->bind_param("i", $pollId);
    $stmtUpdatePoll->execute();

    // Confirmar transação
    mysqli_commit($con);

    // Buscar dados atualizados da poll
    $sqlPollData = "
        SELECT p.total_votos, po.id, po.opcao_texto, po.votos
        FROM polls p
        JOIN poll_opcoes po ON p.id = po.poll_id
        WHERE p.id = ?
        ORDER BY po.ordem ASC
    ";
    $stmtPollData = $con->prepare($sqlPollData);
    $stmtPollData->bind_param("i", $pollId);
    $stmtPollData->execute();
    $pollDataResult = $stmtPollData->get_result();

    $opcoes = [];
    $totalVotos = 0;
    
    while ($row = $pollDataResult->fetch_assoc()) {
        $totalVotos = $row['total_votos'];
        $opcoes[] = [
            'id' => $row['id'],
            'texto' => $row['opcao_texto'],
            'votos' => $row['votos'],
            'percentagem' => $totalVotos > 0 ? round(($row['votos'] / $totalVotos) * 100, 1) : 0
        ];
    }

    echo json_encode([
        'success' => true,
        'total_votos' => $totalVotos,
        'opcoes' => $opcoes,
        'user_voted' => true
    ]);

} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>