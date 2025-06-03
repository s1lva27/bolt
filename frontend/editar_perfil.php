<?php
session_start();
require "../backend/ligabd.php";

// Verificar se o utilizador está autenticado
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["id"];

// Buscar informações do utilizador na tabela "utilizadores"
$sqlUser = "SELECT * FROM utilizadores WHERE id = $userId";
$resultUser = mysqli_query($con, $sqlUser);
$userData = mysqli_fetch_assoc($resultUser);

$_SESSION = $userData;

// Buscar informações do perfil na tabela "perfis"
$sqlPerfil = "SELECT * FROM perfis WHERE id_utilizador = $userId";
$resultPerfil = mysqli_query($con, $sqlPerfil);
$perfilData = mysqli_fetch_assoc($resultPerfil);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações de Perfil - Orange</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/style_editar_perfil.css">
    <link rel="stylesheet" href="css/popup.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon_orange.png">
    <script src="https://unpkg.com/lucide@latest"></script>


</head>



<body>

    <?php
    require "parciais/header.php";
    ?>

    <!-- Conteúdo Principal -->
    <div class="profile-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile-picture">
                <?php
                $fotoPerfil = !empty($perfilData['foto_perfil']) ? "../frontend/images/perfil/" . $perfilData['foto_perfil'] : "images/default-profile.jpg";
                ?>

                <form action="../backend/upload_foto.php" method="POST" enctype="multipart/form-data">

                    <label for="fotoInput">
                        <img id="profile-img" src="<?php echo $fotoPerfil; ?>" alt="Foto do Perfil">
                    </label>

                    <input type="file" name="foto" id="fotoInput" accept="image/*" style="display: none;" required>
                    <button type="submit" name="submit" id="uploadForm" style="display: none;"></button>
                </form>

                <script>
                    document.getElementById('fotoInput').addEventListener('change', function () {
                        document.getElementById('uploadForm').click();
                    });
                </script>
            </div>

            <nav class="profile-nav">
                <a href="#profile-info" id="1" onclick="update_nav('1')" class="active">Informações</a>
                <a href="#professional-info" id="2" onclick="update_nav('2')">Profissional</a>
                <a href="#social-info" id="3" onclick="update_nav('3')">Redes Sociais</a>
                <a href="#security-info" id="4" onclick="update_nav('4')">Segurança</a>
            </nav>


            <script>
                function update_nav(id) {
                    var element = document.getElementById(id);

                    document.getElementById("1").classList.remove("active");
                    document.getElementById("2").classList.remove("active");
                    document.getElementById("3").classList.remove("active");
                    document.getElementById("4").classList.remove("active");

                    element.classList.add("active")
                }
            </script>
        </aside>
        <!-- Modal de Upload Moderno -->
        <!-- <div id="upload-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Foto de Perfil</h2>
        
        <div class="crop-container">
            <div class="crop-area">
                <img id="crop-image" src="" alt="Foto do Perfil">
            </div>
        </div>
        
        <input type="range" id="zoom-slider" min="1" max="3" step="0.1" value="1">
        
        <button id="save-btn">Salvar</button>
    </div>
</div> -->

        <!-- <style>
            /* Estilo do Modal Moderno */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(8px);
            }

            .modal-content {
                background: white;
                padding: 20px;
                border-radius: 12px;
                text-align: center;
                width: 320px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
                animation: fadeIn 0.3s ease-in-out;
            }

            .close {
                position: absolute;
                top: 10px;
                right: 15px;
                font-size: 20px;
                cursor: pointer;
                color: #333;
            }

            .crop-container {
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 15px 0;
            }

            .crop-area {
                width: 150px;
                height: 150px;
                border-radius: 50%;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid #f97316;
            }

            #crop-image {
                max-width: 100%;
                cursor: grab;
            }

            #zoom-slider {
                width: 80%;
                margin: 10px 0;
            }

            #save-btn {
                background: #f97316;
                color: white;
                padding: 10px 20px;
                border: none;
                cursor: pointer;
                border-radius: 8px;
                transition: 0.3s;
            }

            #save-btn:hover {
                background: #e26210;
            }
        </style> -->

        <!-- <script>
            const uploadInput = document.getElementById("upload-pic");
            const modal = document.getElementById("upload-modal");
            const closeModal = document.querySelector(".close");
            const saveBtn = document.getElementById("save-btn");
            const cropImage = document.getElementById("crop-image");
            const zoomSlider = document.getElementById("zoom-slider");
            let offsetX = 0, offsetY = 0, scale = 1;

            uploadInput.addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        cropImage.src = e.target.result;
                        modal.style.display = "flex";
                    };
                    reader.readAsDataURL(file);
                }
            });

            let isDragging = false;
            let startX, startY;

            cropImage.addEventListener("mousedown", (e) => {
                isDragging = true;
                startX = e.clientX - offsetX;
                startY = e.clientY - offsetY;
            });

            document.addEventListener("mousemove", (e) => {
                if (!isDragging) return;
                offsetX = e.clientX - startX;
                offsetY = e.clientY - startY;
                updateImage();
            });

            document.addEventListener("mouseup", () => {
                isDragging = false;
            });

            zoomSlider.addEventListener("input", (e) => {
                scale = e.target.value;
                updateImage();
            });

            function updateImage() {
                cropImage.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${scale})`;
            }

            closeModal.onclick = function () {
                modal.style.display = "none";
            };

            window.onclick = function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            };

            saveBtn.addEventListener("click", function () {
                document.querySelector(".profile-picture img").src = cropImage.src;
                modal.style.display = "none";
            });
        </script> -->



        <!-- Conteúdo Principal -->
        <section class="profile-content">
            <!-- Informações Básicas -->
            <div id="profile-info" class="section">
                <h2>Informações Básicas</h2>
                <form action="../backend/editar_perfil/informacoes.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $perfilData['id_utilizador']; ?>">


                    <div class="form-group">
                        <label for="name"
                            style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                            Nome de Utilizador:
                        </label>
                        <input type="text" name="nick" value="<?php echo $userData['nick']; ?>">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="localizacao"
                                style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                                Localização:
                            </label>
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px">
                                <select name="país" id="país">
                                    <script src="js/localizacao.js"></script>
                                    <script>
                                        // Preenche o select de países
                                        for (const pais in localizacao) {
                                            const option = document.createElement('option');
                                            option.value = pais;
                                            option.textContent = pais;
                                            // Verifica se o país é o mesmo que foi salvo
                                            if (pais === "<?php echo $perfilData['pais']; ?>") {
                                                option.selected = true;
                                            }
                                            document.getElementById("país").appendChild(option);
                                        }
                                    </script>
                                </select>
                                <select name="cidade" id="cidade">
                                    <script>
                                        var paisesSelect = document.getElementById("país");
                                        var cidadesSelect = document.getElementById("cidade");
    
                                        // Função para preencher as cidades com base no país selecionado
                                        function preencherCidades(paisSelecionado, cidadeSalva) {
                                            // Limpa o select de cidades
                                            cidadesSelect.innerHTML = '<option value="">Selecione uma cidade</option>';
    
                                            // Se um país foi selecionado, preenche o select de cidades
                                            if (paisSelecionado) {
                                                const cidades = localizacao[paisSelecionado];
                                                cidades.forEach(cidade => {
                                                    const option = document.createElement('option');
                                                    option.value = cidade;
                                                    option.textContent = cidade;
                                                    // Verifica se a cidade é a mesma que foi salva
                                                    if (cidade === cidadeSalva) {
                                                        option.selected = true;
                                                    }
                                                    cidadesSelect.appendChild(option);
                                                });
                                            }
                                        }
    
                                        // Preenche as cidades quando a página é carregada
                                        var paisSalvo = "<?php echo $perfilData['pais']; ?>";
                                        var cidadeSalva = "<?php echo $perfilData['cidade']; ?>";
                                        if (paisSalvo) {
                                            preencherCidades(paisSalvo, cidadeSalva);
                                        }
    
                                        // Atualiza as cidades quando o país é alterado
                                        paisesSelect.addEventListener('change', function () {
                                            preencherCidades(this.value, "");
                                        });
                                    </script>
    
    
                                </select>
                            </div>



                        </div>
                    </div>




                    <div class="form-grid">
                        <div class="form-group">
                            <label for="email"
                                style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                                Email:




                            </label>
                            <input type="text" name="email" value="<?php echo $userData['email']; ?>">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="data"
                                style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                                Data de Nascimento:
                            </label>
                            <input type="date" name="data" value="<?php echo $userData['data_nascimento']; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio" class="bio-label" style="font-size: 15px !important;">Biografia</label>

                        <textarea id="bio" name="biografia" rows="4"
                            placeholder="Conte-nos algo sobre si..."></textarea>
                        <script>document.getElementById("bio").value = "<?php echo htmlspecialchars($perfilData['biografia']); ?>"</script>
                    </div>

                    <!-- Botão Salvar -->
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Salvar Alterações</button>
                    </div>
                </form>
            </div>

            <!-- Informações Profissionais -->
            <div id="professional-info" class="section">
                <h2>Informações Profissionais</h2>
                <form action="../backend/editar_perfil/profissional.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $perfilData['id_utilizador']; ?>">

                    <div class="form-group">
                        <label for="data"
                            style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                            Ocupação:




                        </label>
                        <input type="text" name="ocupacao" value="<?php echo $perfilData['ocupacao']; ?>">
                    </div>

                    <!-- Botão Salvar -->
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Salvar Alterações</button>
                    </div>
                </form>
            </div>

            <!-- Redes Sociais -->
            <div id="social-info" class="section">
                <h2>Redes Sociais</h2>
                <form action="../backend/editar_perfil/redes_sociais.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $perfilData['id_utilizador']; ?>">

                    <div class="form-group">
                        <label for="x"
                            style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                            X:
                        </label>
                        <input type="text" name="x" value="<?php echo $perfilData['x']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="linkedin"
                            style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                            LinkedIn:
                        </label>
                        <input type="text" name="linkedin" value="<?php echo $perfilData['linkedin']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="github"
                            style="font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
                            GitHub:
                        </label>
                        <input type="text" name="github" value="<?php echo $perfilData['github']; ?>">
                    </div>

                    <!-- Botão Salvar -->
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Salvar Alterações</button>
                    </div>
                </form>
            </div>

            <!-- Segurança -->
            <div id="security-info" class="section">
                <h2>Segurança</h2>
                <form action="../backend/editar_perfil/seguranca.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $userData['id']; ?>">
                    <div class="form-group">
                        <label for="currentPassword">Mudar Palavra-passe</label>
                        <input type="password" id="currentPassword" name="pass_atual" placeholder="Palavra-passe Atual">
                    </div>
                    <div class="form-group">
                        <input type="password" id="newPassword" name="pass_nova" placeholder="Nova palavra-passe">
                    </div>


                    <!-- Botão Salvar -->
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Salvar Alterações</button>
                    </div>
                </form>
            </div>
            <p id="erro"></p>
        </section>
    </div>

    <script>
        // Inicializa os ícones Lucide
        lucide.createIcons();
    </script>



</body>

</html>

<?php

if (isset($_SESSION["erro"])) {
    $erro = $_SESSION["erro"];

    echo "<script>document.getElementById('erro').textContent = '$erro';</script>";

    unset($_SESSION["erro"]);
}

?>