<?php
include "ligabd.php";

$postId = intval($_GET['id']);
$sql = "SELECT p.*, u.id AS id_utilizador, u.nick, pr.foto_perfil, pr.ocupacao 
        FROM publicacoes p
        JOIN utilizadores u ON p.id_utilizador = u.id
        LEFT JOIN perfis pr ON u.id = pr.id_utilizador
        WHERE p.id_publicacao = $postId";

$result = mysqli_query($con, $sql);
$post = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode($post);
?>