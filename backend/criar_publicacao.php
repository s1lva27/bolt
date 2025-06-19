<?php
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
    
    // Verificar se há conteúdo ou imagem
    if (empty($conteudo) && empty($_FILES['imagem']['name'])) {
        $_SESSION['erro'] = "Adicione texto ou uma imagem para publicar!";
        header('Location: ../frontend/index.php');
        exit();
    }

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
            
            // Processar upload de imagem se existir
            if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
                $file = $_FILES['imagem'];
                
                // Tipos de arquivo permitidos
                $allowedTypes = [
                    'image/jpeg' => 'jpg', 
                    'image/png' => 'png', 
                    'image/gif' => 'gif'
                ];
                $fileType = $file['type'];
                $fileExt = isset($allowedTypes[$fileType]) ? $allowedTypes[$fileType] : false;
                
                if($fileExt) {
                    // Gerar nome único para o arquivo
                    $fileName = uniqid('img_') . '.' . $fileExt;
                    $uploadPath = '../frontend/images/publicacoes/' . $fileName;
                    
                    // Mover arquivo para a pasta de publicações
                    if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        // Inserir na tabela de mídia
                        $sqlMedia = "INSERT INTO publicacao_medias 
                                    (publicacao_id, tipo, url) 
                                    VALUES (?, 'imagem', ?)";
                        $stmtMedia = $con->prepare($sqlMedia);
                        $stmtMedia->bind_param('is', $publicacaoId, $fileName);
                        $stmtMedia->execute();
                        $stmtMedia->close();
                    } else {
                        throw new Exception("Falha ao mover o arquivo.");
                    }
                } else {
                    throw new Exception("Tipo de arquivo não permitido. Use JPG, PNG ou GIF.");
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