<?php
session_start();
include "ligabd.php";

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$pergunta = trim($_POST['pergunta'] ?? '');
$conteudo = trim($_POST['conteudo'] ?? '');
$opcoes = $_POST['opcoes'] ?? [];
$duracao = intval($_POST['duracao'] ?? 24);

// Validações
if (empty($pergunta)) {
    echo json_encode(['success' => false, 'message' => 'A pergunta é obrigatória']);
    exit;
}

if (count($opcoes) < 2) {
    echo json_encode(['success' => false, 'message' => 'São necessárias pelo menos 2 opções']);
    exit;
}

// Limitar a 4 opções
$opcoes = array_slice($opcoes, 0, 4);

// Calcular data de expiração
$dataExpiracao = date('Y-m-d H:i:s', strtotime("+{$duracao} hours"));

// Iniciar transação
$con->begin_transaction();

try {
    // 1. Criar a publicação
    $stmt = $con->prepare("INSERT INTO publicacoes (id_utilizador, conteudo, tipo, data_criacao) 
                          VALUES (?, ?, 'poll', NOW())");
    $stmt->bind_param("is", $_SESSION['id'], $conteudo);
    $stmt->execute();
    $publicacaoId = $con->insert_id;

    // 2. Criar a poll
    $stmt = $con->prepare("INSERT INTO polls (publicacao_id, pergunta, data_expiracao, total_votos) 
                          VALUES (?, ?, ?, 0)");
    $stmt->bind_param("iss", $publicacaoId, $pergunta, $dataExpiracao);
    $stmt->execute();
    $pollId = $con->insert_id;

    // 3. Adicionar opções
    $stmt = $con->prepare("INSERT INTO poll_opcoes (poll_id, opcao_texto, votos, ordem) 
                          VALUES (?, ?, 0, ?)");
    
    $ordem = 1;
    foreach ($opcoes as $opcao) {
        $opcao = trim($opcao);
        if (!empty($opcao)) {
            $stmt->bind_param("isi", $pollId, $opcao, $ordem);
            $stmt->execute();
            $ordem++;
        }
    }

    $con->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Poll criada com sucesso',
        'poll_id' => $pollId,
        'publicacao_id' => $publicacaoId
    ]);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao criar poll: ' . $e->getMessage()
    ]);
}
?>