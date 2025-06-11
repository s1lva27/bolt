<?php
// backend/save_post.php
session_start();
include "ligabd.php";

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postId = $_POST['id_publicacao'];
    $userId = $_SESSION['id'];

    // Verificar se já está salvo
    $checkSql = "SELECT * FROM publicacao_salvas 
                 WHERE publicacao_id = $postId AND utilizador_id = $userId";
    $checkResult = mysqli_query($con, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        // Remover salvamento
        $deleteSql = "DELETE FROM publicacao_salvas 
                      WHERE publicacao_id = $postId AND utilizador_id = $userId";
        if (mysqli_query($con, $deleteSql)) {
            echo json_encode(['success' => true, 'action' => 'unsaved']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao remover salvamento']);
        }
    } else {
        // Adicionar salvamento
        $insertSql = "INSERT INTO publicacao_salvas (utilizador_id, publicacao_id) 
                      VALUES ($userId, $postId)";
        if (mysqli_query($con, $insertSql)) {
            echo json_encode(['success' => true, 'action' => 'saved']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar publicação']);
        }
    }
}
?>