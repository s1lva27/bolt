<?php
session_start();
require "../backend/ligabd.php";

// Verificar se o utilizador está autenticado
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$currentUserId = $_SESSION['id'];
$currentUserType = $_SESSION['id_tipos_utilizador'];

// Função para transformar URLs em links clicáveis
function makeLinksClickable($text)
{
    $pattern = '/(https?:\/\/[^\s]+)/';
    $linkedText = preg_replace($pattern, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
    return $linkedText;
}

// Função para contar comentários
function getCommentCount($con, $postId)
{
    $sql = "SELECT COUNT(*) as count FROM comentarios WHERE id_publicacao = $postId";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}

// Função para verificar se o post está salvo
function isPostSaved($con, $userId, $postId)
{
    $sql = "SELECT * FROM publicacao_salvas
            WHERE utilizador_id = $userId AND publicacao_id = $postId";
    $result = mysqli_query($con, $sql);
    return mysqli_num_rows($result) > 0;
}

// Função para buscar imagens da publicação
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

// Função para buscar dados da poll
function getPollData($con, $postId)
{
    // Verificar se a publicação é uma poll
    $sqlCheck = "SELECT tipo FROM publicacoes WHERE id_publicacao = ?";
    $stmtCheck = $con->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $postId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $post = $result->fetch_assoc();

    if (!$post || $post['tipo'] !== 'poll') {
        return null;
    }

    // Buscar dados da poll
    $sqlPoll = "SELECT p.id, p.pergunta, p.data_expiracao, p.total_votos
                FROM polls p
                WHERE p.publicacao_id = ?";
    $stmtPoll = $con->prepare($sqlPoll);
    $stmtPoll->bind_param("i", $postId);
    $stmtPoll->execute();
    $pollResult = $stmtPoll->get_result();

    if ($pollResult->num_rows === 0) {
        return null;
    }

    $poll = $pollResult->fetch_assoc();

    // Buscar opções da poll
    $sqlOpcoes = "SELECT id, opcao_texto, votos FROM poll_opcoes 
                  WHERE poll_id = ? ORDER BY ordem ASC";
    $stmtOpcoes = $con->prepare($sqlOpcoes);
    $stmtOpcoes->bind_param("i", $poll['id']);
    $stmtOpcoes->execute();
    $opcoesResult = $stmtOpcoes->get_result();

    $opcoes = [];
    while ($row = $opcoesResult->fetch_assoc()) {
        $opcoes[] = $row;
    }

    return [
        'poll' => $poll,
        'opcoes' => $opcoes
    ];
}

// Função para verificar se o usuário já votou na poll
function hasUserVoted($con, $pollId, $userId)
{
    $sql = "SELECT id FROM poll_votos WHERE poll_id = ? AND utilizador_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $pollId, $userId);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

$userId = $_SESSION["id"];
$sqlPerfil = "SELECT * FROM perfis WHERE id_utilizador = $userId";
$resultPerfil = mysqli_query($con, $sqlPerfil);
$perfilData = mysqli_fetch_assoc($resultPerfil);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orange - Rede Social</title>
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/style_polls.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/video_player.css">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon_orange.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .no-comments {
            text-align: center;
            padding: 20px;
            color: var(--text-secondary);
            font-style: italic;
            border-top: 1px solid var(--border-light);
            margin-top: 15px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .confirmation-modal {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: modalFadeIn 0.3s ease;
        }

        .confirmation-modal h3 {
            margin-top: 0;
            color: var(--text-light);
            font-size: 1.2rem;
        }

        .confirmation-modal p {
            margin: 15px 0 25px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .confirmation-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .confirmation-buttons button {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .confirm-btn {
            background-color: var(--color-primary);
            color: white;
            border: none;
        }

        .confirm-btn:hover {
            background-color: var(--color-primary-dark);
        }

        .cancel-btn {
            background-color: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
        }

        .cancel-btn:hover {
            background-color: var(--bg-input);
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div id="confirmationModal" class="modal-overlay" style="display: none; z-index: 1001;">
        <div class="confirmation-modal">
            <h3>Confirmar ação</h3>
            <p id="confirmationMessage">Tem a certeza que deseja apagar esta publicação?</p>
            <div class="confirmation-buttons">
                <button id="confirmCancel" class="cancel-btn">Cancelar</button>
                <button id="confirmAction" class="confirm-btn">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <?php require "parciais/header.php" ?>

    <!-- Comments Modal -->
    <div id="commentsModal" class="modal-overlay" style="display: none; z-index: 1000;">
        <div class="comment-modal">
            <div class="modal-post" id="modalPostContent"></div>
            <div class="modal-comments">
                <div class="comments-list" id="commentsList"></div>
                <form class="comment-form" id="commentForm">
                    <input type="hidden" id="currentPostId" value="">
                    <input type="text" class="comment-input" id="commentInput" placeholder="Adicione um comentário..."
                        required>
                    <button type="submit" class="comment-submit">Publicar</button>
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
        </div>
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

    <div class="container">
        <?php require("parciais/sidebar.php"); ?>

        <main class="feed">
            <!-- Create Post -->
            <div class="create-post">
                <div class="post-input">
                    <img src="images/perfil/<?php echo $perfilData['foto_perfil'] ?: 'default-profile.jpg'; ?>"
                        alt="User" class="profile-pic">
                    <form action="../backend/criar_publicacao.php" method="POST" enctype="multipart/form-data">
                        <textarea name="conteudo" placeholder="O que está a acontecer?"></textarea>
                </div>
                <div class="post-actions">
                    <button type="button" onclick="document.getElementById('media0').click()">
                        <i class="fas fa-image"></i>
                    </button>
                    <button type="button" onclick="document.getElementById('media0').click()">
                        <i class="fas fa-video"></i>
                    </button>
                    <button type="button" class="poll-toggle-btn" onclick="togglePollForm()">
                        <i class="fas fa-poll"></i>
                    </button>
                    <button type="submit" name="publicar" class="publish-btn">Publicar</button>
                </div>

                <!-- Hidden file inputs -->
                <input type="file" id="media0" name="media0" accept="image/*,video/*" style="display: none;" multiple>
                <input type="file" id="media1" name="media1" accept="image/*,video/*" style="display: none;">
                <input type="file" id="media2" name="media2" accept="image/*,video/*" style="display: none;">
                <input type="file" id="media3" name="media3" accept="image/*,video/*" style="display: none;">
                <input type="file" id="media4" name="media4" accept="image/*,video/*" style="display: none;">
                </form>
            </div>

            <!-- Poll Form -->
            <div class="poll-form" id="pollForm" style="display: none;">
                <div class="poll-form-header">
                    <h3 class="poll-form-title">
                        <i class="fas fa-poll"></i>
                        Criar Poll
                    </h3>
                    <button type="button" class="poll-form-close" onclick="hidePollForm()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="pollCreationForm" action="../backend/criar_publicacao_poll.php" method="POST">
                    <div class="poll-form-group">
                        <label class="poll-form-label">Pergunta da Poll *</label>
                        <input type="text" name="pergunta" class="poll-form-input" placeholder="Qual é a sua pergunta?"
                            required maxlength="500">
                    </div>

                    <div class="poll-form-group">
                        <label class="poll-form-label">Descrição (opcional)</label>
                        <textarea name="conteudo" class="poll-form-textarea"
                            placeholder="Adicione uma descrição à sua poll..."></textarea>
                    </div>

                    <div class="poll-form-group">
                        <label class="poll-form-label">Opções *</label>
                        <div class="poll-options-form" id="pollOptionsContainer">
                            <div class="poll-option-input-group">
                                <input type="text" name="opcoes[]" class="poll-form-input poll-option-input"
                                    placeholder="Opção 1" required maxlength="200">
                            </div>
                            <div class="poll-option-input-group">
                                <input type="text" name="opcoes[]" class="poll-form-input poll-option-input"
                                    placeholder="Opção 2" required maxlength="200">
                            </div>
                        </div>
                        <button type="button" class="poll-add-option" id="addOptionBtn" onclick="addPollOption()">
                            <i class="fas fa-plus"></i>
                            Adicionar Opção
                        </button>
                    </div>

                    <div class="poll-form-group">
                        <label class="poll-form-label">Duração da poll</label>
                        <div class="poll-duration-group">
                            <input type="number" name="duracao" class="poll-form-input poll-duration-input" value="24"
                                min="1" max="168" required>
                            <span class="poll-duration-unit">horas</span>
                        </div>
                        <small style="color: var(--text-muted); margin-top: 4px; display: block;">
                            Mínimo: 1 hora | Máximo: 7 dias (168 horas)
                        </small>
                    </div>

                    <div class="poll-form-actions">
                        <button type="button" class="poll-form-cancel" onclick="hidePollForm()">
                            Cancelar
                        </button>
                        <button type="submit" name="publicar_poll" class="poll-form-submit">
                            <i class="fas fa-poll"></i>
                            Publicar Poll
                        </button>
                    </div>
                </form>
            </div>

            <!-- Posts -->
            <div class="posts">
                <?php
                $sql = "SELECT p.id_publicacao, p.conteudo, p.data_criacao, p.likes, p.tipo,
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
                        $checkSql = "SELECT * FROM publicacao_likes 
                                     WHERE publicacao_id = $publicacaoId AND utilizador_id = $currentUserId";
                        $checkResult = mysqli_query($con, $checkSql);
                        if (mysqli_num_rows($checkResult) > 0) {
                            $likedClass = 'liked';
                        }

                        // Verificar se está salvo
                        $savedClass = '';
                        if (isPostSaved($con, $currentUserId, $publicacaoId)) {
                            $savedClass = 'saved';
                        }

                        // Buscar imagens da publicação
                        $images = getPostImages($con, $publicacaoId);

                        // Buscar dados da poll se for uma poll
                        $pollData = null;
                        if ($linha['tipo'] === 'poll') {
                            $pollData = getPollData($con, $publicacaoId);
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
                                <?php if (!empty($linha['conteudo'])): ?>
                                    <p><?php echo nl2br(makeLinksClickable($linha['conteudo'])); ?></p>
                                <?php endif; ?>

                                <?php if ($linha['tipo'] === 'poll' && $pollData !== null): ?>
                                    <!-- Poll Content -->
                                    <div class="poll-container" data-poll-id="<?php echo $pollData['poll']['id']; ?>">
                                        <div class="poll-question"><?php echo htmlspecialchars($pollData['poll']['pergunta']); ?>
                                        </div>

                                        <div class="poll-options">
                                            <?php
                                            $hasVoted = hasUserVoted($con, $pollData['poll']['id'], $currentUserId);
                                            $isExpired = strtotime($pollData['poll']['data_expiracao']) < time();

                                            foreach ($pollData['opcoes'] as $opcao):
                                                $percentage = $pollData['poll']['total_votos'] > 0 ?
                                                    round(($opcao['votos'] / $pollData['poll']['total_votos']) * 100, 1) : 0;
                                                ?>
                                                <div class="poll-option <?php echo ($hasVoted || $isExpired) ? 'disabled voted' : ''; ?>"
                                                    data-opcao-id="<?php echo $opcao['id']; ?>" <?php if (!$hasVoted && !$isExpired): ?>
                                                        onclick="voteInPoll(<?php echo $pollData['poll']['id']; ?>, <?php echo $opcao['id']; ?>)"
                                                    <?php endif; ?>>
                                                    <div class="poll-option-progress" style="width: <?php echo $percentage; ?>%"></div>
                                                    <div class="poll-option-content">
                                                        <span
                                                            class="poll-option-text"><?php echo htmlspecialchars($opcao['opcao_texto']); ?></span>
                                                        <?php if ($hasVoted || $isExpired): ?>
                                                            <div class="poll-option-stats">
                                                                <span class="poll-option-percentage"><?php echo $percentage; ?>%</span>
                                                                <span class="poll-option-votes"><?php echo $opcao['votos']; ?>
                                                                    voto<?php echo $opcao['votos'] !== 1 ? 's' : ''; ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="poll-meta">
                                            <span class="poll-total-votes"><?php echo $pollData['poll']['total_votos']; ?>
                                                voto<?php echo $pollData['poll']['total_votos'] !== 1 ? 's' : ''; ?></span>
                                            <span class="poll-time-left <?php echo $isExpired ? 'poll-expired' : ''; ?>">
                                                <i class="fas fa-clock"></i>
                                                <?php
                                                if ($isExpired) {
                                                    echo 'Poll encerrada';
                                                } else {
                                                    $timeLeft = strtotime($pollData['poll']['data_expiracao']) - time();
                                                    $hours = floor($timeLeft / 3600);
                                                    $minutes = floor(($timeLeft % 3600) / 60);
                                                    echo "Encerra em {$hours}h {$minutes}m";
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

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
                                        elseif ($imageCount == 4)
                                            $gridClass = 'quad';
                                        else
                                            $gridClass = 'multiple';
                                        ?>
                                        <div class="images-grid <?php echo $gridClass; ?>">
                                            <?php foreach ($images as $i => $media): ?>
                                                <?php if ($i < 4 || $imageCount <= 4): ?>
                                                    <div class="media-item"
                                                        onclick="openMediaModal(<?php echo $publicacaoId; ?>, <?php echo $i; ?>)">
                                                        <?php if ($media['tipo'] === 'video'): ?>
                                                            <div class="video-container">
                                                                <video muted preload="metadata" playsInline>
                                                                    <source
                                                                        src="images/publicacoes/<?php echo htmlspecialchars($media['url']); ?>"
                                                                        type="video/mp4">
                                                                    Seu navegador não suporta vídeos.
                                                                </video>
                                                            </div>
                                                        <?php else: ?>
                                                            <img src="images/publicacoes/<?php echo htmlspecialchars($media['url']); ?>"
                                                                alt="Imagem da publicação" class="post-media">
                                                        <?php endif; ?>
                                                        <?php if ($i == 3 && $imageCount > 4): ?>
                                                            <div class="more-images-overlay">
                                                                +<?php echo $imageCount - 4; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
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
                                <button><i class="fas fa-share"></i></button>
                                <button class="save-btn <?php echo $savedClass; ?>"
                                    data-publicacao-id="<?php echo $publicacaoId; ?>">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                                <?php if ($currentUserId == $linha['id_utilizador'] || $currentUserType == 2): ?>
                                    <button class="delete-btn" onclick="deletePost(<?php echo $publicacaoId; ?>, this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php
                    }
                } else {
                    echo "<p class='no-posts'>Nenhuma publicação ainda. Seja o primeiro a publicar!</p>";
                }
                ?>
            </div>

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

    <?php require "parciais/footer.php" ?>

    <!-- Include Video Player JavaScript -->
    <script src="js/video-player.js"></script>
    <script src="js/polls.js"></script>

    <script>
        // Variáveis globais para controle da confirmação
        let pendingDelete = {
            postId: null,
            element: null,
            type: null
        };

        // Poll form management
        let isPollFormVisible = false;

        function togglePollForm() {
            if (isPollFormVisible) {
                hidePollForm();
            } else {
                showPollForm();
            }
        }

        function showPollForm() {
            const pollForm = document.getElementById('pollForm');
            const createPost = document.querySelector('.create-post');
            const toggleBtn = document.querySelector('.poll-toggle-btn');

            pollForm.style.display = 'block';
            createPost.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
            isPollFormVisible = true;
        }

        function hidePollForm() {
            const pollForm = document.getElementById('pollForm');
            const createPost = document.querySelector('.create-post');
            const toggleBtn = document.querySelector('.poll-toggle-btn');

            pollForm.style.display = 'none';
            createPost.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-poll"></i>';
            isPollFormVisible = false;

            // Reset form
            document.getElementById('pollCreationForm').reset();
            resetPollOptions();
        }

        function addPollOption() {
            const container = document.getElementById('pollOptionsContainer');
            const currentOptions = container.querySelectorAll('.poll-option-input-group');

            if (currentOptions.length >= 4) {
                showToast('Máximo de 4 opções permitidas');
                return;
            }

            const optionHTML = `
                <div class="poll-option-input-group">
                    <input type="text" name="opcoes[]" class="poll-form-input poll-option-input" 
                           placeholder="Opção ${currentOptions.length + 1}" required maxlength="200">
                    <button type="button" class="poll-option-remove" onclick="removePollOption(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', optionHTML);
            updateAddButton();
        }

        function removePollOption(button) {
            const container = document.getElementById('pollOptionsContainer');
            const currentOptions = container.querySelectorAll('.poll-option-input-group');

            if (currentOptions.length <= 2) {
                showToast('Mínimo de 2 opções necessárias');
                return;
            }

            button.closest('.poll-option-input-group').remove();
            updateAddButton();
            updatePlaceholders();
        }

        function updateAddButton() {
            const container = document.getElementById('pollOptionsContainer');
            const addBtn = document.getElementById('addOptionBtn');
            const currentOptions = container.querySelectorAll('.poll-option-input-group');

            if (addBtn) {
                addBtn.disabled = currentOptions.length >= 4;
            }
        }

        function updatePlaceholders() {
            const container = document.getElementById('pollOptionsContainer');
            const inputs = container.querySelectorAll('.poll-option-input');

            inputs.forEach((input, index) => {
                input.placeholder = `Opção ${index + 1}`;
            });
        }

        function resetPollOptions() {
            const container = document.getElementById('pollOptionsContainer');
            const options = container.querySelectorAll('.poll-option-input-group');

            // Remove extra options, keep only 2
            for (let i = options.length - 1; i >= 2; i--) {
                options[i].remove();
            }

            updateAddButton();
            updatePlaceholders();
        }

        // Vote in poll function
        async function voteInPoll(pollId, opcaoId) {
            try {
                const formData = new FormData();
                formData.append('poll_id', pollId);
                formData.append('opcao_id', opcaoId);

                const response = await fetch('../backend/votar_poll.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Voto registado com sucesso!');
                    // Reload the page to show updated results
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'Erro ao votar');
                }
            } catch (error) {
                console.error('Erro ao votar:', error);
                showToast('Erro de conexão');
            }
        }

        // Função para apagar publicação com modal de confirmação
        function deletePost(postId, element) {
            pendingDelete = {
                postId,
                element,
                type: 'post'
            };

            document.getElementById('confirmationMessage').textContent = 'Tem certeza que deseja apagar esta publicação?';
            showConfirmation(function (confirmed) {
                if (confirmed) {
                    fetch('../backend/delete_post.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_publicacao=${postId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                element.closest('.post').style.opacity = '0';
                                element.closest('.post').style.transform = 'translateX(-100px)';
                                setTimeout(() => {
                                    element.closest('.post').remove();
                                }, 300);
                                showToast('Publicação apagada com sucesso');
                            } else {
                                showToast('Erro ao apagar publicação');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Erro ao apagar publicação');
                        });
                }
            });
        }

        // Função para apagar comentário com modal de confirmação
        function deleteComment(commentId, element) {
            pendingDelete = {
                commentId,
                element,
                type: 'comment'
            };

            document.getElementById('confirmationMessage').textContent = 'Tem a certeza que deseja apagar este comentário?';
            showConfirmation(function (confirmed) {
                if (confirmed) {
                    fetch('../backend/delete_comment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_comentario=${commentId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                element.closest('.comment-item').remove();
                                showToast('Comentário apagado com sucesso');

                                const commentCount = document.querySelector(`.comment-btn[onclick*="${currentPostId}"] .comment-count`);
                                if (commentCount) {
                                    commentCount.textContent = parseInt(commentCount.textContent) - 1;
                                }
                            } else {
                                showToast('Erro ao apagar comentário');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Erro ao apagar comentário');
                        });
                }
            });
        }

        // Função para mostrar o modal de confirmação
        function showConfirmation(callback) {
            const modal = document.getElementById('confirmationModal');
            const confirmBtn = document.getElementById('confirmAction');
            const cancelBtn = document.getElementById('confirmCancel');

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            confirmBtn.onclick = null;
            cancelBtn.onclick = null;

            confirmBtn.onclick = function () {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                callback(true);
            };

            cancelBtn.onclick = function () {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                callback(false);
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            initializeVideoThumbnails();
            document.getElementById('commentsModal').style.display = 'none';
        });

        // Sistema de visualização de mídia
        let currentImageModal = {
            postId: null,
            currentIndex: 0,
            medias: []
        };

        function openMediaModal(postId, mediaIndex = 0) {
            const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
            if (!postElement) return;

            const medias = [];
            const mediaElements = postElement.querySelectorAll('.media-item');

            mediaElements.forEach(item => {
                const videoElement = item.querySelector('video');
                const imgElement = item.querySelector('img');

                if (videoElement) {
                    const source = videoElement.querySelector('source');
                    medias.push({
                        url: source ? source.src.split('/').pop() : '',
                        tipo: 'video'
                    });
                } else if (imgElement) {
                    medias.push({
                        url: imgElement.src.split('/').pop(),
                        tipo: 'imagem'
                    });
                }
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

            modalContent.innerHTML = '';

            const currentMedia = currentImageModal.medias[currentImageModal.currentIndex];

            if (currentMedia.tipo === 'video') {
                const videoContainer = document.createElement('div');
                videoContainer.className = 'modal-video-container';

                const video = document.createElement('video');
                video.autoplay = false;
                video.controls = false;
                video.className = 'modal-media';
                video.muted = false;
                video.preload = 'metadata';
                video.playsInline = true;

                const source = document.createElement('source');
                source.src = `images/publicacoes/${currentMedia.url}`;
                source.type = 'video/mp4';

                video.appendChild(source);
                video.appendChild(document.createTextNode('Seu navegador não suporta vídeos.'));
                videoContainer.appendChild(video);
                modalContent.appendChild(videoContainer);

                setTimeout(() => {
                    new ModernVideoPlayer(video);
                }, 100);
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
            showMediaInModal();
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
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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

            const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
            if (postElement) {
                const postClone = postElement.cloneNode(true);
                const actions = postClone.querySelector('.post-actions');
                if (actions) actions.remove();

                document.getElementById('modalPostContent').innerHTML = '';
                document.getElementById('modalPostContent').appendChild(postClone);

                loadComments(postId);
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
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

                            const commentCount = document.querySelector(`.comment-btn[onclick*="${currentPostId}"] .comment-count`);
                            if (commentCount) {
                                commentCount.textContent = parseInt(commentCount.textContent) + 1;
                            }
                        }
                    });
            }
        });

        function loadComments(postId) {
            fetch(`../backend/get_comments.php?post_id=${postId}`)
                .then(response => response.json())
                .then(comments => {
                    const commentsList = document.getElementById('commentsList');
                    commentsList.innerHTML = '';

                    if (comments.length === 0) {
                        const noCommentsMsg = document.createElement('div');
                        noCommentsMsg.className = 'no-comments';
                        noCommentsMsg.textContent = 'Ainda sem comentários. Seja o primeiro a comentar!';
                        commentsList.appendChild(noCommentsMsg);
                        return;
                    }

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
                            <div class="comment-user-info">
                                <a href="perfil.php?id=${comment.utilizador_id}" class="profile-link">
                                    <span class="comment-username">${comment.nick}</span>
                                </a>
                                <span class="comment-time">${dataComentarioFormatada}</span>
                            </div>
                            ${(<?php echo $_SESSION['id']; ?> == comment.utilizador_id || <?php echo $_SESSION['id_tipos_utilizador']; ?> == 2) ?
                                `<button class="delete-comment-btn" onclick="deleteComment(${comment.id}, this)">
                                    <i class="fas fa-trash"></i>
                                </button>` : ''}
                        </div>
                        <p class="comment-text">${comment.conteudo}</p>
                    </div>
                `;
                        commentsList.appendChild(commentItem);
                    });
                });
        }
    </script>
</body>

</html>

<?php
if (isset($_SESSION["erro"])) {
    echo "<script>showToast('" . addslashes($_SESSION["erro"]) . "');</script>";
    unset($_SESSION["erro"]);
}

if (isset($_SESSION["sucesso"])) {
    echo "<script>showToast('" . addslashes($_SESSION["sucesso"]) . "');</script>";
    unset($_SESSION["sucesso"]);
}
?>