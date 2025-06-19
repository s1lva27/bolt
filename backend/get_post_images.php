<?php
include "ligabd.php";

if (!isset($_GET['post_id'])) {
    echo json_encode([]);
    exit;
}

$postId = intval($_GET['post_id']);

$sql = "SELECT url, content_warning FROM publicacao_medias 
        WHERE publicacao_id = ? AND tipo = 'imagem' 
        ORDER BY ordem ASC";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}

header('Content-Type: application/json');
echo json_encode($images);
?>