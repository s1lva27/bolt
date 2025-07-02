<?php
session_start();

if (!isset($_POST["botaoGravar"]) || !isset($_SESSION["nick"]) || $_SESSION["id_tipos_utilizador"] != 2) {
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

require "../ligabd.php";

// Obter e sanitizar os dados
$id = (int)$_POST["id"];
$email = trim($_POST["email"]);
$password = trim($_POST["password"]);
$id_tipos_utilizador = (int)$_POST["id_tipos_utilizador"];

// Validações
$errors = [];

// Validar ID
if ($id <= 0) {
    $errors[] = "ID de utilizador inválido.";
}

// Validar email
if (empty($email)) {
    $errors[] = "O email é obrigatório.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "O formato do email é inválido.";
}

// Validar password (se fornecida)
if (!empty($password) && strlen($password) < 6) {
    $errors[] = "A palavra-passe deve ter pelo menos 6 caracteres.";
}

// Validar tipo de utilizador
if (!in_array($id_tipos_utilizador, [0, 2])) {
    $errors[] = "Tipo de utilizador inválido.";
}

// Se há erros de validação, retornar
if (!empty($errors)) {
    $_SESSION["erro"] = implode(" ", $errors);
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

// Verificar se o utilizador existe
$sql_check_user = "SELECT nick FROM utilizadores WHERE id = $id";
$result_check = mysqli_query($con, $sql_check_user);

if (!$result_check || mysqli_num_rows($result_check) == 0) {
    $_SESSION["erro"] = "Utilizador não encontrado.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

$user_data = mysqli_fetch_assoc($result_check);
$nick = $user_data['nick'];

// Verificar se email já está em uso por outro utilizador
$email_escaped = mysqli_real_escape_string($con, $email);
$sql_check_email = "SELECT id FROM utilizadores WHERE email = '$email_escaped' AND id != $id";
$result_email = mysqli_query($con, $sql_check_email);

if (mysqli_num_rows($result_email) > 0) {
    $_SESSION["erro"] = "O email '$email' já está em uso por outro utilizador.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

// Proteger o admin principal de ser rebaixado
if ($nick == "admin" && $id_tipos_utilizador != 2) {
    $_SESSION["erro"] = "Não é possível alterar o tipo do utilizador admin principal.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

try {
    // Preparar query de atualização
    $password_clause = "";
    if (!empty($password)) {
        $password_escaped = mysqli_real_escape_string($con, $password);
        $password_clause = "palavra_passe = password('$password_escaped'),";
    }

    $sql_gravar = "UPDATE utilizadores SET 
                   $password_clause 
                   email = '$email_escaped', 
                   id_tipos_utilizador = $id_tipos_utilizador 
                   WHERE id = $id";

    $resultado = mysqli_query($con, $sql_gravar);

    if (!$resultado) {
        throw new Exception("Erro ao atualizar utilizador: " . mysqli_error($con));
    }

    $tipo_texto = ($id_tipos_utilizador == 2) ? "administrador" : "utilizador";
    $password_msg = !empty($password) ? " e palavra-passe" : "";
    
    $_SESSION["sucesso"] = "Utilizador '$nick' atualizado com sucesso! Tipo: $tipo_texto$password_msg.";

} catch (Exception $e) {
    $_SESSION["erro"] = $e->getMessage();
    error_log("Erro ao atualizar utilizador: " . $e->getMessage());
}

header("Location: ../../frontend/editar_utilizadores.php");
exit();
?>