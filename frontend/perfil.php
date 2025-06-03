<?php
session_start();
require "../backend/ligabd.php";

// Verificar se o utilizador está autenticado
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Verificar se foi passado um ID na URL
if (isset($_GET['id'])) {
    $userId = intval($_GET['id']); // Convertemos para inteiro por segurança
} else {
    $userId = $_SESSION["id"]; // Padrão: perfil do próprio usuário
}

// Verificar se o ID é válido
$sql_check = "SELECT id FROM utilizadores WHERE id = ?";
$stmt_check = $con->prepare($sql_check);
$stmt_check->bind_param("i", $userId);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die("Utilizador não encontrado!");
}



// Buscar informações do utilizador na tabela "utilizadores"
$sqlUser = "SELECT * FROM utilizadores WHERE id = $userId";
$resultUser = mysqli_query($con, $sqlUser);
$userData = mysqli_fetch_assoc($resultUser);

// Buscar informações do perfil na tabela "perfis"
$sqlPerfil = "SELECT * FROM perfis WHERE id_utilizador = $userId";
$resultPerfil = mysqli_query($con, $sqlPerfil);
$perfilData = mysqli_fetch_assoc($resultPerfil);

// Definir imagem de perfil padrão, se necessário
$fotoPerfil = !empty($perfilData['foto_perfil']) ? $perfilData['foto_perfil'] : 'images/perfil/default-profile.jpg';

$sqlPublicacoes = "SELECT * FROM publicacoes 
                  WHERE id_utilizador = $userId 
                  ORDER BY data_criacao DESC";
$resultPublicacoes = mysqli_query($con, $sqlPublicacoes);
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Orange</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style_perfil.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../images/favicon/favicon_orange.png">

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <!-- Cabeçalho -->

    <?php
    require("parciais/header.php");
    ?>

    <!-- Perfil Header -->
    <div class="profile-header">
        <div class="cover-photo" id="cover-photo">
            <?php
            $fotoCapa = !empty($perfilData['foto_capa']) ? "../frontend/images/capa/" . $perfilData['foto_capa'] : "images/default-capa.png";
            ?>
            <style>
                .cover-photo {
                    background-image: url("<?php echo $fotoCapa; ?>");
                }
            </style>
            <form action="../backend/upload_capa.php" method="POST" enctype="multipart/form-data">
                <label for="fotoInput" class="cover-photo-btn">
                    <i data-lucide="camera"></i>
                    Alterar Capa
                    <input type="file" name="foto" id="fotoInput" accept="image/*" style="display: none;" required>
                    <button type="submit" name="submit" id="uploadForm" style="display: none;"></button>
                </label>
                <script>
                    document.getElementById('fotoInput').addEventListener('change', function () {
                        document.getElementById('uploadForm').click();
                    });
                </script>
            </form>
        </div>


        <div class="profile-photo-container">
            <div class="profile-photo-wrapper">
                <img src="<?php echo ('images/perfil/' . $fotoPerfil); ?>" alt="Foto de Perfil" class="profile-photo"
                    alt="Foto de Perfil" class="profile-photo">

            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <main>
        <!-- Informações do Perfil -->
        <div class="profile-card">
            <div class="profile-header-content">
                <div class="profile-info">
                    <h1>
                        <?php echo htmlspecialchars($userData['nome_completo']); ?>
                    </h1>
                    <p class="nickperfil">@
                        <?php echo htmlspecialchars($userData['nick']); ?>
                    </p>

                    <div class="contact-info">
                        <span>
                            <i data-lucide="map-pin"></i>
                            <?php echo htmlspecialchars($perfilData['cidade']); ?>,
                            <?php echo htmlspecialchars($perfilData['pais']); ?>
                        </span>
                        <span>
                            <i data-lucide="mail"></i>
                            <?php echo htmlspecialchars($userData['email']); ?>
                            </p>
                        </span>
                    </div>
                </div>

                <?php if ($userId !== $_SESSION["id"]): ?>
                    <?php
                    // Verificar se o utilizador autenticado já segue o perfil visitado
                    $sqlCheckFollow = "SELECT * FROM seguidores WHERE id_seguidor = ? AND id_seguido = ?";
                    $stmtFollow = $con->prepare($sqlCheckFollow);
                    $stmtFollow->bind_param("ii", $_SESSION["id"], $userId);
                    $stmtFollow->execute();
                    $resultFollow = $stmtFollow->get_result();
                    $isFollowing = $resultFollow->num_rows > 0;
                    ?>

                    <form action="../backend/seguir.php" method="POST">
                        <input type="hidden" name="id_seguido" value="<?php echo $userId; ?>">
                        <?php if ($isFollowing): ?>
                            <button type="submit" name="acao" value="unfollow" class="unfollow-btn">Deixar de
                                Seguir</button>
                        <?php else: ?>
                            <button type="submit" name="acao" value="follow" class="follow-btn">Seguir</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>

                <?php if ($userId == $_SESSION["id"]): ?>
                    <a href="editar_perfil.php#profile-info" class="edit-profile-btn">Editar Perfil</a>
                <?php endif; ?>

            </div>

            <div class="bio">
                <?php echo htmlspecialchars($perfilData['biografia']); ?>
            </div>

            <?php
            // Contar seguidores
            $sqlSeguidores = "SELECT COUNT(*) AS total FROM seguidores WHERE id_seguido = ?";
            $stmtSeguidores = $con->prepare($sqlSeguidores);
            $stmtSeguidores->bind_param("i", $userId);
            $stmtSeguidores->execute();
            $resultSeguidores = $stmtSeguidores->get_result();
            $totalSeguidores = $resultSeguidores->fetch_assoc()["total"];

            // Contar seguindo
            $sqlSeguindo = "SELECT COUNT(*) AS total FROM seguidores WHERE id_seguidor = ?";
            $stmtSeguindo = $con->prepare($sqlSeguindo);
            $stmtSeguindo->bind_param("i", $userId);
            $stmtSeguindo->execute();
            $resultSeguindo = $stmtSeguindo->get_result();
            $totalSeguindo = $resultSeguindo->fetch_assoc()["total"];
            ?>


            <div class="stats">
                <div class="stat">
                    <i data-lucide="users"></i>
                    <span>
                        <?php echo $totalSeguidores; ?> seguidores ·
                        <?php echo $totalSeguindo; ?> seguindo
                    </span>
                </div>
            </div>

            <div class="stat">
                <i data-lucide="calendar"></i>

                <?php
                $dataFormatada = date("d  F  Y", strtotime($userData["data_criacao"]));

                // Converter o nome do mês para português
                $meses = [
                    "January" => "Jan",
                    "February" => "Fev",
                    "March" => "Mar",
                    "April" => "Abr",
                    "May" => "Mai",
                    "June" => "Jun",
                    "July" => "Jul",
                    "August" => "Ago",
                    "September" => "Set",
                    "October" => "Out",
                    "November" => "Nov",
                    "December" => "Dez"
                ];

                foreach ($meses as $ingles => $portugues) {
                    $dataFormatada = str_replace($ingles, $portugues, $dataFormatada);
                }
                ?>

                <span>
                    <?php echo htmlspecialchars($dataFormatada); ?>
                </span>
            </div>
        </div>

        <div class="social-links">
            <a href="<?php echo htmlspecialchars($perfilData['x']); ?>" target="_blank" class="social-link">
                <i data-lucide="twitter"></i>
            </a>
            <a href="<?php echo htmlspecialchars($perfilData['github']); ?>" target="_blank" class="social-link">
                <i data-lucide="github"></i>
            </a>
            <a href="<?php echo htmlspecialchars($perfilData['linkedin']); ?>" target="_blank" class="social-link">
                <i data-lucide="linkedin"></i>
            </a>
        </div>
        </div>

        <!-- Atividade Recente -->
        <div class="activity-card">
            <h2>Atividade Recente</h2>
            <div class="activity-list">
                <?php if (mysqli_num_rows($resultPublicacoes) > 0): ?>
                    <?php while ($publicacao = mysqli_fetch_assoc($resultPublicacoes)): ?>
                        <div class="activity-item">
                            <img src="<?php echo $fotoPerfil; ?>" alt="Post" class="activity-image">
                            <div class="activity-content">
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($publicacao['conteudo'])); ?>
                                </div>
                                <div class="activity-time">
                                    <?php
                                    $dataPublicacao = date("d F Y H:i", strtotime($publicacao['data_criacao']));
                                    $meses = [
                                        "January" => "Jan",
                                        "February" => "Fev",
                                        "March" => "Mar",
                                        "April" => "Abr",
                                        "May" => "Mai",
                                        "June" => "Jun",
                                        "July" => "Jul",
                                        "August" => "Ago",
                                        "September" => "Set",
                                        "October" => "Out",
                                        "November" => "Nov",
                                        "December" => "Dez"
                                    ];
                                    foreach ($meses as $en => $pt) {
                                        $dataPublicacao = str_replace($en, $pt, $dataPublicacao);
                                    }
                                    echo $dataPublicacao;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-activity">Este utilizador ainda não fez nenhuma publicação.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Inicializa os ícones Lucide
        lucide.createIcons();
    </script>
</body>

</html>