<?php
session_start();
require "ligabd.php";

header('Content-Type: application/json'); // Adicione esta linha

if (!isset($_SESSION["id"])) {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_seguido"], $_POST["acao"])) {
    $idSeguidor = $_SESSION["id"];
    $idSeguido = intval($_POST["id_seguido"]);

    if ($idSeguidor === $idSeguido) {
        die("Não pode seguir-se a si próprio.");
    }

    if ($_POST["acao"] === "follow") {
        // Adicionar seguimento
        $sql = "INSERT INTO seguidores (id_seguidor, id_seguido) VALUES (?, ?)";
    } elseif ($_POST["acao"] === "unfollow") {
        // Remover seguimento
        $sql = "DELETE FROM seguidores WHERE id_seguidor = ? AND id_seguido = ?";
    } else {
        die("Ação inválida.");
    }

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $idSeguidor, $idSeguido);
    
    if ($stmt->execute()) {
        header("Location: ../frontend/perfil.php?id=$idSeguido");
        echo json_encode(['success' => true, 'action' => $action]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao processar ação.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}

