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

        /* Adicione ao seu CSS existente */
        .image-preview-container {
            margin-top: 10px;
            display: none;
        }

        .image-preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .remove-image-btn {
            background: var(--color-danger);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            margin-top: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .remove-image-btn:hover {
            background: #d63031;
        }

        /* Novos estilos para as imagens */
        .post-image-container {
            position: relative;
            overflow: hidden;
            margin-top: 15px;
            border-radius: 8px;
            cursor: pointer;
            height: 400px;
            /* Altura fixa para todas as imagens */
        }

        .post-image {
            width: auto;
            height: 100%;
            border-radius: 20px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .post-image-container:hover .post-image {
            transform: scale(1.03);
        }

        /* Modal para imagem expandida */
        #imageModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        #expandedImg {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .close-image-modal {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            cursor: pointer;
            background: none;
            border: none;
            z-index: 2001;
        }

        .image-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            background: rgba(0, 0, 0, 0.6);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
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
    <div id="imageModal">
        <button class="close-image-modal">&times;</button>
        <img id="expandedImg" src="" alt="Imagem expandida">
        <div class="image-info" id="imageInfo"></div>
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
                <form method="POST" action="../backend/criar_publicacao.php" enctype="multipart/form-data">
                    <input type="file" name="imagem" id="imageUpload" style="display:none;" accept="image/*">
                    <div class="post-input">
                        <?php
                        $fotoPerfil = !empty($perfilData['foto_perfil']) ? "images/perfil/" . $perfilData['foto_perfil'] : "images/perfil/default-profile.jpg";
                        ?>
                        <img src="<?php echo $fotoPerfil ?>" alt="Profile" class="profile-pic">
                        <textarea name="conteudo" placeholder="Partilhe com o mundo..." maxlength="500"
                            required></textarea>
                    </div>

                    <!-- Container de pré-visualização da imagem -->
                    <div class="image-preview-container" id="imagePreviewContainer">
                        <img id="imagePreview" class="image-preview" src="#" alt="Pré-visualização da imagem">
                        <button type="button" id="removeImageBtn" class="remove-image-btn">Remover imagem</button>
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
       pr.foto_perfil, pr.ocupacao,
       pm.url AS imagem_url
FROM publicacoes p
JOIN utilizadores u ON p.id_utilizador = u.id
LEFT JOIN perfis pr ON u.id = pr.id_utilizador
LEFT JOIN publicacao_medias pm ON p.id_publicacao = pm.publicacao_id
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
                                <?php if (!empty($linha['imagem_url'])): ?>
                                    <div class="post-image-container" onclick="openImageModal(
                 'images/publicacoes/<?php echo htmlspecialchars($linha['imagem_url']); ?>',
                 '<?php echo htmlspecialchars($linha['nick']); ?>',
                 '<?php echo date('d-m-Y H:i', strtotime($linha['data_criacao'])); ?>'
             )">
                                        <img src="images/publicacoes/<?php echo htmlspecialchars($linha['imagem_url']); ?>"
                                            alt="Imagem da publicação" class="post-image">
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
        // Atualize a função openCommentsModal:
        function openCommentsModal(postId) {
            currentPostId = postId;

            fetch(`../backend/get_post.php?id=${postId}`)
                .then(response => response.json())
                .then(post => {
                    const dataCriacao = new Date(post.data_criacao);
                    const dataFormatada = `${dataCriacao.getDate().toString().padStart(2, '0')}-${(dataCriacao.getMonth() + 1).toString().padStart(2, '0')}-${dataCriacao.getFullYear()} ${dataCriacao.getHours().toString().padStart(2, '0')}:${dataCriacao.getMinutes().toString().padStart(2, '0')}`;

                    // Adicione a classe 'post-content' para manter a formatação
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
            <p>${post.conteudo.replace(/\n/g, '<br>')}</p> <!-- Mantém quebras de linha -->
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
                        // Formatar a data do comentário
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

            // Mostrar o toast
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Esconder após 3 segundos
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }, 3000);
        }

        // Funções para abrir/fechar imagem expandida
        function openImageModal(imageSrc, username, timestamp) {
            const modal = document.getElementById('imageModal');
            const expandedImg = document.getElementById('expandedImg');
            const imageInfo = document.getElementById('imageInfo');

            expandedImg.src = imageSrc;
            imageInfo.innerHTML = `<span>${username}</span> · ${timestamp}`;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Event listeners para o modal de imagem
        document.querySelector('.close-image-modal').addEventListener('click', closeImageModal);

        document.getElementById('imageModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Fechar com ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && document.getElementById('imageModal').style.display === 'flex') {
                closeImageModal();
            }
        });

        // Dentro do evento de clique do save-btn:
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

        // Botão para abrir seletor de imagens
        document.getElementById('imageUploadBtn').addEventListener('click', function () {
            document.getElementById('imageUpload').click();
        });

        // Adicione ao seu script existente
        document.getElementById('imageUpload').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function (event) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = event.target.result;
                    document.getElementById('imagePreviewContainer').style.display = 'block';
                }

                reader.readAsDataURL(file);
            }
        });

        // Botão para remover imagem selecionada
        document.getElementById('removeImageBtn').addEventListener('click', function () {
            document.getElementById('imageUpload').value = '';
            document.getElementById('imagePreviewContainer').style.display = 'none';
            document.getElementById('imagePreview').src = '#';
        });

        // Botão para abrir seletor de imagens
        document.getElementById('imageUploadBtn').addEventListener('click', function () {
            document.getElementById('imageUpload').click();
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

                            // Atualiza contador de comentários
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