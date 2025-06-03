<?php
session_start();
require "ligabd.php";

if (!isset($_SESSION["id"])) {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_publicacao"])) {
    $userId = $_SESSION["id"];
    $publicacaoId = intval($_POST["id_publicacao"]);
    
    // Verificar se o usuário já deu like nesta publicação
    $checkSql = "SELECT * FROM publicacao_likes WHERE publicacao_id = ? AND utilizador_id = ?";
    $stmtCheck = $con->prepare($checkSql);
    $stmtCheck->bind_param("ii", $publicacaoId, $userId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows > 0) {
        // Remover like
        $deleteSql = "DELETE FROM publicacao_likes WHERE publicacao_id = ? AND utilizador_id = ?";
        $stmtDelete = $con->prepare($deleteSql);
        $stmtDelete->bind_param("ii", $publicacaoId, $userId);
        
        if ($stmtDelete->execute()) {
            // Atualizar contagem de likes
            $updateSql = "UPDATE publicacoes SET likes = likes - 1 WHERE id_publicacao = ?";
            $stmtUpdate = $con->prepare($updateSql);
            $stmtUpdate->bind_param("i", $publicacaoId);
            $stmtUpdate->execute();
            echo "unliked";
        } else {
            echo "error";
        }
    } else {
        // Adicionar like
        $insertSql = "INSERT INTO publicacao_likes (publicacao_id, utilizador_id) VALUES (?, ?)";
        $stmtInsert = $con->prepare($insertSql);
        $stmtInsert->bind_param("ii", $publicacaoId, $userId);
        
        if ($stmtInsert->execute()) {
            // Atualizar contagem de likes
            $updateSql = "UPDATE publicacoes SET likes = likes + 1 WHERE id_publicacao = ?";
            $stmtUpdate = $con->prepare($updateSql);
            $stmtUpdate->bind_param("i", $publicacaoId);
            $stmtUpdate->execute();
            echo "liked";
        } else {
            echo "error";
        }
    }
} else {
    die("Requisição inválida.");
}
?>