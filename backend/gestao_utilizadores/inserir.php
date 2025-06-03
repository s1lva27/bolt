<?php
session_start();

if (!isset($_POST["botaoInserir"]) || !isset($_SESSION["nick"]) || $_SESSION["id_tipos_utilizador"] != 2) {
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

require "../ligabd.php";

// Obter os dados
$nome_completo = trim($_POST["nome_completo"]);
$nick = trim($_POST["nick"]);
$password = trim($_POST["password"]);
$email = trim($_POST["email"]);
$id_tipos_utilizador = (int)$_POST["id_tipos_utilizador"];

// Validações
if (!preg_match("/^[a-zA-Z\s]{3,}$/", $nome_completo)) {
    $_SESSION["erro"] = "O nome completo deve ter pelo menos 3 caracteres e não pode conter números.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

if (!preg_match("/^[a-zA-Z0-9._]{3,16}$/", $nick)) {
    $_SESSION["erro"] = "O nome de utilizador deve ter entre 3 e 16 caracteres e só pode conter letras, números, pontos ou sublinhados.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

$sql_check_nick = "SELECT * FROM utilizadores WHERE nick='$nick'";
$result_nick = mysqli_query($con, $sql_check_nick);
if (mysqli_num_rows($result_nick) > 0) {
    $_SESSION["erro"] = "O nome de utilizador já está registado.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["erro"] = "O email é inválido.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

$sql_check_email = "SELECT * FROM utilizadores WHERE email='$email'";
$result_email = mysqli_query($con, $sql_check_email);
if (mysqli_num_rows($result_email) > 0) {
    $_SESSION["erro"] = "O email já está registado.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

// Inserção
$sql_inserir = "INSERT INTO utilizadores (nome_completo, email, palavra_passe, nick, id_tipos_utilizador) 
                VALUES ('$nome_completo', '$email', password('$password'), '$nick', '$id_tipos_utilizador')";
$result = mysqli_query($con, $sql_inserir);

if (!$result) {
    $_SESSION["erro"] = "Erro ao inserir o utilizador.";
} else {
    $_SESSION["sucesso"] = "Utilizador inserido com sucesso!";
}

header("Location: ../../frontend/editar_utilizadores.php");
?>
