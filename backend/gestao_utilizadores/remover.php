<?php

session_start(); 


if(!isset($_POST["botaoRemover"]) || !isset($_SESSION["nick"]) || $_SESSION["id_tipos_utilizador"] != 2)
{
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
    
}


if($_POST["nick"] == "admin"){
    header("Location: ../../frontend/editar_utilizadores.php");
    exit();
}

require "../ligabd.php"; 


$sql_remover = "DELETE FROM utilizadores WHERE nick='".$_POST["nick"]."'";

$resultado = mysqli_query($con,$sql_remover); 
$registo = mysqli_fetch_array($resultado);

if(!$resultado){
    $_SESSION["erro"] = "Não foi possivel remover o utilizador.";
}

header("Location:../../frontend/editar_utilizadores.php");

?>