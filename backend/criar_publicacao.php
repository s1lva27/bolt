<?php

$MAX_MEDIA = 5;
$EXT_PERMITIDAS = ['jpg', 'jpeg', 'png', 'gif'];

session_start();
require 'ligabd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicar'])) {
    // Verificar se o usuário está logado
    if (!isset($_SESSION['id'])) {
        $_SESSION['erro'] = "Por favor, faça login para publicar.";
        header('Location: ../frontend/login.php');
        exit();
    }

    // Sanitizar e validar entrada de texto
    $conteudo = trim(htmlspecialchars($_POST['conteudo']));
    
    echo '<pre>';
    var_dump($_FILES);
    echo '</pre>';

    try {
        // Inserir no banco de dados
        $stmt = $con->prepare("
            INSERT INTO publicacoes 
            (id_utilizador, conteudo, data_criacao) 
            VALUES (?, ?, NOW())
        ");
        
        $stmt->bind_param("is", $_SESSION['id'], $conteudo);

        if ($stmt->execute()) {
            $publicacaoId = $stmt->insert_id;

            for ($i=0; $i < $MAX_MEDIA; $i++) { 
                $media = $_FILES["media".$i];

                if(empty($media['name']))
                {
                    continue;
                }

                $ext = strtolower(pathinfo(basename($media["name"]), PATHINFO_EXTENSION));
                
                if (!in_array($ext, $EXT_PERMITIDAS)) {
                    die("Erro: Apenas imagens JPG, JPEG, PNG ou GIF são permitidas.");
                }

                $novo_nome = uniqid('pub_' . time() . '_' . $i . '_') . '.' . $ext;
                $destino = "../frontend/images/publicacoes/" . $novo_nome;

                if (move_uploaded_file($media['tmp_name'], $destino)) {

                    $sql_pub_medias = "INSERT INTO publicacao_medias
                        (publicacao_id, url, ordem, content_warning) VALUES
                        ($publicacaoId, '$novo_nome', $i, 'none')";

                    $result_pub_medias = mysqli_query($con, $sql_pub_medias);

                    echo 'sql: '.$sql_pub_medias;
                    // return;
                }
            }
            
            $_SESSION['sucesso'] = "Publicação criada com sucesso!";
            header('Location: ../frontend/index.php');
            exit();
        } else {
            throw new Exception("Erro ao publicar no banco de dados.");
        }

    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
        header('Location: ../frontend/index.php');
        exit();
    } finally {
        if(isset($stmt)) $stmt->close();
    }
} else {
    // Se alguém tentar acessar diretamente o arquivo
    header('Location: ../frontend/index.php');
    exit();
}
?>