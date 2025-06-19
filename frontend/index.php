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
    $sql = "SELECT url, content_warning FROM publicacao_medias 
            WHERE publicacao_id = $postId AND tipo = 'imagem' 
            ORDER BY ordem ASC";
    $result = mysqli_query($con, $sql);
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
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

        /* Galeria de imagens nas publicações */
        .post-images {
            margin-top: 15px;
        }

        .images-grid {
            display: grid;
            gap: 8px;
            border-radius: 12px;
            overflow: hidden;
        }

        .images-grid.single {
            grid-template-columns: 1fr;
        }

        .images-grid.double {
            grid-template-columns: 1fr 1fr;
        }

        .images-grid.triple {
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 1fr 1fr;
        }

        .images-grid.multiple {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
        }

        .image-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            background: var(--bg-input);
            min-height: 200px;
        }

        .images-grid.single .image-item {
            min-height: 400px;
        }

        .images-grid.triple .image-item:first-child {
            grid-row: 1 / 3;
        }

        .post-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .image-item:hover .post-image {
            transform: scale(1.02);
        }

        .more-images-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Modal para imagem expandida */
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            padding: 20px;
        }

        .image-modal-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-image {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .image-modal-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            color: white;
        }

        .modal-nav-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.2s ease;
        }

        .modal-nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .modal-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .image-counter {
            color: white;
            font-size: 14px;
        }

        .close-image-modal {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            padding: 5px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .preview-grid {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 8px;
            }

            .images-grid.double,
            .images-grid.triple,
            .images-grid.multiple {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
            }

            .images-grid.triple .image-item:first-child {
                grid-row: auto;
            }

            .image-item {
                min-height: 250px;
            }
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
                <form method="POST" action="../backend/criar_publicacao.php" enctype="multipart/form-data" id="postForm">
                    <input type="file" name="imagens[]" id="imageUpload" style="display:none;" accept="image/*" multiple>
                    <div class="post-input">
                        <?php
                        $fotoPerfil = !empty($perfilData['foto_perfil']) ? "images/perfil/" . $perfilData['foto_perfil'] : "images/perfil/default-profile.jpg";
                        ?>
                        <img src="<?php echo $fotoPerfil ?>" alt="Profile" class="profile-pic">
                        <textarea name="conteudo" placeholder="Partilhe com o mundo..." maxlength="500"></textarea>
                    </div>

                    <!-- Container de pré-visualização das imagens -->
                    <div class="multiple-image-preview" id="imagePreviewContainer">
                        <div class="preview-header">
                            <span class="preview-count" id="imageCount">0 imagens selecionadas</span>
                            <button type="button" id="clearAllBtn" class="clear-all-btn">Remover todas</button>
                        </div>
                        <div class="preview-grid" id="previewGrid"></div>
                    </div>

                    <div class="post-actions">
                        <div class="action-icons">
                            <button type="button" id="imageUploadBtn"><i class="fas fa-image"></i></button>
                            <button type="button"><i class="fas fa-file-alt"></i></button>
                            <button type="button"><i class="fas fa-link"></i></button>
                            <button type="button"><i class="fas fa-poll"></i></button>
                        </div>
                        <button type="submit" name="publicar" class="publish-btn">Publicar</button>
                    </div>
                </form>
            </div>

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
                                        if ($imageCount == 1) $gridClass = 'single';
                                        elseif ($imageCount == 2) $gridClass = 'double';
                                        elseif ($imageCount == 3) $gridClass = 'triple';
                                        else $gridClass = 'multiple';
                                        ?>
                                        <div class="images-grid <?php echo $gridClass; ?>" data-post-id="<?php echo $publicacaoId; ?>">
                                            <?php 
                                            $displayCount = min($imageCount, 4);
                                            for ($i = 0; $i < $displayCount; $i++): 
                                                $image = $images[$i];
                                            ?>
                                                <div class="image-item" onclick="openImageModal(<?php echo $publicacaoId; ?>, <?php echo $i; ?>)">
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
        // Sistema de múltiplas imagens
        class MultipleImageUpload {
            constructor() {
                this.selectedFiles = [];
                this.maxFiles = 10;
                this.maxFileSize = 5 * 1024 * 1024; // 5MB
                this.allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                this.initializeElements();
                this.bindEvents();
            }
            
            initializeElements() {
                this.fileInput = document.getElementById('imageUpload');
                this.uploadBtn = document.getElementById('imageUploadBtn');
                this.previewContainer = document.getElementById('imagePreviewContainer');
                this.previewGrid = document.getElementById('previewGrid');
                this.imageCount = document.getElementById('imageCount');
                this.clearAllBtn = document.getElementById('clearAllBtn');
                this.form = document.getElementById('postForm');
            }
            
            bindEvents() {
                this.uploadBtn.addEventListener('click', () => this.fileInput.click());
                this.fileInput.addEventListener('change', (e) => this.handleFileSelection(e));
                this.clearAllBtn.addEventListener('click', () => this.removeAllImages());
                this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
            }
            
            handleFileSelection(event) {
                const files = Array.from(event.target.files || []);
                
                if (this.selectedFiles.length + files.length > this.maxFiles) {
                    this.showToast(`Máximo de ${this.maxFiles} imagens permitidas`);
                    event.target.value = '';
                    return;
                }
                
                files.forEach((file, index) => {
                    if (this.validateFile(file)) {
                        this.addFileToSelection(file, index);
                    }
                });
                
                event.target.value = '';
                this.updatePreview();
            }
            
            validateFile(file) {
                if (!this.allowedTypes.includes(file.type)) {
                    this.showToast(`Tipo de arquivo não suportado: ${file.type}`);
                    return false;
                }
                
                if (file.size > this.maxFileSize) {
                    this.showToast(`Arquivo muito grande: ${file.name}. Máximo 5MB.`);
                    return false;
                }
                
                return true;
            }
            
            addFileToSelection(file, originalIndex) {
                const fileId = `${Date.now()}_${originalIndex}_${Math.random()}`;
                const fileData = {
                    id: fileId,
                    file: file,
                    originalIndex: originalIndex
                };
                
                this.selectedFiles.push(fileData);
            }
            
            removeImage(fileId) {
                this.selectedFiles = this.selectedFiles.filter(f => f.id !== fileId);
                this.updatePreview();
            }
            
            removeAllImages() {
                this.selectedFiles = [];
                this.updatePreview();
            }
            
            updatePreview() {
                if (this.selectedFiles.length === 0) {
                    this.previewContainer.style.display = 'none';
                    return;
                }
                
                this.previewContainer.style.display = 'block';
                this.previewGrid.innerHTML = '';
                
                this.selectedFiles.forEach((fileData) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imageWrapper = document.createElement('div');
                        imageWrapper.className = 'preview-item';
                        imageWrapper.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="preview-image">
                            <button type="button" class="remove-image-btn" data-file-id="${fileData.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        
                        const removeBtn = imageWrapper.querySelector('.remove-image-btn');
                        removeBtn.addEventListener('click', () => this.removeImage(fileData.id));
                        
                        this.previewGrid.appendChild(imageWrapper);
                    };
                    reader.readAsDataURL(fileData.file);
                });
                
                this.imageCount.textContent = `${this.selectedFiles.length}/${this.maxFiles} imagens selecionadas`;
            }
            
            handleFormSubmit(event) {
                if (this.selectedFiles.length === 0) return;
                
                const form = event.target;
                const formData = new FormData();
                
                for (let [key, value] of new FormData(form).entries()) {
                    if (key !== 'imagens[]') {
                        formData.append(key, value);
                    }
                }
                
                this.selectedFiles.forEach((fileData, index) => {
                    formData.append('imagens[]', fileData.file);
                });
                
                event.preventDefault();
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Erro no servidor');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    this.showToast('Erro ao criar publicação');
                });
            }
            
            showToast(message) {
                if (window.showToast) {
                    window.showToast(message);
                } else {
                    alert(message);
                }
            }
        }

        // Inicializar sistema de upload
        document.addEventListener('DOMContentLoaded', function() {
            new MultipleImageUpload();
        });

        // Sistema de visualização de imagens
        let currentImageModal = {
            postId: null,
            currentIndex: 0,
            images: []
        };

        function openImageModal(postId, imageIndex = 0) {
            // Buscar todas as imagens da publicação
            fetch(`../backend/get_post_images.php?post_id=${postId}`)
                .then(response => response.json())
                .then(images => {
                    currentImageModal.postId = postId;
                    currentImageModal.currentIndex = imageIndex;
                    currentImageModal.images = images;
                    
                    showImageInModal();
                    document.getElementById('imageModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Erro ao carregar imagens:', error);
                });
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

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.addEventListener('keydown', function(e) {
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

            fetch(`../backend/get_post.php?id=${postId}`)
                .then(response => response.json())
                .then(post => {
                    const dataCriacao = new Date(post.data_criacao);
                    const dataFormatada = `${dataCriacao.getDate().toString().padStart(2, '0')}-${(dataCriacao.getMonth() + 1).toString().padStart(2, '0')}-${dataCriacao.getFullYear()} ${dataCriacao.getHours().toString().padStart(2, '0')}:${dataCriacao.getMinutes().toString().padStart(2, '0')}`;

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
          </div>
        </div>
      `;

                    loadComments(postId);
                });

            document.getElementById('currentPostId').value = postId;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
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