-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/06/2025 às 17:32
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
(1, 28, 78, 'o,a', '2025-06-03 20:21:37'),
(2, 28, 78, 'ASJHKDFVAKJSHDFUJASD', '2025-06-03 20:21:47'),
(3, 28, 78, 'ASDDDASDADS', '2025-06-03 20:21:49'),
(4, 28, 78, 'ALALALLALALA', '2025-06-03 20:22:02'),
(5, 25, 78, 'VEM VERAO', '2025-06-03 20:32:47'),
(6, 28, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 20:35:15'),
(7, 28, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 20:35:17'),
(8, 28, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 20:35:18'),
(9, 28, 78, 'AAA', '2025-06-03 20:35:19'),
(10, 28, 78, 'A', '2025-06-03 20:35:19'),
(11, 28, 78, 'A', '2025-06-03 20:35:19'),
(12, 28, 78, 'A', '2025-06-03 20:35:19'),
(13, 28, 78, 'A', '2025-06-03 20:35:20'),
(14, 28, 78, 'A', '2025-06-03 20:35:20'),
(15, 28, 78, 'A', '2025-06-03 20:35:20'),
(16, 28, 78, 'ola, eu contrato te', '2025-06-03 20:37:10'),
(17, 28, 78, 'as', '2025-06-03 20:53:56'),
(18, 28, 78, 'asasas', '2025-06-03 20:53:59'),
(19, 28, 78, 'asas', '2025-06-03 21:00:05'),
(20, 28, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 22:14:51'),
(21, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:26:40'),
(22, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:27:00'),
(23, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:27:19'),
(24, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:27:38'),
(25, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:28:12'),
(26, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:29:24'),
(27, 28, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:37:18'),
(28, 28, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 22:45:52'),
(29, 31, 78, 'A', '2025-06-03 22:47:52'),
(30, 31, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 22:48:48'),
(31, 31, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 22:49:52'),
(32, 31, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-03 23:09:48'),
(33, 31, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-03 23:12:43'),
(34, 32, 78, 'as', '2025-06-10 17:53:20'),
(35, 32, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-06-10 17:53:23'),
(36, 34, 78, 'boa pap', '2025-06-11 15:41:46'),
(37, 35, 78, 'o balao do joao', '2025-06-11 16:12:37'),
(38, 45, 78, 'a', '2025-06-13 19:05:47'),
(39, 47, 78, 'a', '2025-06-13 21:22:51'),
(40, 8, 89, 'pog', '2025-06-13 22:24:37'),
(41, 51, 78, 'eu tambem 🍊🍊🍊🍊🍊', '2025-06-13 22:25:27'),
(42, 60, 78, 'ganda merda', '2025-06-18 15:37:34');

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
(15, 89, '', 'default-profile.jpg', '2025-06-10', 'default-capa.png', 'asas', 'asas', 'https://github.com/s1lva27/Orange/blob/master/frontend/css/style_index.css', '', 'Irlanda', 'Dublin');

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
(6, 26, 86, '2025-06-03 12:28:55'),
(7, 25, 86, '2025-06-03 12:29:17'),
(9, 24, 78, '2025-06-03 12:32:17'),
(15, 26, 78, '2025-06-03 12:36:26'),
(17, 22, 78, '2025-06-03 14:42:36'),
(18, 21, 78, '2025-06-03 14:42:36'),
(20, 19, 78, '2025-06-03 14:42:38'),
(21, 18, 78, '2025-06-03 14:42:39'),
(22, 17, 78, '2025-06-03 14:42:40'),
(23, 16, 78, '2025-06-03 14:42:41'),
(24, 15, 78, '2025-06-03 14:42:41'),
(26, 25, 87, '2025-06-03 15:12:25'),
(42, 28, 78, '2025-06-10 17:50:43'),
(59, 25, 78, '2025-06-10 18:07:05'),
(78, 32, 78, '2025-06-10 18:29:18'),
(79, 31, 78, '2025-06-10 18:35:49'),
(80, 30, 78, '2025-06-10 18:35:51'),
(81, 20, 78, '2025-06-10 18:35:57'),
(83, 31, 89, '2025-06-10 18:50:21'),
(85, 32, 89, '2025-06-10 21:10:28'),
(86, 25, 89, '2025-06-10 21:10:58'),
(87, 30, 89, '2025-06-10 21:29:28'),
(88, 33, 78, '2025-06-11 15:12:28'),
(89, 34, 78, '2025-06-11 15:41:41'),
(90, 35, 78, '2025-06-11 16:11:54'),
(91, 43, 78, '2025-06-11 16:42:21'),
(92, 42, 78, '2025-06-11 16:42:22'),
(93, 41, 78, '2025-06-11 16:42:24'),
(94, 40, 78, '2025-06-11 16:42:24'),
(95, 37, 78, '2025-06-12 21:33:07'),
(97, 23, 78, '2025-06-12 21:54:02'),
(99, 44, 78, '2025-06-12 22:43:05'),
(100, 45, 78, '2025-06-13 19:05:44'),
(102, 47, 78, '2025-06-13 21:44:49'),
(103, 46, 78, '2025-06-13 21:44:51'),
(104, 48, 78, '2025-06-13 22:21:50'),
(105, 8, 89, '2025-06-13 22:24:35'),
(106, 33, 89, '2025-06-13 22:24:46'),
(107, 51, 78, '2025-06-13 22:25:09'),
(108, 56, 78, '2025-06-18 14:13:52'),
(109, 67, 78, '2025-06-18 17:58:48'),
(110, 72, 78, '2025-06-18 18:25:11'),
(111, 73, 78, '2025-06-18 18:29:09'),
(112, 76, 78, '2025-06-19 15:13:08'),
(113, 81, 78, '2025-06-19 15:20:49'),
(114, 59, 78, '2025-06-19 15:21:01'),
(115, 83, 78, '2025-06-19 15:21:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacao_medias`
--

CREATE TABLE `publicacao_medias` (
  `id` int(11) NOT NULL,
  `publicacao_id` int(11) DEFAULT NULL,
  `tipo` enum('imagem','video','outro') NOT NULL,
  `url` varchar(255) NOT NULL,
  `crop_data` text DEFAULT NULL,
  `content_warning` enum('none','nudity','violence') NOT NULL DEFAULT 'none',
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `publicacao_medias`
--

INSERT INTO `publicacao_medias` (`id`, `publicacao_id`, `tipo`, `url`, `crop_data`, `content_warning`, `ordem`) VALUES
(1, 52, 'imagem', 'img_684cb477567a2.jpg', '', 'none', 0),
(2, 53, 'imagem', 'img_684f3a9814e8b.jpg', '', 'nudity', 0),
(3, 55, 'imagem', 'img_684f4224b2547.png', '', 'none', 0),
(4, 56, 'imagem', 'img_6852c93fabc95.jpg', '', 'none', 0),
(5, 57, 'imagem', 'img_6852dd0a7a2c9.jpg', '', 'none', 0),
(6, 58, 'imagem', 'img_6852dd1232137.jpg', '', 'none', 0),
(7, 61, 'imagem', 'img_6852e1de3f37e.jpg', NULL, 'none', 0),
(8, 62, 'imagem', 'img_6852e1f7c5f81.jpg', NULL, 'violence', 0),
(9, 64, 'imagem', 'img_6852fc3020ff6.png', NULL, 'none', 0),
(10, 65, 'imagem', 'img_6852fc365aed4.png', NULL, 'none', 0),
(11, 66, 'imagem', 'img_6852fc5270d21.png', NULL, 'none', 0),
(12, 68, 'imagem', 'img_6852fe625557c.png', NULL, 'none', 0),
(13, 69, 'imagem', 'img_6852ff0321001.png', NULL, 'none', 0),
(14, 70, 'imagem', 'img_6852ff7d634b9.png', NULL, 'none', 0),
(15, 71, 'imagem', 'img_6852ffe896197.png', NULL, 'none', 0),
(16, 72, 'imagem', 'img_68530026f10e3.png', NULL, 'none', 0),
(17, 73, 'imagem', 'img_6853049d60a69.png', NULL, 'none', 0),
(18, 74, 'imagem', 'img_685424372ffef.jpg', NULL, 'none', 0),
(19, 75, 'imagem', 'img_685426b75c27d.png', NULL, 'none', 0),
(20, 77, 'imagem', 'img_1750346000_68542910f2ae7.jpg', NULL, 'none', 0),
(21, 78, 'imagem', 'img_1750346021_68542925b9066.png', NULL, 'none', 0),
(22, 79, 'imagem', 'img_1750346372_68542a840eefc.png', NULL, 'none', 0),
(23, 80, 'imagem', 'img_1750346398_68542a9e7dda3.png', NULL, 'none', 0),
(24, 83, 'imagem', 'img_1750346496_68542b00e499f.png', NULL, 'none', 0),
(25, 84, 'imagem', 'img_1750347054_0_68542d2e1a6bc.png', NULL, 'none', 0);

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
(65, 78, 41, '2025-06-12 21:41:59'),
(66, 78, 40, '2025-06-12 21:42:01'),
(67, 78, 39, '2025-06-12 21:42:02'),
(68, 78, 38, '2025-06-12 21:42:03'),
(69, 78, 37, '2025-06-12 21:42:04'),
(70, 78, 36, '2025-06-12 21:42:06'),
(71, 78, 35, '2025-06-12 21:42:08'),
(72, 78, 30, '2025-06-12 21:42:14'),
(73, 78, 29, '2025-06-12 21:42:18'),
(79, 78, 26, '2025-06-12 21:45:55'),
(80, 78, 25, '2025-06-12 21:45:58'),
(81, 78, 24, '2025-06-12 21:45:59'),
(82, 78, 23, '2025-06-12 21:46:02'),
(83, 78, 21, '2025-06-12 21:46:04'),
(86, 78, 42, '2025-06-12 22:43:18'),
(88, 78, 43, '2025-06-12 22:45:36'),
(89, 78, 44, '2025-06-12 22:49:02'),
(90, 78, 45, '2025-06-13 19:05:44'),
(94, 78, 46, '2025-06-13 21:44:52'),
(95, 78, 67, '2025-06-18 17:58:49');

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
(1, 78, 'ass', NULL, '2025-03-09 15:27:38', '0000-00-00 00:00:00', 0),
(2, 78, 'zzddzd', NULL, '2025-03-09 15:27:44', '0000-00-00 00:00:00', 0),
(3, 84, 'ola', NULL, '2025-03-09 15:34:02', '0000-00-00 00:00:00', 0),
(4, 78, 'asasasasas', NULL, '2025-03-09 16:07:35', '0000-00-00 00:00:00', 0),
(5, 78, 'sou bonmito', NULL, '2025-03-09 16:20:30', '0000-00-00 00:00:00', 0),
(6, 78, 'asajsdbajd', NULL, '2025-03-09 16:37:29', '0000-00-00 00:00:00', 0),
(8, 78, 'olÃ¡ colegas, eu sou o zddeis, estou na conta do caro colega amigo colaborador companheiro afonso matos silva', NULL, '2025-03-10 09:57:26', '0000-00-00 00:00:00', 1),
(9, 81, 'olaola', NULL, '2025-03-10 23:47:05', '0000-00-00 00:00:00', 0),
(10, 81, '1213234', NULL, '2025-03-10 23:47:33', '0000-00-00 00:00:00', 0),
(11, 81, 'ola procuro emprego (job)', NULL, '2025-03-11 00:17:56', '0000-00-00 00:00:00', 0),
(12, 78, 'teste1234', NULL, '2025-03-11 00:19:10', '0000-00-00 00:00:00', 0),
(13, 78, 'preciso de tirar boa nota hoje (prometo que me vou esforÃ§ar mais)', NULL, '2025-03-11 09:06:30', '0000-00-00 00:00:00', 0),
(14, 78, 'ola 123', NULL, '2025-03-11 09:25:14', '0000-00-00 00:00:00', 0),
(15, 78, 'as', NULL, '2025-05-05 15:21:55', '0000-00-00 00:00:00', 1),
(16, 78, 'as', NULL, '2025-05-23 15:38:04', '0000-00-00 00:00:00', 1),
(17, 78, 'AS', NULL, '2025-05-23 21:51:48', '0000-00-00 00:00:00', 1),
(18, 78, 'ola 1 2 3', NULL, '2025-05-23 21:51:54', '0000-00-00 00:00:00', 1),
(19, 78, 'fasajksggahksg', NULL, '2025-05-23 21:52:43', '0000-00-00 00:00:00', 1),
(20, 78, '123', NULL, '2025-05-23 21:55:59', '0000-00-00 00:00:00', 1),
(21, 78, 'o tomas é lindo', NULL, '2025-05-25 14:16:58', '0000-00-00 00:00:00', 1),
(22, 81, 'O HUGO É UM BURRO', NULL, '2025-05-27 12:04:05', '0000-00-00 00:00:00', 1),
(23, 78, 'sexo', NULL, '2025-05-27 15:28:06', '0000-00-00 00:00:00', 1),
(24, 86, 'estou farta de estudar', NULL, '2025-06-02 18:47:13', '0000-00-00 00:00:00', 1),
(25, 86, '#vemverao', NULL, '2025-06-02 18:47:27', '0000-00-00 00:00:00', 4),
(26, 86, 'asa', NULL, '2025-06-03 12:13:18', '0000-00-00 00:00:00', 2),
(27, 78, 'aaa', NULL, '2025-06-03 14:42:47', '0000-00-00 00:00:00', 0),
(28, 87, 'procuro trabalho como stripper', NULL, '2025-06-03 15:12:17', '0000-00-00 00:00:00', 1),
(29, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', NULL, '2025-06-03 22:20:22', '0000-00-00 00:00:00', 0),
(30, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', NULL, '2025-06-03 22:37:06', '0000-00-00 00:00:00', 2),
(31, 78, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', NULL, '2025-06-03 22:45:42', '0000-00-00 00:00:00', 2),
(32, 78, 'a', NULL, '2025-06-06 22:25:11', '0000-00-00 00:00:00', 2),
(33, 89, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', NULL, '2025-06-10 21:36:03', '0000-00-00 00:00:00', 2),
(34, 78, 'http://localhost/orange/frontend/index.php#', NULL, '2025-06-11 15:24:31', '0000-00-00 00:00:00', 1),
(35, 78, 'qasssas', NULL, '2025-06-11 16:11:50', '0000-00-00 00:00:00', 1),
(36, 78, 'https://www.youtube.com/', NULL, '2025-06-11 16:20:19', '0000-00-00 00:00:00', 0),
(37, 78, 'https://www.youtube.com/', NULL, '2025-06-11 16:24:21', '0000-00-00 00:00:00', 1),
(38, 78, 'http://localhost/orange/frontend/index.php', NULL, '2025-06-11 16:25:15', '0000-00-00 00:00:00', 0),
(39, 78, 'https://www.youtube.com/', NULL, '2025-06-11 16:32:05', '0000-00-00 00:00:00', 0),
(40, 78, 'https://www.youtube.com/', NULL, '2025-06-11 16:32:37', '0000-00-00 00:00:00', 1),
(41, 78, 'vejam o meu canal: www.afonso.pt', NULL, '2025-06-11 16:36:19', '0000-00-00 00:00:00', 1),
(42, 78, 'vejam o meu canal: https://www.afonso.pt por vaor', NULL, '2025-06-11 16:37:11', '0000-00-00 00:00:00', 1),
(43, 78, '#pacman', NULL, '2025-06-11 16:41:45', '0000-00-00 00:00:00', 1),
(44, 78, 'assssas', NULL, '2025-06-11 17:04:52', '0000-00-00 00:00:00', 1),
(45, 78, 'a', NULL, '2025-06-13 18:41:46', '0000-00-00 00:00:00', 1),
(46, 78, 'a', NULL, '2025-06-13 19:05:49', '0000-00-00 00:00:00', 1),
(47, 78, 'joao', NULL, '2025-06-13 20:53:31', '0000-00-00 00:00:00', 1),
(48, 78, 'POUCAS HORAS PARA O LANÇAMENTO DA ORANGE', NULL, '2025-06-13 22:20:42', '0000-00-00 00:00:00', 1),
(49, 78, '🤩🤩🤩🤩', NULL, '2025-06-13 22:20:56', '0000-00-00 00:00:00', 0),
(50, 78, '💩', NULL, '2025-06-13 22:21:05', '0000-00-00 00:00:00', 0),
(51, 89, 'estou bastante ansioso para o lancamento oficial da orange', NULL, '2025-06-13 22:25:02', '0000-00-00 00:00:00', 1),
(52, 78, 'a', NULL, '2025-06-13 23:29:59', '0000-00-00 00:00:00', 0),
(53, 78, 'a', NULL, '2025-06-15 21:26:48', '0000-00-00 00:00:00', 0),
(54, 78, 'a', NULL, '2025-06-15 21:42:12', '0000-00-00 00:00:00', 0),
(55, 78, '', NULL, '2025-06-15 21:59:00', '0000-00-00 00:00:00', 0),
(56, 78, '', NULL, '2025-06-18 14:12:15', '0000-00-00 00:00:00', 1),
(57, 78, 'asas', NULL, '2025-06-18 15:36:42', '0000-00-00 00:00:00', 0),
(58, 78, 'a', NULL, '2025-06-18 15:36:50', '0000-00-00 00:00:00', 0),
(59, 78, 'a', NULL, '2025-06-18 15:37:07', '0000-00-00 00:00:00', 1),
(60, 78, 'a', NULL, '2025-06-18 15:37:19', '0000-00-00 00:00:00', 0),
(61, 78, 'asas', NULL, '2025-06-18 15:57:18', '0000-00-00 00:00:00', 0),
(62, 78, '', NULL, '2025-06-18 15:57:43', '0000-00-00 00:00:00', 0),
(63, 78, 'a', NULL, '2025-06-18 17:11:32', '0000-00-00 00:00:00', 0),
(64, 78, 'a', NULL, '2025-06-18 17:49:36', '0000-00-00 00:00:00', 0),
(65, 78, 'a', NULL, '2025-06-18 17:49:42', '0000-00-00 00:00:00', 0),
(66, 78, 'a', NULL, '2025-06-18 17:50:10', '0000-00-00 00:00:00', 0),
(67, 78, 'aas', NULL, '2025-06-18 17:58:46', '0000-00-00 00:00:00', 1),
(68, 78, 'a', NULL, '2025-06-18 17:58:58', '0000-00-00 00:00:00', 0),
(69, 78, 'aaa', NULL, '2025-06-18 18:01:39', '0000-00-00 00:00:00', 0),
(70, 78, 'a', NULL, '2025-06-18 18:03:41', '0000-00-00 00:00:00', 0),
(71, 78, 'a', NULL, '2025-06-18 18:05:28', '0000-00-00 00:00:00', 0),
(72, 78, 'a', NULL, '2025-06-18 18:06:30', '0000-00-00 00:00:00', 1),
(73, 78, 'a', NULL, '2025-06-18 18:25:33', '0000-00-00 00:00:00', 1),
(74, 78, 'asas', NULL, '2025-06-19 14:52:39', '0000-00-00 00:00:00', 0),
(75, 78, 'a', NULL, '2025-06-19 15:03:19', '0000-00-00 00:00:00', 0),
(76, 78, 'a', NULL, '2025-06-19 15:13:05', '0000-00-00 00:00:00', 1),
(77, 78, '', NULL, '2025-06-19 15:13:20', '0000-00-00 00:00:00', 0),
(78, 78, '', NULL, '2025-06-19 15:13:41', '0000-00-00 00:00:00', 0),
(79, 78, '', NULL, '2025-06-19 15:19:32', '0000-00-00 00:00:00', 0),
(80, 78, '', NULL, '2025-06-19 15:19:58', '0000-00-00 00:00:00', 0),
(81, 78, '', NULL, '2025-06-19 15:20:46', '0000-00-00 00:00:00', 1),
(82, 78, 'asas', NULL, '2025-06-19 15:21:32', '0000-00-00 00:00:00', 0),
(83, 78, '', NULL, '2025-06-19 15:21:36', '0000-00-00 00:00:00', 1),
(84, 78, '', NULL, '2025-06-19 15:30:54', '0000-00-00 00:00:00', 0);

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
(89, 'Gouveia', 'gouveuaaa@gmail.com', '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257', '1999-11-11', 'gougou', 0, '2025-06-10 18:45:10.639913');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `publicacao_likes`
--
ALTER TABLE `publicacao_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de tabela `publicacao_medias`
--
ALTER TABLE `publicacao_medias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `publicacao_salvas`
--
ALTER TABLE `publicacao_salvas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  MODIFY `id_publicacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de tabela `tipos_utilizador`
--
ALTER TABLE `tipos_utilizador`
  MODIFY `id_tipos_utilizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

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
