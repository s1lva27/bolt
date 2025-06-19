<?php
session_start();
require "ligabd.php";

header('Content-Type: application/json'); // Adicione esta linha

if (!isset($_SESSION["id"])) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"])) {
    $idSeguidor = $_SESSION["id"];
    $idSeguido = intval($_POST["user_id"]);

    if ($idSeguidor === $idSeguido) {
        echo json_encode(['success' => false, 'message' => 'Não pode seguir-se a si próprio.']);
        exit;
    }

    // Verificar se já está seguindo
    $checkSql = "SELECT * FROM seguidores WHERE id_seguidor = ? AND id_seguido = ?";
    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("ii", $idSeguidor, $idSeguido);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $isFollowing = $checkResult->num_rows > 0;

    if ($isFollowing) {
        // Deixar de seguir
        $sql = "DELETE FROM seguidores WHERE id_seguidor = ? AND id_seguido = ?";
        $action = 'unfollow';
    } else {
        // Seguir
        $sql = "INSERT INTO seguidores (id_seguidor, id_seguido) VALUES (?, ?)";
        $action = 'follow';
    }

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $idSeguidor, $idSeguido);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'action' => $action]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao processar ação.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}