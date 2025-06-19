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
    if (empty($conteudo) && empty($_FILES['imagens']['name'][0])) {
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
            
            // Processar upload de múltiplas imagens se existirem
            if(isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
                $files = $_FILES['imagens'];
                $maxFiles = 10;
                $fileCount = count($files['name']);
                
                if ($fileCount > $maxFiles) {
                    throw new Exception("Máximo de $maxFiles imagens permitidas.");
                }
                
                // Tipos de arquivo permitidos
                $allowedTypes = [
                    'image/jpeg' => 'jpg', 
                    'image/png' => 'png', 
                    'image/gif' => 'gif',
                    'image/webp' => 'webp'
                ];
                
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($files['error'][$i] == UPLOAD_ERR_OK) {
                        $fileType = $files['type'][$i];
                        $fileSize = $files['size'][$i];
                        $maxSize = 5 * 1024 * 1024; // 5MB
                        
                        if ($fileSize > $maxSize) {
                            throw new Exception("Arquivo muito grande. Máximo 5MB por imagem.");
                        }
                        
                        $fileExt = isset($allowedTypes[$fileType]) ? $allowedTypes[$fileType] : false;
                        
                        if($fileExt) {
                            // Gerar nome único para o arquivo
                            $fileName = uniqid('img_' . time() . '_' . $i . '_') . '.' . $fileExt;
                            $uploadPath = '../frontend/images/publicacoes/' . $fileName;
                            
                            // Mover arquivo para a pasta de publicações
                            if(move_uploaded_file($files['tmp_name'][$i], $uploadPath)) {
                                // Inserir na tabela de mídia
                                $sqlMedia = "INSERT INTO publicacao_medias 
                                            (publicacao_id, tipo, url, ordem) 
                                            VALUES (?, 'imagem', ?, ?)";
                                $stmtMedia = $con->prepare($sqlMedia);
                                $stmtMedia->bind_param('isi', $publicacaoId, $fileName, $i);
                                $stmtMedia->execute();
                                $stmtMedia->close();
                            } else {
                                throw new Exception("Falha ao mover o arquivo $i.");
                            }
                        } else {
                            throw new Exception("Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP.");
                        }
                    }
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