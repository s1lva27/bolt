<?php session_start();
include "../backend/ligabd.php";

// Função para transformar URLs em links clicáveis
function makeLinksClickable($text)
{
    // Primeiro transforma URLs em links
    $pattern = '/(https?:\/\/[^\s]+)/';
    $linkedText = preg_replace($pattern, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);

    // Depois aplica segurança
    return $linkedText;
}

function isPostSaved($con, $userId, $postId)
{
    $sql = "SELECT * FROM publicacao_salvas
            WHERE utilizador_id = $userId AND publicacao_id = $postId";
    $result = mysqli_query($con, $sql);
    return mysqli_num_rows($result) > 0;
}

function getPostImages($con, $postId)
{
    $sql = "SELECT url, content_warning, tipo FROM publicacao_medias 
            WHERE publicacao_id = $postId
            ORDER BY ordem ASC";
    $result = mysqli_query($con, $sql);
    $medias = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $medias[] = $row;
    }
    return $medias;
}

if (!empty($_SESSION)) {
    $userId = $_SESSION["id"];
    $sqlPerfil = "SELECT * FROM perfis WHERE id_utilizador = $userId";
    $resultPerfil = mysqli_query($con, $sqlPerfil);
    $perfilData = mysqli_fetch_assoc($resultPerfil);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orange</title>
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon_orange.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos adicionais para os links de perfil */
        .profile-link {
            text-decoration: none;
            color: var(--text-light);
            display: inline-block;
            transition: color 0.2s ease;
        }

        .profile-link:hover {
            color: var(--color-primary);
            text-decoration: underline;
        }

        .post-info .profile-link h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .modal-post .profile-link {
            color: var(--text-light);
        }

        .comment-content .profile-link {
            color: var(--text-light);
            font-weight: 600;
        }

        .comment-content .profile-link:hover {
            color: var(--color-primary);
        }

        /* Sistema de múltiplas imagens */
        .multiple-image-preview {
            margin-top: 15px;
            display: none;
            background: var(--bg-input);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid var(--border-light);
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .preview-count {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .clear-all-btn {
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .clear-all-btn:hover {
            background: var(--color-primary-dark);
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
        }

        .preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            background: var(--bg-card);
            border: 1px solid var(--border-light);
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s ease;
        }

        .preview-item:hover .preview-image {
            transform: scale(1.05);
        }

        .remove-image-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s ease;
        }

        .remove-image-btn:hover {
            background: var(--color-primary);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php require "parciais/header.php" ?>

    <!-- Comments Modal -->
    <div id="commentsModal" class="modal-overlay">
        <div class="comment-modal">
            <div class="modal-post" id="modalPostContent">
                <!-- Conteúdo será preenchido via JS -->
            </div>
            <div class="modal-comments">
                <div class="comments-list" id="commentsList">
                    <!-- Comentários serão carregados aqui -->
                </div>
                <form class="comment-form" id="commentForm">
                    <input type="hidden" id="currentPostId" value="">
                    <input type="text" class="comment-input" id="commentInput" placeholder="Add a comment..." required>
                    <button type="submit" class="comment-submit">Post</button>
                </form>
            </div>
            <button class="close-button">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Modal para mídia expandida -->
    <div id="imageModal" class="image-modal">
        <div class="image-modal-content">
            <button class="close-image-modal">&times;</button>
            <div id="modalImage" class="modal-image-container"></div>
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

    <!-- Main Content -->
    <div class="container">
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="#" class="active"><i class="fas fa-home"></i> <span>Home</span></a></li>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a></li>
                    <li><a href="#"><i class="fas fa-briefcase"></i> <span>Trabalho</span></a></li>
                    <li><a href="#"><i class="fas fa-comments"></i> <span>Mensagens</span></a></li>
                    <li><a href="#"><i class="fas fa-bell"></i> <span>Notificações</span></a></li>
                    <li><a href="#"><i class="fas fa-network-wired"></i> <span>Conexões</span></a></li>
                    <li><a href="itens_salvos.php"><i class="fas fa-bookmark"></i> <span>Itens Salvos</span></a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> <span>Estatisticas</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Feed -->
        <main class="feed">
            <!-- Create Post -->
            <div class="create-post">
                <form method="POST" action="../backend/criar_publicacao.php" enctype="multipart/form-data"
                    id="postForm">
                    <input type="file" name="media0" hidden id="media0" accept="image/*,video/*">
                    <input type="file" name="media1" hidden id="media1" accept="image/*,video/*">
                    <input type="file" name="media2" hidden id="media2" accept="image/*,video/*">
                    <input type="file" name="media3" hidden id="media3" accept="image/*,video/*">
                    <input type="file" name="media4" hidden id="media4" accept="image/*,video/*">

                    <div class="post-input">
                        <?php
                        $fotoPerfil = !empty($perfilData['foto_perfil']) ? "images/perfil/" . $perfilData['foto_perfil'] : "images/perfil/default-profile.jpg";
                        ?>
                        <img src="<?php echo $fotoPerfil ?>" alt="Profile" class="profile-pic">
                        <textarea name="conteudo" placeholder="Partilhe com o mundo..." maxlength="500"></textarea>
                    </div>

                    <!-- Container de pré-visualização das imagens -->
                    <div id="previewGrid" class="flex gap-1">
                        <?php
                        for ($i = 0; $i < 5; $i++) {
                            ?>
                            <div class="relative w-[100px] h-[100px] rounded-lg bg-gray-100 overflow-hidden hidden"
                                id="preview-container-<?= $i ?>">
                                <img id="preview-img-<?= $i ?>" class="object-cover w-full h-full">
                                <button type="button" onclick="removeFile(<?= $i ?>)"
                                    class="absolute top-1 right-1 bg-[#FF5722] hover:bg-[#bf4019] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">×</button>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <div class="post-actions">
                        <div class="action-icons">
                            <button type="button" id="uploadImage"><i class="fas fa-image"></i></button>
                            <button type="button"><i class="fas fa-file-alt"></i></button>
                            <button type="button"><i class="fas fa-link"></i></button>
                            <button type="button"><i class="fas fa-poll"></i></button>
                        </div>
                        <button type="submit" name="publicar" class="publish-btn">Publicar</button>
                    </div>
                </form>
            </div>

            <script>
                const uploadBtn = document.getElementById('uploadImage');
                const inputFiles = [];
                const previewImgs = [];
                const previewContainers = [];

                for (let i = 0; i < 5; i++) {
                    inputFiles[i] = document.getElementById('media' + i);
                    previewImgs[i] = document.getElementById('preview-img-' + i);
                    previewContainers[i] = document.getElementById('preview-container-' + i);

                    inputFiles[i].addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                previewImgs[i].src = e.target.result;
                                previewContainers[i].classList.remove('hidden');
                            }
                            reader.readAsDataURL(file);
                        } else {
                            previewImgs[i].src = '';
                            previewContainers[i].classList.add('hidden');
                        }
                    });
                }

                uploadBtn.addEventListener('click', () => {
                    for (let i = 0; i < inputFiles.length; i++) {
                        if (!inputFiles[i].files.length) {
                            inputFiles[i].click();
                            break;
                        }
                    }
                });

                function removeFile(i) {
                    inputFiles[i].value = ""; // Clear the file input
                    console.log(previewImgs);
                    previewImgs[i].src = "";
                    previewContainers[i].classList.add('hidden');
                }
            </script>

            <!-- Posts -->
            <div class="posts">
                <?php
                include_once("../backend/ligabd.php");

                // Função para contar comentários
                function getCommentCount($con, $postId)
                {
                    $sql = "SELECT COUNT(*) as count FROM comentarios WHERE id_publicacao = $postId";
                    $result = mysqli_query($con, $sql);
                    $data = mysqli_fetch_assoc($result);
                    return $data['count'];
                }

                $sql = "SELECT p.id_publicacao, p.conteudo, p.data_criacao, p.likes, 
                        u.id AS id_utilizador, u.nick, 
                        pr.foto_perfil, pr.ocupacao
                        FROM publicacoes p
                        JOIN utilizadores u ON p.id_utilizador = u.id
                        LEFT JOIN perfis pr ON u.id = pr.id_utilizador
                        WHERE p.deletado_em = '0000-00-00 00:00:00'
                        ORDER BY p.data_criacao DESC";

                $resultado = mysqli_query($con, $sql);

                if (mysqli_num_rows($resultado) > 0) {
                    while ($linha = mysqli_fetch_assoc($resultado)) {
                        $foto = $linha['foto_perfil'] ?: 'default-profile.jpg';
                        $ocupacao = $linha['ocupacao'] ?: 'Utilizador';
                        $publicacaoId = $linha['id_publicacao'];

                        // Verificar se o usuário logado já deu like
                        $likedClass = '';
                        if (isset($_SESSION['id'])) {
                            $userId = $_SESSION['id'];
                            $checkSql = "SELECT * FROM publicacao_likes 
                             WHERE publicacao_id = $publicacaoId AND utilizador_id = $userId";
                            $checkResult = mysqli_query($con, $checkSql);
                            if (mysqli_num_rows($checkResult) > 0) {
                                $likedClass = 'liked';
                            }
                        }

                        $savedClass = '';
                        if (isset($_SESSION['id'])) {
                            $userId = $_SESSION['id'];
                            if (isPostSaved($con, $userId, $publicacaoId)) {
                                $savedClass = 'saved';
                            }
                        }

                        // Buscar imagens da publicação
                        $images = getPostImages($con, $publicacaoId);
                        ?>

                        <article class="post" data-post-id="<?php echo $publicacaoId; ?>">
                            <div class="post-header">
                                <a href="perfil.php?id=<?php echo $linha['id_utilizador']; ?>">
                                    <img src="images/perfil/<?php echo htmlspecialchars($foto); ?>" alt="User"
                                        class="profile-pic">
                                </a>
                                <div class="post-info">
                                    <div>
                                        <a href="perfil.php?id=<?php echo $linha['id_utilizador']; ?>" class="profile-link">
                                            <h3><?php echo htmlspecialchars($linha['nick']); ?></h3>
                                        </a>
                                        <p><?php echo htmlspecialchars($ocupacao); ?></p>
                                    </div>
                                    <span
                                        class="timestamp"><?php echo date('d-m-Y H:i', strtotime($linha['data_criacao'])); ?></span>
                                </div>

                            </div>
                            <div class="post-content">
                                <p><?php echo nl2br(makeLinksClickable($linha['conteudo'])); ?></p>

                                <?php if (!empty($images)): ?>
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
                                            data-post-id="<?php echo $publicacaoId; ?>">
                                            <?php
                                            $displayCount = min($imageCount, 4);
                                            for ($i = 0; $i < $displayCount; $i++):
                                                $media = $images[$i];
                                                ?>
                                                <div class="media-item"
                                                    onclick="openMediaModal(<?php echo $publicacaoId; ?>, <?php echo $i; ?>)">
                                                    <?php if ($media['tipo'] === 'video'): ?>
                                                        <div class="video-thumbnail">
                                                            <video class="post-media" muted>
                                                                <source
                                                                    src="images/publicacoes/<?php echo htmlspecialchars($media['url']); ?>"
                                                                    type="video/mp4">
                                                            </video>
                                                            <div class="play-icon">
                                                                <i class="fas fa-play"></i>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <img src="images/publicacoes/<?php echo htmlspecialchars($media['url']); ?>"
                                                            alt="Imagem da publicação" class="post-media">
                                                        <?php if ($i == 3 && $imageCount > 4): ?>
                                                            <div class="more-images-overlay">
                                                                +<?php echo $imageCount - 4; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="post-actions">
                                <button class="like-btn <?php echo $likedClass; ?>"
                                    data-publicacao-id="<?php echo $publicacaoId; ?>">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span class="like-count"><?php echo $linha['likes']; ?></span>
                                </button>
                                <button class="comment-btn" onclick="openCommentsModal(<?php echo $linha['id_publicacao']; ?>)">
                                    <i class="fas fa-comment"></i>
                                    <span
                                        class="comment-count"><?php echo getCommentCount($con, $linha['id_publicacao']); ?></span>
                                </button>
                                <button><i class="fas fa-share"></i> </button>
                                <button class="save-btn <?php echo $savedClass; ?>"
                                    data-publicacao-id="<?php echo $publicacaoId; ?>">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </div>
                        </article>
                        <?php
                    }
                } else {
                    echo "<p class='no-posts'>Sem publicações para mostrar.</p>";
                }
                ?>
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
    </div>

    <!-- Footer -->
    <?php require "parciais/footer.php" ?>



    <script>
        // Sistema de visualização de imagens
        let currentImageModal = {
            postId: null,
            currentIndex: 0,
            images: []
        };

        function openMediaModal(postId, mediaIndex = 0) {
            const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
            if (!postElement) return;

            const medias = [];
            const mediaElements = postElement.querySelectorAll('.media-item');

            mediaElements.forEach(item => {
                const videoElement = item.querySelector('video');
                const imgElement = item.querySelector('img');

                medias.push({
                    url: videoElement
                        ? videoElement.querySelector('source').src.split('/').pop()
                        : imgElement.src.split('/').pop(),
                    tipo: videoElement ? 'video' : 'imagem'
                });
            });

            if (medias.length === 0) return;

            currentImageModal = {
                postId,
                currentIndex: mediaIndex,
                medias
            };

            showMediaInModal();
            document.getElementById('imageModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function showMediaInModal() {
            const modal = document.getElementById('imageModal');
            const modalContent = document.getElementById('modalImage');
            const imageCounter = document.getElementById('imageCounter');
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');

            // Limpa o conteúdo anterior e pausa qualquer vídeo
            modalContent.innerHTML = '';

            const currentMedia = currentImageModal.medias[currentImageModal.currentIndex];

            if (currentMedia.tipo === 'video') {
                const videoContainer = document.createElement('div');
                videoContainer.className = 'video-container';

                const video = document.createElement('video');
                video.controls = true;
                video.autoplay = true;
                video.className = 'modal-media';
                video.style.maxWidth = '100%';
                video.style.maxHeight = '80vh';

                const source = document.createElement('source');
                source.src = `images/publicacoes/${currentMedia.url}`;
                source.type = 'video/mp4';

                video.appendChild(source);
                video.appendChild(document.createTextNode('Seu navegador não suporta vídeos.'));
                videoContainer.appendChild(video);
                modalContent.appendChild(videoContainer);
            } else {
                const img = document.createElement('img');
                img.src = `images/publicacoes/${currentMedia.url}`;
                img.className = 'modal-media';
                img.alt = 'Imagem expandida';
                modalContent.appendChild(img);
            }

            imageCounter.textContent = `${currentImageModal.currentIndex + 1} / ${currentImageModal.medias.length}`;

            prevBtn.disabled = currentImageModal.currentIndex === 0;
            nextBtn.disabled = currentImageModal.currentIndex === currentImageModal.medias.length - 1;
        }

        function closeImageModal() {
            const modalContent = document.getElementById('modalImage');
            // Pausa todos os vídeos dentro do modal
            const videos = modalContent.getElementsByTagName('video');
            for (let video of videos) {
                video.pause();
            }

            document.getElementById('imageModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function navigateImage(direction) {
            if (direction === 'prev' && currentImageModal.currentIndex > 0) {
                currentImageModal.currentIndex--;
            } else if (direction === 'next' && currentImageModal.currentIndex < currentImageModal.medias.length - 1) {
                currentImageModal.currentIndex++;
            }
            showMediaInModal(); // Mudar de showImageInModal() para showMediaInModal()
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

        // Like functionality
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

        // Referências para o modal e o botão de fechar
        const modal = document.getElementById('commentsModal');
        const closeButton = modal.querySelector('.close-button');

        // Função para fechar o modal
        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Evento para o botão de fechar
        closeButton.addEventListener('click', closeModal);

        // Fechar modal ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Variável global para armazenar o ID da publicação atual
        let currentPostId = null;

        // Função para abrir o modal de comentários
        function openCommentsModal(postId) {
            currentPostId = postId;

            // Primeiro, pegue o elemento da publicação diretamente do DOM
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
                            imagesHTML = `
                        <div class="post-images">
                            <div class="images-grid single">
                                <img src="images/publicacoes/${post.images[0].url}" alt="Imagem da publicação" class="post-image">
                            </div>
                        </div>
                    `;
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

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        // Função para carregar comentários
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

        // Função para mostrar toast
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

        // Save functionality
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function () {
                const publicacaoId = this.getAttribute('data-publicacao-id');

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

        // Envio de novo comentário
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

                            const commentCount = document.querySelector(`.comment-btn[onclick*="${currentPostId}"] .comment-count`);
                            if (commentCount) {
                                commentCount.textContent = parseInt(commentCount.textContent) + 1;
                            }
                        }
                    });
            }
        });
    </script>

</body>

</html>