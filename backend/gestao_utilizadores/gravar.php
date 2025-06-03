<?php
session_start();




if (!isset($_POST["botaoGravar"]) || !isset($_SESSION["nick"]) || $_SESSION["id_tipos_utilizador"] != 2) {
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

require "../ligabd.php";


// Obter os dados
$g_nome_completo = trim($_POST["nome_completo"]);
$g_nick = trim($_POST["nick"]);
$g_password = trim($_POST["password"]);
$g_email = trim($_POST["email"]);
$g_id_tipos_utilizador = (int)$_POST["id_tipos_utilizador"];

// Validações
if (!preg_match("/^[a-zA-Z\s]{3,}$/", $g_nome_completo)) {
    $_SESSION["erro"] = "O nome completo deve ter pelo menos 3 caracteres e não pode conter números.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

if (!filter_var($g_email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["erro"] = "O email é inválido.";
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

// Atualização
$g_password_linha = strlen($g_password) == 0 ? "" : "palavra_passe=password('$g_password'),";

$sql_gravar = "UPDATE utilizadores SET 
                $g_password_linha 
                email='$g_email', 
                id_tipos_utilizador='$g_id_tipos_utilizador' 
                WHERE nick='$g_nick'";
$resultado = mysqli_query($con, $sql_gravar);

if (!$resultado) {
    $_SESSION["erro"] = "Erro ao atualizar o utilizador.";
} else {
    $_SESSION["sucesso"] = "Utilizador atualizado com sucesso!";
}

header("Location: ../../frontend/editar_utilizadores.php");
?>
