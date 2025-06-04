<?php
session_start();
include "ligabd.php";

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$postId = intval($_POST['post_id']);
$content = mysqli_real_escape_string($con, $_POST['content']);
$userId = $_SESSION['id'];

$sql = "INSERT INTO comentarios (id_publicacao, utilizador_id, conteudo, data) 
        VALUES ($postId, $userId, '$content', NOW())";

if (mysqli_query($con, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
}
?>