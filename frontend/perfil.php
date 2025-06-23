<?php
session_start();
require "../backend/ligabd.php";


function getPostImages($con, $postId)
{
    $sql = "SELECT url, content_warning FROM publicacao_medias 
            WHERE publicacao_id = $postId
            ORDER BY ordem ASC";
    $result = mysqli_query($con, $sql);
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
}

// Função para transformar URLs em links clicáveis
function makeLinksClickable($text)
{
    $pattern = '/(https?:\/\/[^\s]+)/';
    $linkedText = preg_replace($pattern, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
    return $linkedText;
}

function isPostSaved($con, $userId, $postId)
{
    $sql = "SELECT * FROM publicacao_salvas
            WHERE utilizador_id = $userId AND publicacao_id = $postId";
    $result = mysqli_query($con, $sql);
    return mysqli_num_rows($result) > 0;
}

function getCommentCount($con, $postId)
{
    $sql = "SELECT COUNT(*) as count FROM comentarios WHERE id_publicacao = $postId";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}

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
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Orange</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style_perfil.css">
    <link rel="stylesheet" href="css/style_index.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon_orange.png">

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
                    <p class="nickperfil">@<?php echo htmlspecialchars($userData['nick']); ?>
                    </p>

                    <div class="contact-info">
                        <?php if (!empty($perfilData['cidade']) || !empty($perfilData['pais'])): ?>
                            <span>
                                <i data-lucide="map-pin"></i>
                                <?php
                                $location = [];
                                if (!empty($perfilData['cidade'])) {
                                    $location[] = htmlspecialchars($perfilData['cidade']);
                                }
                                if (!empty($perfilData['pais'])) {
                                    $location[] = htmlspecialchars($perfilData['pais']);
                                }
                                echo implode(', ', $location);
                                ?>
                            </span>
                        <?php endif; ?>
                        <span>
                            <i data-lucide="mail"></i>
                            <?php echo htmlspecialchars($userData['email']); ?>
                            </p>
                        </span>
                    </div>
                </div>

                <?php if ((int) $userId != (int) $_SESSION["id"]): ?>
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
            <div class="social-links" style="margin-top: 17px;">
                <?php if (!empty($perfilData['x'])): ?>
                    <a href="<?php echo htmlspecialchars($perfilData['x']); ?>" target="_blank" class="social-link">
                        <i data-lucide="twitter"></i>
                    </a>
                <?php endif; ?>

                <?php if (!empty($perfilData['github'])): ?>
                    <a href="<?php echo htmlspecialchars($perfilData['github']); ?>" target="_blank" class="social-link">
                        <i data-lucide="github"></i>
                    </a>
                <?php endif; ?>

                <?php if (!empty($perfilData['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($perfilData['linkedin']); ?>" target="_blank" class="social-link">
                        <i data-lucide="linkedin"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>



        <!-- Atividade Recente -->
        <!-- Atividade Recente -->
        <div class="activity-card">
            <h2>Atividade Recente</h2>
            <div class="posts">
                <?php if (mysqli_num_rows($resultPublicacoes) > 0): ?>
                    <?php while ($publicacao = mysqli_fetch_assoc($resultPublicacoes)):
                        // Verificar se o usuário logado já deu like
                        $likedClass = '';
                        $savedClass = '';
                        if (isset($_SESSION['id'])) {
                            $currentUserId = $_SESSION['id'];
                            $publicacaoId = $publicacao['id_publicacao'];

                            // Verificar like
                            $checkSql = "SELECT * FROM publicacao_likes 
                                     WHERE publicacao_id = $publicacaoId AND utilizador_id = $currentUserId";
                            $checkResult = mysqli_query($con, $checkSql);
                            if (mysqli_num_rows($checkResult) > 0) {
                                $likedClass = 'liked';
                            }

                            // Verificar se está salvo
                            if (isPostSaved($con, $currentUserId, $publicacaoId)) {
                                $savedClass = 'saved';
                            }
                        }
                        ?>
                        <article class="post" data-post-id="<?php echo $publicacao['id_publicacao']; ?>">
                            <div class="post-header">
                                <a href="perfil.php?id=<?php echo $userId; ?>">
                                    <img src="images/perfil/<?php echo $fotoPerfil; ?>" alt="User" class="profile-pic">
                                </a>
                                <div class="post-info">
                                    <div>
                                        <a href="perfil.php?id=<?php echo $userId; ?>" class="profile-link">
                                            <h3><?php echo htmlspecialchars($userData['nome_completo']); ?></h3>
                                        </a>
                                        <p>@<?php echo htmlspecialchars($userData['nick']); ?></p>
                                    </div>
                                    <span class="timestamp">
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
                                    </span>
                                </div>
                            </div>
                            <div class="post-content">
                                <p><?php echo nl2br(makeLinksClickable(htmlspecialchars($publicacao['conteudo']))); ?></p>

                                <?php
                                // Buscar imagens da publicação
                                $images = getPostImages($con, $publicacao['id_publicacao']);
                                if (!empty($images)): ?>
                                    <div class="post-images">
                                        <?php
                                        $imageCount = count($images);
                                        $gridClass = '';
                                        if ($imageCount == 1)
                                            $gridClass = 'single';
                                        elseif ($imageCount == 2)
                                            $gridClass = 'double';
                                        elseif ($imageCount == 3)
                                            $gridClass = 'triple';
                                        else
                                            $gridClass = 'multiple';
                                        ?>
                                        <div class="images-grid <?php echo $gridClass; ?>"
                                            data-post-id="<?php echo $publicacao['id_publicacao']; ?>">
                                            <?php
                                            $displayCount = min($imageCount, 4);
                                            for ($i = 0; $i < $displayCount; $i++):
                                                $image = $images[$i];
                                                ?>
                                                <div class="image-item"
                                                    onclick="openImageModal(<?php echo $publicacao['id_publicacao']; ?>, <?php echo $i; ?>)">
                                                    <img src="images/publicacoes/<?php echo htmlspecialchars($image['url']); ?>"
                                                        alt="Imagem da publicação" class="post-image">
                                                    <?php if ($i == 3 && $imageCount > 4): ?>
                                                        <div class="more-images-overlay">
                                                            +<?php echo $imageCount - 4; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="post-actions">
                                <button class="like-btn <?php echo $likedClass; ?>"
                                    data-publicacao-id="<?php echo $publicacao['id_publicacao']; ?>">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span class="like-count"><?php echo $publicacao['likes']; ?></span>
                                </button>
                                <button class="comment-btn"
                                    onclick="openCommentsModal(<?php echo $publicacao['id_publicacao']; ?>)">
                                    <i class="fas fa-comment"></i>
                                    <span
                                        class="comment-count"><?php echo getCommentCount($con, $publicacao['id_publicacao']); ?></span>
                                </button>
                                <button><i class="fas fa-share"></i></button>
                                <button class="save-btn <?php echo $savedClass; ?>"
                                    data-publicacao-id="<?php echo $publicacao['id_publicacao']; ?>">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-activity">Este utilizador ainda não fez nenhuma publicação.</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Modal de Comentários -->
        <div id="commentsModal" class="modal-overlay">
            <div class="comment-modal">
                <div class="modal-post" id="modalPostContent">
                    <!-- Conteúdo preenchido via JS -->
                </div>
                <div class="modal-comments">
                    <div class="comments-list" id="commentsList">
                        <!-- Comentários carregados aqui -->
                    </div>
                    <form class="comment-form" id="commentForm">
                        <input type="hidden" id="currentPostId" value="">
                        <input type="text" class="comment-input" id="commentInput"
                            placeholder="Adicione um comentário..." required>
                        <button type="submit" class="comment-submit">Publicar</button>
                    </form>
                </div>
                <button class="close-button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Modal para imagem expandida -->
        <div id="imageModal" class="image-modal">
            <div class="image-modal-content">
                <button class="close-image-modal">&times;</button>
                <img id="modalImage" class="modal-image" src="" alt="Imagem expandida">
                <div class="image-modal-nav">
                    <button id="prevImageBtn" class="modal-nav-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span id="imageCounter" class="image-counter">1 / 1</span>
                    <button id="nextImageBtn" class="modal-nav-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div id="toast" class="toast">
            <div class="toast-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="toast-content">
                <p id="toast-message">Mensagem aqui</p>
            </div>
        </div>
    </main>

    <script>
        // Inicializa os ícones Lucide
        lucide.createIcons();
        // Funcionalidades de interação
        // Like
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function () {
                const publicacaoId = this.getAttribute('data-publicacao-id');
                const likeCount = this.querySelector('.like-count');

                fetch('../backend/like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_publicacao=${publicacaoId}`
                })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'liked') {
                            this.classList.add('liked');
                            likeCount.textContent = parseInt(likeCount.textContent) + 1;
                        } else if (data === 'unliked') {
                            this.classList.remove('liked');
                            likeCount.textContent = parseInt(likeCount.textContent) - 1;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Salvar publicação
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function () {
                const publicacaoId = this.getAttribute('data-publicacao-id');
                const isCurrentlySaved = this.classList.contains('saved');

                fetch('../backend/save_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_publicacao=${publicacaoId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.action === 'saved') {
                                this.classList.add('saved');
                                showToast('Adicionado aos itens salvos');
                            } else {
                                this.classList.remove('saved');
                                showToast('Removido dos itens salvos');
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Sistema de visualização de imagens
        let currentImageModal = {
            postId: null,
            currentIndex: 0,
            images: []
        };

        function openImageModal(postId, imageIndex = 0) {
            // Busca as imagens diretamente do elemento DOM
            const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
            if (!postElement) return;

            const images = [];
            const imageElements = postElement.querySelectorAll('.post-image');
            imageElements.forEach(img => {
                images.push({
                    url: img.src.split('/').pop(), // Extrai apenas o nome do arquivo
                    content_warning: 'none'
                });
            });

            if (images.length === 0) return;

            currentImageModal = {
                postId,
                currentIndex: imageIndex,
                images
            };

            showImageInModal();
            document.getElementById('imageModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function showImageInModal() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const imageCounter = document.getElementById('imageCounter');
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');

            const currentImage = currentImageModal.images[currentImageModal.currentIndex];
            modalImage.src = `images/publicacoes/${currentImage.url}`;

            imageCounter.textContent = `${currentImageModal.currentIndex + 1} / ${currentImageModal.images.length}`;

            prevBtn.disabled = currentImageModal.currentIndex === 0;
            nextBtn.disabled = currentImageModal.currentIndex === currentImageModal.images.length - 1;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function navigateImage(direction) {
            if (direction === 'prev' && currentImageModal.currentIndex > 0) {
                currentImageModal.currentIndex--;
            } else if (direction === 'next' && currentImageModal.currentIndex < currentImageModal.images.length - 1) {
                currentImageModal.currentIndex++;
            }
            showImageInModal();
        }

        // Event listeners para o modal
        document.querySelector('.close-image-modal').addEventListener('click', closeImageModal);
        document.getElementById('prevImageBtn').addEventListener('click', () => navigateImage('prev'));
        document.getElementById('nextImageBtn').addEventListener('click', () => navigateImage('next'));

        document.getElementById('imageModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            const modal = document.getElementById('imageModal');
            if (modal.style.display === 'flex') {
                if (e.key === 'Escape') {
                    closeImageModal();
                } else if (e.key === 'ArrowLeft') {
                    navigateImage('prev');
                } else if (e.key === 'ArrowRight') {
                    navigateImage('next');
                }
            }
        });

        // Modal de comentários
        const modal = document.getElementById('commentsModal');
        const closeButton = modal.querySelector('.close-button');

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        closeButton.addEventListener('click', closeModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        let currentPostId = null;

        function openCommentsModal(postId) {
            currentPostId = postId;

            // Primeiro, tente pegar o elemento da publicação diretamente do DOM
            const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
            if (postElement) {
                // Clone o elemento da publicação para mostrar no modal
                const postClone = postElement.cloneNode(true);

                // Remova os botões de ação para economizar espaço
                const actions = postClone.querySelector('.post-actions');
                if (actions) actions.remove();

                // Adicione ao modal
                document.getElementById('modalPostContent').innerHTML = '';
                document.getElementById('modalPostContent').appendChild(postClone);

                // Carregue os comentários
                loadComments(postId);

                // Mostre o modal
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } else {
                // Fallback: carregue via AJAX se não encontrar no DOM
                fetch(`../backend/get_post.php?id=${postId}`)
                    .then(response => response.json())
                    .then(post => {
                        const dataCriacao = new Date(post.data_criacao);
                        const dataFormatada = `${dataCriacao.getDate().toString().padStart(2, '0')}-${(dataCriacao.getMonth() + 1).toString().padStart(2, '0')}-${dataCriacao.getFullYear()} ${dataCriacao.getHours().toString().padStart(2, '0')}:${dataCriacao.getMinutes().toString().padStart(2, '0')}`;

                        let imagesHTML = '';
                        if (post.images && post.images.length > 0) {
                            // Construir HTML para as imagens (igual ao do index.php)
                            const imageCount = post.images.length;
                            const gridClass = imageCount === 1 ? 'single' :
                                imageCount === 2 ? 'double' :
                                    imageCount === 3 ? 'triple' : 'multiple';

                            imagesHTML = `<div class="post-images">
                        <div class="images-grid ${gridClass}">
                            ${post.images.slice(0, 4).map((image, i) => `
                                <div class="image-item">
                                    <img src="images/publicacoes/${image.url}" alt="Imagem da publicação" class="post-image">
                                    ${i === 3 && imageCount > 4 ? `
                                        <div class="more-images-overlay">
                                            +${imageCount - 4}
                                        </div>
                                    ` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>`;
                        }

                        document.getElementById('modalPostContent').innerHTML = `
                    <div class="post">
                        <div class="post-header">
                            <a href="perfil.php?id=${post.id_utilizador}">
                                <img src="images/perfil/${post.foto_perfil || 'default-profile.jpg'}" alt="User" class="profile-pic">
                            </a>
                            <div class="post-info">
                                <a href="perfil.php?id=${post.id_utilizador}" class="profile-link">
                                    <h3>${post.nick}</h3>
                                </a>
                                <p>${post.ocupacao || 'Utilizador'}</p>
                                <span class="timestamp">${dataFormatada}</span>
                            </div>
                        </div>
                        <div class="post-content">
                            <p>${post.conteudo.replace(/\n/g, '<br>')}</p>
                            ${imagesHTML}
                        </div>
                    </div>
                `;

                        loadComments(postId);
                    })
                    .catch(error => {
                        console.error('Error loading post:', error);
                        document.getElementById('modalPostContent').innerHTML = '<p>Erro ao carregar a publicação.</p>';
                    });

                document.getElementById('currentPostId').value = postId;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function loadComments(postId) {
            fetch(`../backend/get_comments.php?post_id=${postId}`)
                .then(response => response.json())
                .then(comments => {
                    const commentsList = document.getElementById('commentsList');
                    commentsList.innerHTML = '';

                    comments.forEach(comment => {
                        const dataComentario = new Date(comment.data);
                        const dataComentarioFormatada = `${dataComentario.getDate().toString().padStart(2, '0')}-${(dataComentario.getMonth() + 1).toString().padStart(2, '0')}-${dataComentario.getFullYear()} ${dataComentario.getHours().toString().padStart(2, '0')}:${dataComentario.getMinutes().toString().padStart(2, '0')}`;

                        const commentItem = document.createElement('div');
                        commentItem.className = 'comment-item';
                        commentItem.innerHTML = `
                            <a href="perfil.php?id=${comment.utilizador_id}">
                                <img src="images/perfil/${comment.foto_perfil || 'default-profile.jpg'}" alt="User" class="comment-avatar">
                            </a>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <a href="perfil.php?id=${comment.utilizador_id}" class="profile-link">
                                        <span class="comment-username">${comment.nick}</span>
                                    </a>
                                    <span class="comment-time">${dataComentarioFormatada}</span>
                                </div>
                                <p class="comment-text">${comment.conteudo}</p>
                            </div>
                        `;
                        commentsList.appendChild(commentItem);
                    });
                });
        }

        // Envio de comentário
        document.getElementById('commentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const commentInput = document.getElementById('commentInput');
            const content = commentInput.value.trim();

            if (content && currentPostId) {
                const formData = new FormData();
                formData.append('post_id', currentPostId);
                formData.append('content', content);

                fetch('../backend/add_comment.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            commentInput.value = '';
                            loadComments(currentPostId);

                            // Atualiza contador de comentários
                            const commentCount = document.querySelector(`.comment-btn[onclick*="${currentPostId}"] .comment-count`);
                            if (commentCount) {
                                commentCount.textContent = parseInt(commentCount.textContent) + 1;
                            }
                        }
                    });
            }
        });

        // Mostrar toast
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;

            toast.style.display = 'flex';
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }, 3000);
        }
    </script>

</body>

</html>