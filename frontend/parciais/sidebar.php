<?php
$currentUserId = $_SESSION['id'] ?? 0;

// Query to get conversations and unread messages count
$sqlConversas = "SELECT c.id, c.utilizador1_id, c.utilizador2_id, c.ultima_atividade,
                        u1.nick as nick1, u1.nome_completo as nome1, p1.foto_perfil as foto1,
                        u2.nick as nick2, u2.nome_completo as nome2, p2.foto_perfil as foto2,
                        (SELECT conteudo FROM mensagens WHERE conversa_id = c.id ORDER BY data_envio DESC LIMIT 1) as ultima_mensagem,
                        (SELECT COUNT(*) FROM mensagens WHERE conversa_id = c.id AND remetente_id != $currentUserId AND lida = 0) as mensagens_nao_lidas
                 FROM conversas c
                 JOIN utilizadores u1 ON c.utilizador1_id = u1.id
                 JOIN utilizadores u2 ON c.utilizador2_id = u2.id
                 LEFT JOIN perfis p1 ON u1.id = p1.id_utilizador
                 LEFT JOIN perfis p2 ON u2.id = p2.id_utilizador
                 WHERE c.utilizador1_id = $currentUserId OR c.utilizador2_id = $currentUserId
                 ORDER BY c.ultima_atividade DESC";

$result = mysqli_query($con, $sqlConversas);
$totalUnread = 0;

if ($result) {
    while ($conversa = mysqli_fetch_assoc($result)) {
        $totalUnread += (int) $conversa['mensagens_nao_lidas'];
    }
}
?>

<!-- Left Sidebar -->
<aside class="sidebar">
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a></li>
            <li><a href="#"><i class="fas fa-briefcase"></i> <span>Trabalho</span></a></li>
            <li><a href="mensagens.php"><i class="fas fa-comments"></i> <span>Mensagens</span>
                    <?php if ($totalUnread > 0): ?>
                        <span id="unread-count-badge" class="notification-badge animate-float"><?= $totalUnread ?></span>
                    <?php else: ?>
                        <span id="unread-count-badge" class="notification-badge" style="display:none;">0</span>
                    <?php endif; ?>
                </a></li>
            <li><a href="#"><i class="fas fa-bell"></i> <span>Notificações</span></a></li>
            <li><a href="itens_salvos.php"><i class="fas fa-bookmark"></i> <span>Itens Salvos</span></a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> <span>Estatisticas</span></a></li>
        </ul>
    </nav>
</aside>

<script>
    var sidebar = document.querySelector(".sidebar");
    var link = window.location.href;
    Array.from(sidebar.querySelectorAll("a")).forEach(element => {
        console.log(element.href);
        if (link == element.href) {
            element.classList.add("active");
        }
    });

    // Função para atualizar o contador global
    function updateUnreadCount(change) {
        const badge = document.getElementById('unread-count-badge');
        if (!badge) return;

        let currentCount = parseInt(badge.textContent) || 0;
        let newCount = currentCount + change;

        // Garantir que não fique negativo
        newCount = Math.max(0, newCount);

        // Atualizar o badge
        badge.textContent = newCount;

        // Mostrar ou esconder conforme necessário
        if (newCount > 0) {
            badge.style.display = 'inline-flex';
            badge.classList.add('animate-float');
        } else {
            badge.style.display = 'none';
            badge.classList.remove('animate-float');
        }

        // Adicionar animação de mudança
        badge.classList.add('animate-pop');
        setTimeout(() => {
            badge.classList.remove('animate-pop');
        }, 300);
    }

    // Ouvir eventos de atualização (será chamado de mensagens.php)
    document.addEventListener('unreadCountUpdated', function (e) {
        updateUnreadCount(e.detail.change);
    });

    // Sincronização entre abas
    window.addEventListener('storage', function (e) {
        if (e.key === 'unreadCountUpdate') {
            const data = JSON.parse(e.newValue);
            updateUnreadCount(data.change);
        }
    });
</script>

<style>
    .notification-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff4444, #ff0000);
        color: white;
        border-radius: 12px;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        font-size: 11px;
        font-weight: bold;
        margin-left: 8px;
        box-shadow: 0 3px 8px rgba(255, 0, 0, 0.3);
        position: relative;
        top: -2px;
        transform-origin: center;
    }

    /* Animação de flutuação contínua */
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    /* Animação quando aparece */
    .notification-badge {
        animation: pop-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-3px);
        }
    }

    @keyframes pop-in {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        80% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Efeito hover pulsante */
    .notification-badge:hover {
        animation: pulse 1s ease infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.4);
        }

        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 8px rgba(255, 0, 0, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
        }
    }

    /* Brilho intermitente */
    .notification-badge::after {
        content: '';
        position: absolute;
        top: -5px;
        right: -5px;
        width: 10px;
        height: 10px;
        background: white;
        border-radius: 50%;
        opacity: 0.8;
        animation: blink 2s infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 0.8;
            transform: scale(1);
        }

        50% {
            opacity: 0;
            transform: scale(0.5);
        }
    }

    .notification-badge {
        background: linear-gradient(135deg, #ff4444, #ff0000);
        background-size: 200% 200%;
        animation: gradient-shift 4s ease infinite;
    }

    @keyframes gradient-shift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
</style>