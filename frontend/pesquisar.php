<?php
session_start();
require "../backend/ligabd.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verifica se o termo de pesquisa existe
$termo = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

// Consulta segura usando prepared statements
$sql = "SELECT * FROM utilizadores 
        WHERE nome_completo LIKE CONCAT('%', ?, '%') 
        OR nick LIKE CONCAT('%', ?, '%')";

$stmt = $con->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da query: " . $con->error);
}

$stmt->bind_param("ss", $termo, $termo);
$stmt->execute();
$resultado = $stmt->get_result();

// Debug: Mostrar a query final
echo "<!-- Query executada: " . $sql . " -->";
echo "<!-- Termo pesquisado: " . $termo . " -->";
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/style_pesquisar.css">
    <title>Resultados</title>
    <style>
        .resultado {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <h1>Resultados para "
        <?php echo htmlspecialchars($termo); ?>"
    </h1>

    <?php if ($resultado->num_rows > 0): ?>
        <?php while ($linha = $resultado->fetch_assoc()): ?>
            <div class="resultado">
                <h3>
                    <?php echo htmlspecialchars($linha['nome_completo']); ?>
                </h3>
                <p>Nick: @
                    <?php echo htmlspecialchars($linha['nick']); ?>
                </p>
                <a href="perfil.php?id=<?php echo $linha['id']; ?>" class="btn-ver-perfil">Ver Perfil</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum resultado encontrado.</p>
    <?php endif; ?>
</body>

</html>