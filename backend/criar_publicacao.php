<?php
session_start();
require 'ligabd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicar'])) {
    // Verificar se o usuário está logado
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }

    // Sanitizar e validar entrada
    $conteudo = trim(htmlspecialchars($_POST['conteudo']));
    
    if (empty($conteudo)) {
        $_SESSION['erro'] = "O conteúdo não pode estar vazio!";
        header('Location: feed.php');
        exit();
    }

    try {
        // Inserir no banco de dados
        $stmt = $con->prepare("
            INSERT INTO publicacoes 
            (id_utilizador, conteudo, data_criacao) 
            VALUES (?, ?, NOW())
        ");
        
        $stmt->bind_param("is", 
            $_SESSION['id'],
            $conteudo
        );

        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Publicação criada com sucesso!";
        } else {
            throw new Exception("Erro ao publicar");
        }

    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao publicar: " . $e->getMessage();
    } finally {
        $stmt->close();
        header('Location: ../frontend/index.php');
        exit();
    }
}