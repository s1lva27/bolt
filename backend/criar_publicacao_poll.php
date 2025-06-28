<?php
session_start();
require 'ligabd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publicar_poll'])) {
    // Verificar se o usuário está logado
    if (!isset($_SESSION['id'])) {
        $_SESSION['erro'] = "Por favor, faça login para publicar.";
        header('Location: ../frontend/login.php');
        exit();
    }

    // Sanitizar e validar entrada
    $conteudo = trim(htmlspecialchars($_POST['conteudo']));
    $pergunta = trim(htmlspecialchars($_POST['pergunta']));
    $opcoes = array_filter(array_map('trim', $_POST['opcoes'])); // Remove opções vazias
    $duracao = intval($_POST['duracao']); // em horas

    // Validações
    if (empty($pergunta)) {
        $_SESSION['erro'] = "A pergunta da enquete é obrigatória.";
        header('Location: ../frontend/index.php');
        exit();
    }

    if (count($opcoes) < 2) {
        $_SESSION['erro'] = "A enquete deve ter pelo menos 2 opções.";
        header('Location: ../frontend/index.php');
        exit();
    }

    if (count($opcoes) > 4) {
        $_SESSION['erro'] = "A enquete pode ter no máximo 4 opções.";
        header('Location: ../frontend/index.php');
        exit();
    }

    if ($duracao < 1 || $duracao > 168) { // 1 hora a 7 dias
        $_SESSION['erro'] = "A duração deve ser entre 1 hora e 7 dias (168 horas).";
        header('Location: ../frontend/index.php');
        exit();
    }

    try {
        // Iniciar transação
        mysqli_begin_transaction($con);

        // Inserir publicação
        $stmt = $con->prepare("
            INSERT INTO publicacoes 
            (id_utilizador, conteudo, tipo, data_criacao) 
            VALUES (?, ?, 'poll', NOW())
        ");
        $stmt->bind_param("is", $_SESSION['id'], $conteudo);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao criar publicação.");
        }

        $publicacaoId = $stmt->insert_id;

        // Calcular data de expiração
        $dataExpiracao = date('Y-m-d H:i:s', strtotime("+{$duracao} hours"));

        // Inserir poll
        $stmtPoll = $con->prepare("
            INSERT INTO polls 
            (publicacao_id, pergunta, data_expiracao) 
            VALUES (?, ?, ?)
        ");
        $stmtPoll->bind_param("iss", $publicacaoId, $pergunta, $dataExpiracao);
        
        if (!$stmtPoll->execute()) {
            throw new Exception("Erro ao criar enquete.");
        }

        $pollId = $stmtPoll->insert_id;

        // Inserir opções
        $stmtOpcao = $con->prepare("
            INSERT INTO poll_opcoes 
            (poll_id, opcao_texto, ordem) 
            VALUES (?, ?, ?)
        ");

        foreach ($opcoes as $index => $opcao) {
            $stmtOpcao->bind_param("isi", $pollId, $opcao, $index);
            if (!$stmtOpcao->execute()) {
                throw new Exception("Erro ao criar opções da enquete.");
            }
        }

        // Confirmar transação
        mysqli_commit($con);

        $_SESSION['sucesso'] = "Poll criada com sucesso!";
        header('Location: ../frontend/index.php');
        exit();

    } catch (Exception $e) {
        // Reverter transação em caso de erro
        mysqli_rollback($con);
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
        header('Location: ../frontend/index.php');
        exit();
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($stmtPoll)) $stmtPoll->close();
        if (isset($stmtOpcao)) $stmtOpcao->close();
    }
} else {
    header('Location: ../frontend/index.php');
    exit();
}
?>