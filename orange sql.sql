-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/06/2025 às 16:54
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `orange`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `id_publicacao` int(11) DEFAULT NULL,
  `utilizador_id` int(11) DEFAULT NULL,
  `conteudo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `id_publicacao`, `utilizador_id`, `conteudo`, `data`) VALUES
(57, 176, 78, 'a', '2025-06-23 18:34:23'),
(58, 173, 78, 'a', '2025-06-23 18:34:34'),
(59, 177, 78, 'aa', '2025-06-23 20:05:31'),
(60, 177, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-23 20:05:33'),
(61, 175, 78, 'ola', '2025-06-23 20:05:47'),
(62, 177, 78, 'a', '2025-06-24 12:19:22');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id_perfil` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `biografia` varchar(255) DEFAULT '',
  `foto_perfil` varchar(255) DEFAULT 'images/default-profile.jpg',
  `data_criacao` date NOT NULL DEFAULT current_timestamp(),
  `foto_capa` varchar(255) DEFAULT 'images/default.png',
  `x` varchar(255) NOT NULL DEFAULT '',
  `linkedin` varchar(255) NOT NULL DEFAULT '',
  `github` varchar(255) NOT NULL DEFAULT '',
  `ocupacao` varchar(255) NOT NULL,
  `pais` varchar(255) NOT NULL DEFAULT '',
  `cidade` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `perfis`
--

INSERT INTO `perfis` (`id_perfil`, `id_utilizador`, `biografia`, `foto_perfil`, `data_criacao`, `foto_capa`, `x`, `linkedin`, `github`, `ocupacao`, `pais`, `cidade`) VALUES
(4, 78, 'aluno mais arrependido do curso q escolheu', 'perfil_67ccd1cce44eb.jpeg', '2025-02-04', 'capa_684ca5aa37e7d.jpg', 'https://x.com/_afonso_silvaa', 'https://www.linkedin.com/in/afonso-silva-7b65552b2/', 'https://github.com/s1lva27', 'Estudante', 'Hungria', 'Szombathely'),
(5, 79, '', 'default-profile.jpg', '2025-02-05', 'default-capa.png', '', '', '', '', 'BÃ©lgica', 'Aalst'),
(6, 80, '', 'perfil_67abd8605e695.jpg', '2025-02-11', 'capa_67abd8410575c.png', '', '', '', '', '', ''),
(7, 81, 'QUERO JOBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'perfil_67cf7a087f212.jpg', '2025-02-12', 'default-capa.png', '', '', '', '', 'Chipre', 'Pafos'),
(8, 82, '', 'default-profile.jpg', '2025-02-12', 'default-capa.png', '', '', '', '', '', ''),
(9, 83, '', 'default-profile.jpg', '2025-02-12', 'default-capa.png', '', '', '', '', '', ''),
(10, 84, '', 'default-profile.jpg', '2025-02-12', 'default-capa.png', '', '', '', '', '', ''),
(11, 85, '', 'default-profile.jpg', '2025-03-09', 'default-capa.png', '', '', '', '', '', ''),
(12, 86, 'moro na granja do ulmeiro', 'perfil_683df190346dc.jpg', '2025-06-02', 'capa_683df19770367.jpg', '', '', '', 'Estudante', 'Portugal', 'Lisboa'),
(13, 87, 'Yo soy di eslovacia', 'perfil_683f10b0c2fe2.jpg', '2025-06-03', 'capa_683f10bbc993d.jpg', '', '', '', 'Stripper', 'Polónia', 'Katowice'),
(14, 88, '', 'default-profile.jpg', '2025-06-03', 'default-capa.png', '', '', '', '', '', ''),
(15, 89, '', 'default-profile.jpg', '2025-06-10', 'default-capa.png', 'asas', 'asas', 'https://github.com/s1lva27/Orange/blob/master/frontend/css/style_index.css', '', 'Irlanda', 'Dublin'),
(16, 90, '', 'default-profile.jpg', '2025-06-19', 'default-capa.png', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacao_likes`
--

CREATE TABLE `publicacao_likes` (
  `id` int(11) NOT NULL,
  `publicacao_id` int(11) DEFAULT NULL,
  `utilizador_id` int(11) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `publicacao_likes`
--

INSERT INTO `publicacao_likes` (`id`, `publicacao_id`, `utilizador_id`, `data`) VALUES
(165, 173, 78, '2025-06-23 17:53:24'),
(166, 174, 78, '2025-06-23 17:53:34'),
(167, 175, 78, '2025-06-23 17:53:35'),
(168, 176, 78, '2025-06-23 17:54:06'),
(170, 177, 78, '2025-06-23 18:34:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacao_medias`
--

CREATE TABLE `publicacao_medias` (
  `id` int(11) NOT NULL,
  `publicacao_id` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `content_warning` enum('none','nudity','violence') NOT NULL DEFAULT 'none',
  `ordem` int(11) NOT NULL DEFAULT 0,
  `tipo` enum('imagem','video') NOT NULL DEFAULT 'imagem'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `publicacao_medias`
--

INSERT INTO `publicacao_medias` (`id`, `publicacao_id`, `url`, `content_warning`, `ordem`, `tipo`) VALUES
(128, 176, 'pub_1750701221_0_685994a54a7f8.mp4', 'none', 0, 'video'),
(129, 177, 'pub_1750703062_0_68599bd6e7996.png', 'none', 0, 'imagem'),
(130, 177, 'pub_1750703062_1_68599bd6e802b.png', 'none', 1, 'imagem'),
(131, 177, 'pub_1750703062_2_68599bd6e8654.jpeg', 'none', 2, 'imagem'),
(132, 177, 'pub_1750703062_3_68599bd6e8a4d.png', 'none', 3, 'imagem'),
(133, 177, 'pub_1750703062_4_68599bd6e8e60.jpeg', 'none', 4, 'imagem');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacao_salvas`
--

CREATE TABLE `publicacao_salvas` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `publicacao_id` int(11) NOT NULL,
  `data_salvamento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `publicacao_salvas`
--

INSERT INTO `publicacao_salvas` (`id`, `utilizador_id`, `publicacao_id`, `data_salvamento`) VALUES
(102, 78, 176, '2025-06-23 17:54:15'),
(105, 78, 175, '2025-06-23 20:05:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacoes`
--

CREATE TABLE `publicacoes` (
  `id_publicacao` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `conteudo` text NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `deletado_em` datetime NOT NULL,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `publicacoes`
--

INSERT INTO `publicacoes` (`id_publicacao`, `id_utilizador`, `conteudo`, `categoria`, `data_criacao`, `deletado_em`, `likes`) VALUES
(173, 78, 'ORANGE TA TAO FODIDA', NULL, '2025-06-23 17:53:23', '0000-00-00 00:00:00', 1),
(174, 78, 'TAO GOSTOSA', NULL, '2025-06-23 17:53:28', '0000-00-00 00:00:00', 1),
(175, 78, 'A', NULL, '2025-06-23 17:53:32', '0000-00-00 00:00:00', 1),
(176, 78, '', NULL, '2025-06-23 17:53:41', '0000-00-00 00:00:00', 1),
(177, 78, '', NULL, '2025-06-23 18:24:22', '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `seguidores`
--

CREATE TABLE `seguidores` (
  `id_seguidor` int(11) NOT NULL,
  `id_seguido` int(11) NOT NULL,
  `data_seguido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `seguidores`
--

INSERT INTO `seguidores` (`id_seguidor`, `id_seguido`, `data_seguido`) VALUES
(78, 78, '2025-06-03 21:25:00'),
(78, 79, '2025-06-13 22:17:29'),
(78, 80, '2025-06-13 22:17:39'),
(78, 81, '2025-06-13 22:17:18'),
(78, 82, '2025-06-13 22:22:34'),
(81, 78, '2025-05-27 12:02:52'),
(86, 78, '2025-06-02 18:47:32'),
(87, 78, '2025-06-03 15:12:47'),
(87, 86, '2025-06-03 15:12:41'),
(89, 78, '2025-06-13 22:24:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_utilizador`
--

CREATE TABLE `tipos_utilizador` (
  `id_tipos_utilizador` int(11) NOT NULL,
  `tipo_utilizador` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_utilizador`
--

INSERT INTO `tipos_utilizador` (`id_tipos_utilizador`, `tipo_utilizador`) VALUES
(0, 'utilizador'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(255) NOT NULL DEFAULT 'NOVO_UTILIZADOR',
  `email` varchar(255) NOT NULL,
  `palavra_passe` varchar(255) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `nick` varchar(50) NOT NULL,
  `id_tipos_utilizador` int(11) DEFAULT 0,
  `data_criacao` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome_completo`, `email`, `palavra_passe`, `data_nascimento`, `nick`, `id_tipos_utilizador`, `data_criacao`) VALUES
(78, 'Afonso Silva', 'imafonsosilva@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2007-12-27', 'silvarealz14', 2, '2025-02-04 17:34:09.071263'),
(79, 'Afonso Silva', 'afonso22roblox@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2002-12-11', 's.ilvaaa', 0, '2025-02-05 11:19:20.330537'),
(80, 'Joao', 'joao@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2010-11-11', 'joaofps', 2, '2025-02-11 23:05:22.674980'),
(81, 'Martim', 'martim@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2007-12-12', 'martim_do_job', 0, '2025-02-12 07:39:11.306499'),
(82, 'Tomas', 'tomas@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2002-10-22', 'tomas13', 0, '2025-02-12 07:39:53.000266'),
(83, 'Joana', 'joanaa@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2003-03-03', 'joana12', 0, '2025-02-12 07:40:20.294933'),
(84, 'Luis', 'luis@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2005-03-05', 'luis', 2, '2025-02-12 07:40:59.696546'),
(85, 'Luisa', 'luisa@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2005-12-12', 'luisafofinha', 0, '2025-03-09 14:16:04.740615'),
(86, 'Matilde Alves', 'matildealves@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2007-12-05', 'matxiudi', 0, '2025-06-02 18:45:26.635706'),
(87, 'Duarte Lopes', 'duarte.v.lopeo@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2007-11-16', 'dudzfn', 0, '2025-06-03 15:10:42.570645'),
(88, 'Duarte Lopes', 'aas@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2000-11-11', 'dudzfn_1', 0, '2025-06-03 15:15:08.310135'),
(89, 'Gouveia', 'gouveuaaa@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '1999-11-11', 'gougou', 0, '2025-06-10 18:45:10.639913'),
(90, 'Zdoca', 'david.fcg07@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '2000-11-11', 'zddeis', 0, '2025-06-19 16:43:17.104718');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilizador` (`utilizador_id`),
  ADD KEY `idx_data` (`data`),
  ADD KEY `idx_publicacao_utilizador` (`id_publicacao`,`utilizador_id`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id_perfil`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices de tabela `publicacao_likes`
--
ALTER TABLE `publicacao_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_like` (`publicacao_id`,`utilizador_id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `idx_data` (`data`);

--
-- Índices de tabela `publicacao_medias`
--
ALTER TABLE `publicacao_medias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publicacao_id` (`publicacao_id`);

--
-- Índices de tabela `publicacao_salvas`
--
ALTER TABLE `publicacao_salvas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `publicacao_id` (`publicacao_id`);

--
-- Índices de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  ADD PRIMARY KEY (`id_publicacao`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices de tabela `seguidores`
--
ALTER TABLE `seguidores`
  ADD PRIMARY KEY (`id_seguidor`,`id_seguido`),
  ADD KEY `id_seguido` (`id_seguido`);

--
-- Índices de tabela `tipos_utilizador`
--
ALTER TABLE `tipos_utilizador`
  ADD PRIMARY KEY (`id_tipos_utilizador`);

--
-- Índices de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nome_usuario` (`nick`),
  ADD KEY `id_tipos_utilizador` (`id_tipos_utilizador`) USING BTREE;

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `publicacao_likes`
--
ALTER TABLE `publicacao_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT de tabela `publicacao_medias`
--
ALTER TABLE `publicacao_medias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de tabela `publicacao_salvas`
--
ALTER TABLE `publicacao_salvas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  MODIFY `id_publicacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT de tabela `tipos_utilizador`
--
ALTER TABLE `tipos_utilizador`
  MODIFY `id_tipos_utilizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_publicacao`) REFERENCES `publicacoes` (`id_publicacao`),
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `fk_publicacao` FOREIGN KEY (`id_publicacao`) REFERENCES `publicacoes` (`id_publicacao`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfis`
--
ALTER TABLE `perfis`
  ADD CONSTRAINT `perfis_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`);

--
-- Restrições para tabelas `publicacao_likes`
--
ALTER TABLE `publicacao_likes`
  ADD CONSTRAINT `publicacao_likes_ibfk_1` FOREIGN KEY (`publicacao_id`) REFERENCES `publicacoes` (`id_publicacao`),
  ADD CONSTRAINT `publicacao_likes_ibfk_2` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`);

--
-- Restrições para tabelas `publicacao_medias`
--
ALTER TABLE `publicacao_medias`
  ADD CONSTRAINT `publicacao_medias_ibfk_1` FOREIGN KEY (`publicacao_id`) REFERENCES `publicacoes` (`id_publicacao`);

--
-- Restrições para tabelas `publicacao_salvas`
--
ALTER TABLE `publicacao_salvas`
  ADD CONSTRAINT `publicacao_salvas_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `publicacao_salvas_ibfk_2` FOREIGN KEY (`publicacao_id`) REFERENCES `publicacoes` (`id_publicacao`);

--
-- Restrições para tabelas `publicacoes`
--
ALTER TABLE `publicacoes`
  ADD CONSTRAINT `publicacoes_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`);

--
-- Restrições para tabelas `seguidores`
--
ALTER TABLE `seguidores`
  ADD CONSTRAINT `seguidores_ibfk_1` FOREIGN KEY (`id_seguidor`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `seguidores_ibfk_2` FOREIGN KEY (`id_seguido`) REFERENCES `utilizadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
