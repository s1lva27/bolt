<?php
session_start();

// Redireciona se não estiver logado ou se não for administrador
if (!isset($_SESSION["nick"]) || $_SESSION["id_tipos_utilizador"] != 2) {
    header("Location: index.php");
    exit();
}

require "../backend/ligabd.php";

// Paginação
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Pesquisa e ordenação
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'a-z';

// Consulta para contar total de utilizadores
$sqlCount = "SELECT COUNT(*) AS total 
             FROM utilizadores 
             WHERE 1";

if (!empty($search)) {
    $sqlCount .= " AND (nome_completo LIKE '%$search%' 
                    OR nick LIKE '%$search%' 
                    OR email LIKE '%$search%')";
}

$resultCount = mysqli_query($con, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalUsers = $rowCount['total'];
$totalPages = ceil($totalUsers / $limit);

// Consulta para buscar os registos com paginação
$sql = "SELECT utilizadores.*, tipos_utilizador.tipo_utilizador 
FROM utilizadores
JOIN tipos_utilizador ON utilizadores.id_tipos_utilizador = tipos_utilizador.id_tipos_utilizador
";

if (!empty($search)) {
    $sql .= " AND (utilizadores.nome_completo LIKE '%$search%' 
               OR utilizadores.nick LIKE '%$search%' 
               OR utilizadores.email LIKE '%$search%')";
}

// Definir a ordenação
switch ($order) {
    case 'z-a':
        $sql .= " ORDER BY utilizadores.nome_completo DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY utilizadores.id ASC"; // IDs menores são mais antigos
        break;
    case 'newest':
        $sql .= " ORDER BY utilizadores.id DESC"; // IDs maiores são mais recentes
        break;
    default: // 'a-z' (padrão)
        $sql .= " ORDER BY utilizadores.nome_completo ASC";
        break;
}

$sql .= " LIMIT $offset, $limit";
$resultado = mysqli_query($con, $sql);

if (!$resultado) {
    $_SESSION["erro"] = "Não foi possível obter os dados dos utilizadores";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_editar_utilizadores.css">
    <link rel="icon" type="image/x-icon" href="images/favicon_orange.png">
    <title>Gestão de Utilizadores - Orange</title>
</head>

<body>

    <header>
        <div class="logo">orange.</div>
        <nav class="user">
            <ul>
                <?php if (isset($_SESSION["id"]) && $_SESSION["id_tipos_utilizador"] == 2): ?>
                    <a href="editar_utilizadores.php" class="gst-btn">Gestão</a>
                <?php endif; ?>

                <?php if (isset($_SESSION["id"])): ?>
                    <a href="perfil.php">
                        <?php echo htmlspecialchars($_SESSION['nick']); ?>
                    </a>
                    <a href="../backend/logout.php">Sair</a>
                <?php else: ?>
                    <li><a href="login.php" class="cta">Entrar</a></li>
                    <li><a href="registar.php" class="cta">Criar Conta</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Scripts -->
    <script>
        var botaoAcao = "";

        function remover(idForm) {
            document.getElementById(idForm).action = "../backend/gestao_utilizadores/remover.php";
            botaoAcao = "remover";
        }

        function gravar(idForm) {
            document.getElementById(idForm).action = "../backend/gestao_utilizadores/gravar.php";
            botaoAcao = "gravar";
        }

        function acao() {
            return botaoAcao !== "";
        }

        function inserir() {
            return true;
        }
    </script>

    <!-- Formulário de Inserção -->
    <table>
        <tr>
            <th>Nome Completo</th>
            <th>Utilizador</th>
            <th>Palavra-passe</th>
            <th>Email</th>
            <th>Tipo de utilizador</th>
            <th>Inserir</th>
        </tr>
        <tr>
            <form id="formInserir" action='../backend/gestao_utilizadores/inserir.php' method='post'
                onsubmit='return inserir()'>
                <td><input name='nome_completo' type='text'></td>
                <td><input name='nick' type='text'></td>
                <td><input name='password' type='password'></td>
                <td><input name='email' type='text'></td>
                <td>
                    <select name='id_tipos_utilizador'>
                        <option value='2'>administrador</option>
                        <option value='0'>utilizador</option>
                    </select>
                </td>
                <td><button name='botaoInserir'>Inserir</button></td>
            </form>
        </tr>
    </table>

    <!-- Mensagens -->
    <div class="error-message">
        <?php if (isset($_SESSION["erro"])): ?>
            <p>
                <?php echo $_SESSION["erro"];
                unset($_SESSION["erro"]); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="success-message">
        <?php if (isset($_SESSION["sucesso"])): ?>
            <p>
                <?php echo $_SESSION["sucesso"];
                unset($_SESSION["sucesso"]); ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Barra de pesquisa -->
    <form method="GET" action="editar_utilizadores.php">
        <input type="text" name="search" placeholder="Pesquisar utilizador..."
            value="<?php echo htmlspecialchars($search); ?>">
        <select name="order">
            <option value="a-z" <?php echo $order === 'a-z' ? 'selected' : ''; ?>>Nome (A-Z)</option>
            <option value="z-a" <?php echo $order === 'z-a' ? 'selected' : ''; ?>>Nome (Z-A)</option>
            <option value="oldest" <?php echo $order === 'oldest' ? 'selected' : ''; ?>>Mais Antigo</option>
            <option value="newest" <?php echo $order === 'newest' ? 'selected' : ''; ?>>Mais Recente</option>
        </select>
        <button type="submit">Aplicar</button>
    </form>

    <!-- Tabela de Utilizadores -->
    <table>
        <tr>
            <th>Nome Completo</th>
            <th>Utilizador</th>
            <th>Palavra-passe</th>
            <th>Email</th>
            <th>Tipo de utilizador</th>
            <th>Ações</th>
        </tr>
        <?php

        if (mysqli_num_rows($resultado) == 0) {
            echo "<tr><td colspan='6'>Nenhum utilizador encontrado.</td></tr>";
        }

        while ($registo = mysqli_fetch_array($resultado)) {
            // Não mostrar registo de "admin" se o utilizador logado não for admin
            if ($registo["nick"] == "admin" && $_SESSION["nick"] != "admin") {
                continue;
            }
            echo "
                <form id='form" . $registo["id"] . "' action='' method='post' onsubmit='return acao()'>
                    <tr>
                        <td hidden>
                            <input name='id' type='text' value='" . $registo["id"] . "'>
                        </td>
                        <td><input readonly name='nome_completo' type='text' value='" . $registo["nome_completo"] . "'></td>
                        <td><input readonly name='nick' type='text' value='" . $registo["nick"] . "'></td>
                        <td><input name='password' type='password'></td>
                        <td><input name='email' type='text' value='" . $registo["email"] . "'></td>
                        <td>
                            <select name='id_tipos_utilizador'>
                                <option value='2' " . (($registo["id_tipos_utilizador"] == '2') ? "selected" : "") . ">administrador</option>
                                <option value='0' " . (($registo["id_tipos_utilizador"] == '0') ? "selected" : "") . ">utilizador</option>
                             </select>
                        </td>
                        <td style='display: flex; justify-content: center; align-items: center; gap: 10px;'>
                            <button name='botaoRemover' onclick='remover(\"form" . $registo["id"] . "\")' " . (($registo["nick"] == "admin") ? "disabled" : "") . ">Remover</button>
                            <button name='botaoGravar' onclick='gravar(\"form" . $registo["id"] . "\")'>Gravar</button>
                        </td>
                    </tr>
                </form>";
        }
        ?>
    </table>

</body>

</html>