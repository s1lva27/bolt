<?php session_start();
include "../backend/ligabd.php";

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
</head>

<body>
    <!-- Header -->
    <?php require "parciais/header.php" ?>

    <!-- Comments Modal -->
    <div id="commentsModal" class="modal-overlay">
        <div class="comment-modal">
            <div class="modal-post">
                <!-- Post content will be cloned here -->
            </div>
            <div class="modal-comments">
                <div class="comments-list">
                    <!-- Example comments for visual testing -->
                    <div class="comment-item">
                        <img src="images/perfil/default-profile.jpg" alt="User" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-username">John Doe</span>
                                <span class="comment-time">2 hours ago</span>
                            </div>
                            <p class="comment-text">This is an example comment!</p>
                        </div>
                    </div>
                </div>
                <form class="comment-form">
                    <input type="text" class="comment-input" placeholder="Add a comment...">
                    <button type="submit" class="comment-submit">Post</button>
                </form>
            </div>
            <button class="close-button">
                <i class="fas fa-times"></i>
            </button>
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
                    <li><a href="#"><i class="fas fa-bookmark"></i> <span>Itens Salvos</span></a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> <span>Estatisticas</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Feed -->
        <main class="feed">
            <!-- Create Post -->
            <div class="create-post">
                <form method="POST" action="../backend/criar_publicacao.php" enctype="multipart/form-data">
                    <div class="post-input">
                        <?php
                        $fotoPerfil = !empty($perfilData['foto_perfil']) ? "images/perfil/" . $perfilData['foto_perfil'] : "images/perfil/default-profile.jpg";
                        ?>
                        <img src="<?php echo $fotoPerfil ?>" alt="Profile" class="profile-pic">
                        <textarea name="conteudo" placeholder="Partilhe com o mundo..." maxlength="500"
                            required></textarea>
                    </div>
                    <div class="post-actions">
                        <button type="button"><i class="fas fa-image"></i> Fotos</button>
                        <button type="button"><i class="fas fa-file-alt"></i> Document</button>
                        <button type="button"><i class="fas fa-link"></i> Link</button>
                        <button type="button"><i class="fas fa-poll"></i> Poll</button>
                        <button type="submit" name="publicar" class="publish-btn">Publicar</button>
                    </div>
                </form>
            </div>

            <!-- Posts -->
            <div class="posts">
                <?php
                include_once("../backend/ligabd.php");

                $sql = "SELECT p.id_publicacao, p.conteudo, p.data_criacao, p.likes, u.nick, pr.foto_perfil, pr.ocupacao 
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

                        // Verificar se o usuário logado já deu like
                        $likedClass = '';
                        if (isset($_SESSION['id'])) {
                            $userId = $_SESSION['id'];
                            $publicacaoId = $linha['id_publicacao'];
                            $checkSql = "SELECT * FROM publicacao_likes 
                     WHERE publicacao_id = $publicacaoId AND utilizador_id = $userId";
                            $checkResult = mysqli_query($con, $checkSql);
                            if (mysqli_num_rows($checkResult) > 0) {
                                $likedClass = 'liked';
                            }
                        }
                        ?>

                        <article class="post" data-post-id="<?php echo $publicacaoId; ?>">
                            <div class="post-header">
                                <img src="images/perfil/<?php echo htmlspecialchars($foto); ?>" alt="User" class="profile-pic">
                                <div class="post-info">
                                    <h3><?php echo htmlspecialchars($linha['nick']); ?></h3>
                                    <p><?php echo htmlspecialchars($ocupacao); ?></p>
                                    <span
                                        class="timestamp"><?php echo date('d-m-Y H:i', strtotime($linha['data_criacao'])); ?></span>
                                </div>
                            </div>
                            <div class="post-content">
                                <p><?php echo nl2br(htmlspecialchars($linha['conteudo'])); ?></p>
                            </div>
                            <div class="post-actions">
                                <button class="like-btn <?php echo $likedClass; ?>"
                                    data-publicacao-id="<?php echo $publicacaoId; ?>">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span class="like-count"><?php echo $linha['likes']; ?></span>
                                </button>
                                <button class="comment-btn" onclick="openCommentsModal(this)">
                                    <i class="fas fa-comment"></i>
                                    
                                </button>
                                <button><i class="fas fa-share"></i> Share</button>
                                <button><i class="fas fa-bookmark"></i> Save</button>
                            </div>
                        </article>
                        <?php
                    }
                } else {
                    echo "<p class='no-posts'>Sem publicações para mostrar.</p>";
                }
                ?>
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

        // Comments Modal functionality
        const modal = document.getElementById('commentsModal');
        const modalPost = modal.querySelector('.modal-post');
        const closeButton = modal.querySelector('.close-button');

        function openCommentsModal(button) {
            const post = button.closest('.post');
            const postContent = post.querySelector('.post-header').outerHTML +
                post.querySelector('.post-content').outerHTML;

            modalPost.innerHTML = postContent;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        closeButton.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Prevent form submission (for now)
        modal.querySelector('.comment-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const input = e.target.querySelector('.comment-input');
            if (input.value.trim()) {
                // Here you'll add the backend integration later
                input.value = '';
            }
        });
    </script>
</body>

</html>