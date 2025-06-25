<?php
session_start();
include "ligabd.php";

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if (!isset($_POST['id_publicacao'])) {
    echo json_encode(['success' => false, 'message' => 'ID da publicação não fornecido']);
    exit;
}

$postId = intval($_POST['id_publicacao']);
$userId = $_SESSION['id'];
$userType = $_SESSION['id_tipos_utilizador'];

// Verificar se o usuário é o autor ou admin
$sql = "SELECT id_utilizador FROM publicacoes WHERE id_publicacao = $postId";
$result = mysqli_query($con, $sql);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo json_encode(['success' => false, 'message' => 'Publicação não encontrada']);
    exit;
}

if ($post['id_utilizador'] != $userId && $userType != 2) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// "Apagar" a publicação (marcar como deletada em vez de remover fisicamente)
$sql = "UPDATE publicacoes SET deletado_em = NOW() WHERE id_publicacao = $postId";
$result = mysqli_query($con, $sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao apagar publicação']);
}
?>