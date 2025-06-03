<?php
session_start();
require '../backend/ligabd.php';

try {
    // Query para buscar publicações com dados do autor
    $query = "
        SELECT 
            p.*,
            u.nick,
            u.nome_completo,
            pf.foto_perfil
        FROM publicacoes p
        JOIN utilizadores u ON p.id_utilizador = u.id
        LEFT JOIN perfis pf ON u.id = pf.id_utilizador
        WHERE p.deletado_em IS NULL
        ORDER BY p.data_criacao DESC
    ";

    $stmt = $con->prepare($query);
    $stmt->execute();
    $publicacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    die("Erro ao carregar publicações: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Feed de Publicações</title>
    <link rel="stylesheet" href="css/style_publicacoes.css">
    
</head>

<body>
    <div class="feed-container">
        <?php if (!empty($publicacoes)): ?>
            <?php foreach ($publicacoes as $pub): ?>
                <?php
                $fotoPerfil = !empty($pub['foto_perfil']) && $pub['foto_perfil'] !== 'default-profile.jpg'
                ? 'images/perfil/' . $pub['foto_perfil']
                : 'images/default-profile.jpg';
            

                ?>
                <div class="publicacao">
                    <div class="autor-info">
                        <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de Perfil" class="foto-perfil">
                        <div>
                            <h3>
                                <?= htmlspecialchars($pub['nome_completo']) ?>
                            </h3>
                            <p class="nick">@
                                <?= htmlspecialchars($pub['nick']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="conteudo-publicacao">
                        <p>
                            <?= nl2br(htmlspecialchars($pub['conteudo'])) ?>
                        </p>
                    </div>

                    <div class="metadados">
                        <span class="tempo-publicacao">
                            <?= date('d/m/Y H:i', strtotime($pub['data_criacao'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="sem-publicacoes">Ainda não há publicações para mostrar.</p>
        <?php endif; ?>
    </div>
</body>

</html>