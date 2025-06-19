<?php
include_once("../backend/ligabd.php");
session_start();

// Função para transformar URLs em links clicáveis (copiada do index.php)
function makeLinksClickable($text)
{
    $pattern = '/(https?:\/\/[^\s]+)/';
    $linkedText = preg_replace($pattern, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
    return $linkedText;
}

// Função para contar comentários (copiada do index.php)
function getCommentCount($con, $postId)
{
    $sql = "SELECT COUNT(*) as count FROM comentarios WHERE id_publicacao = $postId";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}

// Função para verificar se post está salvo
function isPostSaved($con, $userId, $postId)
{
    $sql = "SELECT * FROM publicacao_salvas 
            WHERE utilizador_id = $userId AND publicacao_id = $postId";
    $result = mysqli_query($con, $sql);
    return mysqli_num_rows($result) > 0;
}

$termo = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$termo_sql = mysqli_real_escape_string($con, $termo);
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
$excludeSelf = $userId ? "AND u.id != $userId" : "";
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar - Orange</title>
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/style_pesquisar.css">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon_orange.png">
    <link rel="stylesheet" href="css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<style>
    /* Estilos adicionais para o botão de seguir */
    .profile-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
    }

    .profile-img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--color-primary);
        box-shadow: var(--shadow-sm);
    }

    .profile-info {
        flex: 1;
    }

    .profile-info h4 {
        margin: 0;
        font-size: 1.1rem;
    }

    .profile-info p {
        margin: 4px 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .profile-info small {
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .follow-btn {
        margin-left: auto;
    }

    /* Adicione no final da tag <style> */
    .profile-info small {
        display: block;
        margin-top: 5px;
    }

    .follow-btn {
        background: var(--color-primary);
        color: white;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 10px;
        font-size: 0.9rem;
    }

    .follow-btn.following {
        background: var(--bg-card);
        color: var(--color-primary);
        border: 1px solid var(--color-primary);
    }
</style>


<body>
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

    <!-- Toast Notification (copiado do index.php) -->
    <div id="toast" class="toast">
        <div class="toast-icon">
            <i class="fas fa-bookmark"></i>
        </div>
        <div class="toast-content">
            <p id="toast-message">Mensagem aqui</p>
        </div>
    </div>

    <div class="container">
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

        <main class="feed">
            <!-- PERFIS -->
            <section class="mb-4">
                <h3 class="mb-2">Perfis relacionados</h3>
                <div class="profile-results" id="profileResults">
                    <?php
                    $sqlPerfis = "SELECT u.id, u.nome_completo, u.nick, p.foto_perfil,
                (SELECT COUNT(*) FROM seguidores WHERE id_seguido = u.id) AS seguidores,
                (SELECT COUNT(*) FROM seguidores WHERE id_seguidor = u.id) AS a_seguir,
                " . ($userId ?
                        "(SELECT COUNT(*) FROM seguidores 
                    WHERE id_seguidor = $userId AND id_seguido = u.id) AS is_following"
                        : "0 AS is_following") . "
              FROM utilizadores u
              JOIN perfis p ON u.id = p.id_utilizador
              WHERE (u.nome_completo LIKE '%$termo_sql%' 
                 OR u.nick LIKE '%$termo_sql%' 
                 OR p.biografia LIKE '%$termo_sql%')
                 $excludeSelf
              LIMIT 3";
                    $resPerfis = mysqli_query($con, $sqlPerfis);

                    if (mysqli_num_rows($resPerfis) > 0) {
                        while ($perfil = mysqli_fetch_assoc($resPerfis)) {
                            ?>
                            <div class="profile-card">
                                <a href="perfil.php?id=<?php echo $perfil['id']; ?>" class="profile-link">
                                    <img src="images/perfil/<?php echo htmlspecialchars($perfil['foto_perfil']); ?>"
                                        class="profile-img">
                                    <div class="profile-info">
                                        <h4><?php echo htmlspecialchars($perfil['nome_completo']); ?></h4>
                                        <p>@<?php echo htmlspecialchars($perfil['nick']); ?></p>
                                        <small>
                                            <strong><?php echo $perfil['seguidores']; ?></strong> seguidores |
                                            <strong><?php echo $perfil['a_seguir']; ?></strong> a seguir
                                        </small>
                                    </div>
                                </a>
                                <?php if ($userId && $userId != $perfil['id']) { ?>
                                    <button class="follow-btn <?php echo $perfil['is_following'] ? 'following' : ''; ?>"
                                        data-user-id="<?php echo $perfil['id']; ?>">
                                        <?php echo $perfil['is_following'] ? 'Seguindo' : 'Seguir'; ?>
                                    </button>
                                <?php } ?>
                            </div>
                            <?php

                        }
                    } else {
                        echo "<p class='no-posts'>Nenhum perfil encontrado.</p>";
                    }



                    // Verificar se há mais perfis
                    $sqlCount = "SELECT COUNT(*) as total FROM utilizadores u
                                 JOIN perfis p ON u.id = p.id_utilizador
                                 WHERE u.nome_completo LIKE '%$termo_sql%' 
                                    OR u.nick LIKE '%$termo_sql%' 
                                    OR p.biografia LIKE '%$termo_sql%'";
                    $resCount = mysqli_query($con, $sqlCount);
                    $totalPerfis = mysqli_fetch_assoc($resCount)['total'];
                    $offset = 3; // Já mostramos 3
                    
                    // Botão "Ver mais" fora da div de resultados
                    if ($totalPerfis > 3) {
                        echo '<button id="loadMoreProfiles" class="btn-load-more">Ver mais</button>';
                    }
                    ?>
                </div>
            </section>

            <!-- PUBLICACOES -->
            <section>
                <h3 class="mb-2">Publicações relacionadas</h3>
                <div class="posts">
                    <?php
                    $sqlPosts = "SELECT p.id_publicacao, p.conteudo, p.data_criacao, p.likes, 
                                        u.id AS id_utilizador, u.nick, 
                                        pr.foto_perfil, pr.ocupacao,
                                        " . ($userId ?
                        "(SELECT COUNT(*) FROM publicacao_likes 
                                              WHERE publicacao_id = p.id_publicacao AND utilizador_id = $userId) AS user_liked,
                                             (SELECT COUNT(*) FROM publicacao_salvas 
                                              WHERE publicacao_id = p.id_publicacao AND utilizador_id = $userId) AS user_saved"
                        : "0 AS user_liked, 0 AS user_saved") . "
                                 FROM publicacoes p
                                 JOIN utilizadores u ON p.id_utilizador = u.id
                                 LEFT JOIN perfis pr ON pr.id_utilizador = u.id
                                 WHERE p.deletado_em = '0000-00-00 00:00:00' 
                                   AND p.conteudo LIKE '%$termo_sql%'
                                 ORDER BY p.data_criacao DESC";
                    $resPosts = mysqli_query($con, $sqlPosts);

                    if (mysqli_num_rows($resPosts) > 0) {
                        while ($post = mysqli_fetch_assoc($resPosts)) {
                            $publicacaoId = $post['id_publicacao'];
                            $foto = $post['foto_perfil'] ?? 'default-profile.jpg';
                            $ocupacao = $post['ocupacao'] ?? 'Utilizador';
                            $likedClass = $post['user_liked'] ? 'liked' : '';
                            $savedClass = $post['user_saved'] ? 'saved' : '';
                            $commentCount = getCommentCount($con, $publicacaoId);
                            ?>
                            <article class="post" data-post-id="<?php echo $publicacaoId; ?>">
                                <div class="post-header">
                                    <a href="perfil.php?id=<?php echo $post['id_utilizador']; ?>">
                                        <img src="images/perfil/<?php echo htmlspecialchars($foto); ?>" class="profile-pic">
                                    </a>
                                    <div class="post-info">
                                        <div>
                                            <a href="perfil.php?id=<?php echo $post['id_utilizador']; ?>" class="profile-link">
                                                <h3><?php echo htmlspecialchars($post['nick']); ?></h3>
                                            </a>
                                            <p><?php echo htmlspecialchars($ocupacao); ?></p>
                                        </div>
                                        <span
                                            class="timestamp"><?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?></span>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <p><?php echo nl2br(makeLinksClickable($post['conteudo'])); ?></p>
                                </div>
                                <div class="post-actions">
                                    <button class="like-btn <?php echo $likedClass; ?>"
                                        data-publicacao-id="<?php echo $publicacaoId; ?>">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span class="like-count"><?php echo $post['likes']; ?></span>
                                    </button>
                                    <button class="comment-btn" onclick="openCommentsModal(<?php echo $publicacaoId; ?>)">
                                        <i class="fas fa-comment"></i>
                                        <span class="comment-count"><?php echo $commentCount; ?></span>
                                    </button>
                                    <button><i class="fas fa-share"></i></button>
                                    <button class="save-btn <?php echo $savedClass; ?>"
                                        data-publicacao-id="<?php echo $publicacaoId; ?>">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                                </div>
                            </article>
                            <?php
                        }
                    } else {
                        echo "<p class='no-posts'>Nenhuma publicação encontrada.</p>";
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

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

        document.addEventListener('click', function (e) {
            const followBtn = e.target.closest('.follow-btn');
            if (followBtn) {
                e.preventDefault();
                const userId = followBtn.getAttribute('data-user-id');
                const isFollowing = followBtn.classList.contains('following');
                const profileCard = followBtn.closest('.profile-card');

                // Criar FormData para enviar
                const formData = new FormData();
                formData.append('user_id', userId);

                fetch('../backend/seguir_alternativo.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Atualizar estado do botão
                            if (data.action === 'follow') {
                                followBtn.classList.add('following');
                                followBtn.textContent = 'Seguindo';

                                // Incrementar contador de seguidores
                                const seguidoresElement = profileCard.querySelector('.profile-info small strong:first-child');
                                let seguidoresCount = parseInt(seguidoresElement.textContent);
                                seguidoresElement.textContent = seguidoresCount + 1;
                            } else {
                                followBtn.classList.remove('following');
                                followBtn.textContent = 'Seguir';

                                // Decrementar contador de seguidores
                                const seguidoresElement = profileCard.querySelector('.profile-info small strong:first-child');
                                let seguidoresCount = parseInt(seguidoresElement.textContent);
                                seguidoresElement.textContent = seguidoresCount - 1;
                            }
                        } else {
                            console.error('Erro:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // CORREÇÃO CARREGAR MAIS PERFIS
        document.getElementById('loadMoreProfiles')?.addEventListener('click', function () {
            const termo = "<?php echo $termo; ?>";
            const currentCount = document.querySelectorAll('.profile-card').length;

            fetch(`../backend/load_more_profiles.php?termo=${termo}&offset=${currentCount}`)
                .then(response => response.text())
                .then(html => {
                    if (html.trim() === '') {
                        this.style.display = 'none';
                    } else {
                        // Inserir antes do botão
                        this.insertAdjacentHTML('beforebegin', html);
                    }
                });
        });

        // Tornar cards de perfil clicáveis
        document.querySelectorAll('.profile-card').forEach(card => {
            card.addEventListener('click', function (e) {
                // Só redireciona se não foi clicado diretamente no botão de seguir
                if (!e.target.closest('.follow-btn')) {
                    const link = this.querySelector('.profile-link');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            });
        });
    </script>
</body>

</html>