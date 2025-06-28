-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/06/2025 às 19:17
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
(105, 224, 78, 'a', '2025-06-27 21:00:26'),
(106, 224, 78, 'a', '2025-06-27 21:00:28'),
(107, 221, 78, 'a', '2025-06-27 21:00:37'),
(108, 226, 78, 'as', '2025-06-27 21:07:38'),
(109, 226, 78, 'asssassssas', '2025-06-27 21:07:40'),
(110, 227, 78, 'a', '2025-06-27 22:47:51'),
(111, 230, 89, 'assas', '2025-06-27 23:07:14'),
(112, 230, 78, 'asssas', '2025-06-27 23:15:38'),
(113, 231, 78, 'as', '2025-06-27 23:15:49'),
(114, 231, 89, 'asas', '2025-06-27 23:21:38'),
(115, 231, 89, 'asas', '2025-06-27 23:21:39'),
(116, 231, 89, 'asas', '2025-06-27 23:21:40'),
(117, 225, 78, 'ASSA', '2025-06-27 23:26:43'),
(118, 224, 78, 'AAAAAAAAAAAAAAAAAAAAAAAAAAA', '2025-06-27 23:26:47'),
(119, 235, 89, 'ola', '2025-06-27 23:30:20'),
(120, 233, 78, 'assasas', '2025-06-27 23:33:01'),
(121, 236, 78, 'asasa', '2025-06-27 23:40:40'),
(122, 235, 78, 'asassasa', '2025-06-27 23:40:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `conversas`
--

CREATE TABLE `conversas` (
  `id` int(11) NOT NULL,
  `utilizador1_id` int(11) NOT NULL,
  `utilizador2_id` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_atividade` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `conversas`
--

INSERT INTO `conversas` (`id`, `utilizador1_id`, `utilizador2_id`, `data_criacao`, `ultima_atividade`) VALUES
(1, 78, 89, '2025-06-27 10:24:46', '2025-06-27 23:18:28'),
(2, 89, 81, '2025-06-27 10:34:14', '2025-06-27 23:32:17'),
(3, 78, 86, '2025-06-27 10:54:05', '2025-06-27 23:27:16'),
(4, 86, 89, '2025-06-27 10:55:37', '2025-06-27 19:14:13'),
(5, 86, 81, '2025-06-27 11:00:47', '2025-06-27 11:01:03'),
(6, 82, 81, '2025-06-27 11:02:45', '2025-06-27 11:02:45'),
(7, 82, 78, '2025-06-27 11:05:52', '2025-06-27 18:00:12'),
(8, 79, 89, '2025-06-27 19:03:53', '2025-06-27 19:11:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `conversa_id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `conteudo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `lida` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `conversa_id`, `remetente_id`, `conteudo`, `data_envio`, `lida`) VALUES
(1, 1, 78, 'ola', '2025-06-27 10:24:56', 1),
(2, 2, 89, 'a', '2025-06-27 10:35:22', 1),
(3, 2, 89, 'assssssssssssssssss', '2025-06-27 10:42:48', 1),
(4, 2, 89, 'assssas', '2025-06-27 10:43:05', 1),
(5, 1, 89, 'ola', '2025-06-27 10:43:09', 1),
(6, 1, 78, 'gay de merda', '2025-06-27 10:43:37', 1),
(7, 1, 78, 'a', '2025-06-27 10:49:29', 1),
(8, 1, 78, 'a', '2025-06-27 10:49:34', 1),
(9, 1, 78, 'a', '2025-06-27 10:49:34', 1),
(10, 1, 78, 'a', '2025-06-27 10:49:34', 1),
(11, 1, 78, 'a', '2025-06-27 10:49:34', 1),
(12, 1, 78, 'a', '2025-06-27 10:49:35', 1),
(13, 1, 78, 'a', '2025-06-27 10:49:35', 1),
(14, 1, 78, 'aaa', '2025-06-27 10:49:35', 1),
(15, 1, 78, 'a', '2025-06-27 10:49:36', 1),
(16, 1, 78, 'a', '2025-06-27 10:49:36', 1),
(17, 5, 86, 'a', '2025-06-27 11:01:03', 1),
(18, 7, 82, 'ola', '2025-06-27 11:05:56', 1),
(19, 7, 78, 'boas mano', '2025-06-27 11:06:13', 1),
(20, 3, 78, 'gostosa rabuda', '2025-06-27 11:14:28', 1),
(21, 1, 89, 'aaaa', '2025-06-27 11:52:32', 1),
(22, 1, 89, 'aaa', '2025-06-27 11:52:33', 1),
(23, 1, 89, 'aaa', '2025-06-27 11:52:34', 1),
(24, 1, 78, 'a', '2025-06-27 12:13:38', 1),
(25, 1, 78, 'a', '2025-06-27 12:13:39', 1),
(26, 1, 78, 'a', '2025-06-27 12:13:39', 1),
(27, 1, 78, 'a', '2025-06-27 12:13:39', 1),
(28, 1, 78, 'a', '2025-06-27 12:13:39', 1),
(29, 1, 78, 'a', '2025-06-27 12:13:39', 1),
(30, 1, 78, 'a', '2025-06-27 12:13:40', 1),
(31, 1, 78, 'a', '2025-06-27 12:13:40', 1),
(32, 1, 78, 'a', '2025-06-27 12:13:40', 1),
(33, 1, 78, 'a', '2025-06-27 12:13:40', 1),
(34, 1, 78, 'a', '2025-06-27 12:13:40', 1),
(35, 1, 78, 'a', '2025-06-27 12:13:40', 1),
(36, 1, 78, 'a', '2025-06-27 12:13:41', 1),
(37, 1, 78, 'a', '2025-06-27 12:13:41', 1),
(38, 1, 78, 'a', '2025-06-27 12:13:41', 1),
(39, 1, 78, 'a', '2025-06-27 12:13:41', 1),
(40, 1, 78, 'a', '2025-06-27 12:13:41', 1),
(41, 1, 78, 'a', '2025-06-27 12:13:41', 1),
(42, 1, 78, 'a', '2025-06-27 12:13:42', 1),
(43, 1, 78, 'a', '2025-06-27 12:13:42', 1),
(44, 1, 78, 'a', '2025-06-27 12:13:42', 1),
(45, 1, 78, 'a', '2025-06-27 12:13:42', 1),
(46, 1, 78, 'a', '2025-06-27 12:13:42', 1),
(47, 1, 78, 'a', '2025-06-27 12:13:42', 1),
(48, 1, 78, 'a', '2025-06-27 12:13:43', 1),
(49, 1, 78, 'a', '2025-06-27 12:13:43', 1),
(50, 1, 78, 'a', '2025-06-27 12:13:43', 1),
(51, 1, 78, 'a', '2025-06-27 12:13:43', 1),
(52, 1, 78, 'a', '2025-06-27 12:13:43', 1),
(53, 1, 78, 'a', '2025-06-27 12:13:43', 1),
(54, 1, 78, 'a', '2025-06-27 12:13:44', 1),
(55, 1, 78, 'a', '2025-06-27 12:13:44', 1),
(56, 1, 78, 'a', '2025-06-27 12:13:44', 1),
(57, 1, 78, 'a', '2025-06-27 12:13:44', 1),
(58, 1, 78, 'a', '2025-06-27 12:13:44', 1),
(59, 1, 78, 'a', '2025-06-27 12:13:44', 1),
(60, 1, 78, 'a', '2025-06-27 12:13:45', 1),
(61, 1, 89, 'as', '2025-06-27 17:59:34', 1),
(62, 1, 89, 'a', '2025-06-27 17:59:37', 1),
(63, 1, 89, 'a', '2025-06-27 17:59:39', 1),
(64, 1, 89, 'a', '2025-06-27 17:59:39', 1),
(65, 1, 89, 'a', '2025-06-27 17:59:39', 1),
(66, 1, 89, 'a', '2025-06-27 17:59:39', 1),
(67, 1, 89, 'a', '2025-06-27 17:59:40', 1),
(68, 1, 89, 'a', '2025-06-27 17:59:40', 1),
(69, 1, 89, 'a', '2025-06-27 17:59:40', 1),
(70, 1, 89, 'a', '2025-06-27 17:59:40', 1),
(71, 2, 89, 'ahhh', '2025-06-27 17:59:43', 1),
(72, 2, 89, 'pap', '2025-06-27 17:59:44', 1),
(73, 1, 89, 'pov', '2025-06-27 17:59:47', 1),
(74, 7, 82, 'boas', '2025-06-27 18:00:12', 1),
(75, 3, 78, 'a', '2025-06-27 18:55:46', 1),
(76, 3, 78, 'asass', '2025-06-27 19:02:31', 1),
(77, 1, 78, 'a', '2025-06-27 19:03:31', 1),
(78, 1, 78, 'a', '2025-06-27 19:03:43', 1),
(79, 8, 79, 'a', '2025-06-27 19:03:55', 1),
(80, 8, 79, 'a', '2025-06-27 19:03:56', 1),
(81, 4, 89, 'a', '2025-06-27 19:10:43', 1),
(82, 4, 89, 'sua gostosa rabuda', '2025-06-27 19:10:53', 1),
(83, 8, 89, 'a', '2025-06-27 19:10:57', 0),
(84, 8, 89, 'a', '2025-06-27 19:11:00', 0),
(85, 8, 89, 'a', '2025-06-27 19:11:01', 0),
(86, 8, 89, 'a', '2025-06-27 19:11:01', 0),
(87, 8, 89, 'a', '2025-06-27 19:11:01', 0),
(88, 8, 89, 'a', '2025-06-27 19:11:01', 0),
(89, 8, 89, 'a', '2025-06-27 19:11:01', 0),
(90, 8, 89, 'a', '2025-06-27 19:11:02', 0),
(91, 8, 89, 'a', '2025-06-27 19:11:02', 0),
(92, 8, 89, 'a', '2025-06-27 19:11:02', 0),
(93, 4, 86, 'seu porco', '2025-06-27 19:12:36', 1),
(94, 4, 86, 'a', '2025-06-27 19:12:41', 1),
(95, 4, 86, 'a', '2025-06-27 19:12:45', 1),
(96, 4, 86, 'a', '2025-06-27 19:12:48', 1),
(97, 4, 86, 'a', '2025-06-27 19:12:51', 1),
(98, 4, 86, 'a', '2025-06-27 19:12:52', 1),
(99, 4, 86, 'gostoso', '2025-06-27 19:13:07', 1),
(100, 4, 89, 'a', '2025-06-27 19:13:17', 1),
(101, 4, 89, 'a', '2025-06-27 19:13:30', 1),
(102, 4, 89, 'a', '2025-06-27 19:13:33', 1),
(103, 4, 86, 'gostoso', '2025-06-27 19:13:57', 1),
(104, 4, 86, 'asas', '2025-06-27 19:14:13', 1),
(105, 3, 78, 'a', '2025-06-27 19:28:49', 1),
(106, 3, 78, 'a', '2025-06-27 19:28:50', 1),
(107, 3, 78, 'a', '2025-06-27 19:28:50', 1),
(108, 3, 78, 'a', '2025-06-27 19:28:50', 1),
(109, 3, 78, 'a', '2025-06-27 19:28:51', 1),
(110, 3, 78, 'a', '2025-06-27 19:28:51', 1),
(111, 3, 78, 'a', '2025-06-27 19:28:51', 1),
(112, 1, 78, 'a', '2025-06-27 23:18:27', 1),
(113, 1, 78, 'a', '2025-06-27 23:18:28', 1),
(114, 1, 78, 'a', '2025-06-27 23:18:28', 1),
(115, 1, 78, 'a', '2025-06-27 23:18:28', 1),
(116, 3, 78, 'A', '2025-06-27 23:27:16', 0),
(117, 2, 89, 'assasa', '2025-06-27 23:31:29', 1),
(118, 2, 81, 'ola', '2025-06-27 23:32:10', 1),
(119, 2, 81, 'ola', '2025-06-27 23:32:17', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL COMMENT 'Quem recebe a notificação',
  `remetente_id` int(11) NOT NULL COMMENT 'Quem causou a notificação',
  `tipo` enum('like','comment','follow','save','unfollow') NOT NULL,
  `publicacao_id` int(11) DEFAULT NULL COMMENT 'ID da publicação (para likes, comments, saves)',
  `comentario_id` int(11) DEFAULT NULL COMMENT 'ID do comentário (para comentários)',
  `mensagem` text NOT NULL COMMENT 'Texto da notificação',
  `lida` tinyint(1) NOT NULL DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `utilizador_id`, `remetente_id`, `tipo`, `publicacao_id`, `comentario_id`, `mensagem`, `lida`, `data_criacao`) VALUES
(1, 86, 78, 'comment', 224, 105, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 21:00:26'),
(2, 86, 78, 'follow', NULL, NULL, 'Afonso Silva começou a seguir-te', 1, '2025-06-27 21:01:18'),
(3, 86, 78, 'like', 227, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 21:03:40'),
(6, 78, 86, 'like', 221, NULL, 'Matilde Alves deu like na sua publicação', 1, '2025-06-27 21:07:17'),
(7, 78, 86, 'like', 220, NULL, 'Matilde Alves deu like na sua publicação', 1, '2025-06-27 21:07:19'),
(8, 86, 78, 'comment', 226, 108, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 21:07:38'),
(9, 86, 78, 'comment', 227, 110, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 22:47:51'),
(11, 78, 89, 'like', 231, NULL, 'Gouveia deu like na sua publicação', 1, '2025-06-27 23:07:11'),
(12, 78, 89, 'save', 231, NULL, 'Gouveia guardou a sua publicação', 1, '2025-06-27 23:07:11'),
(13, 78, 89, 'like', 230, NULL, 'Gouveia deu like na sua publicação', 1, '2025-06-27 23:07:13'),
(14, 78, 89, 'comment', 230, 111, 'Gouveia comentou na sua publicação', 1, '2025-06-27 23:07:14'),
(15, 78, 89, 'like', 229, NULL, 'Gouveia deu like na sua publicação', 1, '2025-06-27 23:07:16'),
(16, 78, 89, 'like', 228, NULL, 'Gouveia deu like na sua publicação', 1, '2025-06-27 23:07:17'),
(17, 78, 89, 'save', 229, NULL, 'Gouveia guardou a sua publicação', 1, '2025-06-27 23:07:18'),
(18, 86, 89, 'like', 227, NULL, 'Gouveia deu like na sua publicação', 1, '2025-06-27 23:07:19'),
(19, 78, 89, 'follow', NULL, NULL, 'Gouveia começou a seguir-te', 1, '2025-06-27 23:07:23'),
(20, 78, 89, 'comment', 231, 114, 'Gouveia comentou na sua publicação', 1, '2025-06-27 23:21:38'),
(21, 86, 78, 'like', 224, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:26:39'),
(22, 86, 78, 'like', 225, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:26:40'),
(23, 86, 78, 'like', 226, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:26:41'),
(24, 86, 78, 'comment', 225, 117, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 23:26:43'),
(25, 89, 78, 'like', 236, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:32:54'),
(26, 89, 78, 'like', 235, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:32:56'),
(29, 89, 78, 'comment', 233, 120, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 23:33:01'),
(30, 89, 78, 'follow', NULL, NULL, 'Afonso Silva começou a seguir-te', 1, '2025-06-27 23:33:07'),
(31, 89, 78, 'comment', 236, 121, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 23:40:40'),
(32, 89, 78, 'comment', 235, 122, 'Afonso Silva comentou na sua publicação', 1, '2025-06-27 23:40:44'),
(33, 89, 78, 'like', 234, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:40:46'),
(34, 89, 78, 'like', 233, NULL, 'Afonso Silva deu like na sua publicação', 1, '2025-06-27 23:40:47'),
(35, 78, 89, 'like', 221, NULL, 'Gouveia deu like na sua publicação', 1, '2025-06-27 23:51:36');

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
-- Estrutura para tabela `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `publicacao_id` int(11) NOT NULL,
  `pergunta` varchar(500) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `total_votos` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `poll_opcoes`
--

CREATE TABLE `poll_opcoes` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `opcao_texto` varchar(200) NOT NULL,
  `votos` int(11) DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `poll_votos`
--

CREATE TABLE `poll_votos` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `opcao_id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `data_voto` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(191, 223, 89, '2025-06-27 11:27:51'),
(193, 223, 86, '2025-06-27 20:49:23'),
(194, 222, 86, '2025-06-27 20:49:24'),
(195, 221, 78, '2025-06-27 21:00:35'),
(196, 223, 78, '2025-06-27 21:00:44'),
(197, 227, 78, '2025-06-27 21:03:40'),
(200, 221, 86, '2025-06-27 21:07:17'),
(201, 220, 86, '2025-06-27 21:07:19'),
(202, 231, 89, '2025-06-27 23:07:11'),
(203, 230, 89, '2025-06-27 23:07:13'),
(204, 229, 89, '2025-06-27 23:07:16'),
(205, 228, 89, '2025-06-27 23:07:17'),
(206, 227, 89, '2025-06-27 23:07:19'),
(207, 224, 78, '2025-06-27 23:26:39'),
(208, 225, 78, '2025-06-27 23:26:40'),
(209, 226, 78, '2025-06-27 23:26:41'),
(210, 236, 78, '2025-06-27 23:32:54'),
(211, 235, 78, '2025-06-27 23:32:56'),
(214, 234, 78, '2025-06-27 23:40:46'),
(215, 233, 78, '2025-06-27 23:40:47'),
(216, 236, 89, '2025-06-27 23:41:39'),
(217, 221, 89, '2025-06-27 23:51:36');

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
(139, 234, 'pub_1751066993_0_685f2971d7e11.jpg', 'none', 0, 'imagem'),
(140, 235, 'pub_1751067008_0_685f29807abd6.jpg', 'none', 0, 'imagem'),
(141, 235, 'pub_1751067008_1_685f29807c012.jpg', 'none', 1, 'imagem'),
(142, 235, 'pub_1751067008_2_685f29807dab7.png', 'none', 2, 'imagem'),
(143, 235, 'pub_1751067008_3_685f29807e24b.png', 'none', 3, 'imagem'),
(144, 236, 'pub_1751067056_0_685f29b0b5df2.mp4', 'none', 0, 'video');

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
(133, 89, 231, '2025-06-27 23:07:11'),
(134, 89, 229, '2025-06-27 23:07:18'),
(135, 78, 231, '2025-06-27 23:15:42'),
(136, 89, 235, '2025-06-27 23:30:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacoes`
--

CREATE TABLE `publicacoes` (
  `id_publicacao` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `conteudo` text NOT NULL,
  `tipo` enum('post','poll') DEFAULT 'post',
  `categoria` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `deletado_em` datetime NOT NULL,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `publicacoes`
--

INSERT INTO `publicacoes` (`id_publicacao`, `id_utilizador`, `conteudo`, `tipo`, `categoria`, `data_criacao`, `deletado_em`, `likes`) VALUES
(220, 78, 'asas', 'post', NULL, '2025-06-26 15:47:24', '0000-00-00 00:00:00', 1),
(221, 78, 'asas', 'post', NULL, '2025-06-26 15:47:25', '0000-00-00 00:00:00', 3),
(222, 78, 'asas', 'post', NULL, '2025-06-26 15:47:26', '0000-00-00 00:00:00', 1),
(223, 78, 'as', 'post', NULL, '2025-06-27 10:24:39', '0000-00-00 00:00:00', 3),
(224, 86, 'ãsas', 'post', NULL, '2025-06-27 20:06:19', '0000-00-00 00:00:00', 1),
(225, 86, 'a', 'post', NULL, '2025-06-27 21:03:30', '0000-00-00 00:00:00', 1),
(226, 86, 'a', 'post', NULL, '2025-06-27 21:03:31', '0000-00-00 00:00:00', 1),
(227, 86, 'assasas', 'post', NULL, '2025-06-27 21:03:32', '0000-00-00 00:00:00', 2),
(228, 78, 'sasaas', 'post', NULL, '2025-06-27 23:06:39', '0000-00-00 00:00:00', 1),
(229, 78, 'sadddddasd', 'post', NULL, '2025-06-27 23:06:40', '0000-00-00 00:00:00', 1),
(230, 78, 'asddasdasd', 'post', NULL, '2025-06-27 23:06:42', '0000-00-00 00:00:00', 1),
(231, 78, 'assdddassdasssd', 'post', NULL, '2025-06-27 23:06:44', '0000-00-00 00:00:00', 1),
(232, 89, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'post', NULL, '2025-06-27 23:29:45', '0000-00-00 00:00:00', 0),
(233, 89, 'Ola', 'post', NULL, '2025-06-27 23:29:48', '0000-00-00 00:00:00', 1),
(234, 89, 'ola', 'post', NULL, '2025-06-27 23:29:53', '0000-00-00 00:00:00', 1),
(235, 89, 'ola', 'post', NULL, '2025-06-27 23:30:08', '0000-00-00 00:00:00', 1),
(236, 89, '', 'post', NULL, '2025-06-27 23:30:56', '0000-00-00 00:00:00', 2);

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
(78, 86, '2025-06-27 21:01:18'),
(78, 89, '2025-06-27 23:33:07'),
(79, 78, '2025-06-25 14:08:17'),
(79, 80, '2025-06-25 14:07:58'),
(79, 81, '2025-06-25 14:07:50'),
(79, 82, '2025-06-25 17:28:22'),
(81, 78, '2025-05-27 12:02:52'),
(81, 82, '2025-06-27 11:01:40'),
(82, 81, '2025-06-27 11:02:23'),
(86, 78, '2025-06-27 20:09:09'),
(86, 89, '2025-06-27 10:55:21'),
(87, 78, '2025-06-03 15:12:47'),
(87, 86, '2025-06-03 15:12:41'),
(89, 78, '2025-06-27 23:07:23'),
(89, 86, '2025-06-27 10:55:06');

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
-- Índices de tabela `conversas`
--
ALTER TABLE `conversas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`utilizador1_id`,`utilizador2_id`),
  ADD KEY `utilizador2_id` (`utilizador2_id`),
  ADD KEY `idx_ultima_atividade` (`ultima_atividade`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversa_id` (`conversa_id`),
  ADD KEY `remetente_id` (`remetente_id`),
  ADD KEY `idx_data_envio` (`data_envio`),
  ADD KEY `idx_lida` (`lida`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `remetente_id` (`remetente_id`),
  ADD KEY `publicacao_id` (`publicacao_id`),
  ADD KEY `comentario_id` (`comentario_id`),
  ADD KEY `idx_data_criacao` (`data_criacao`),
  ADD KEY `idx_lida` (`lida`),
  ADD KEY `idx_utilizador_lida` (`utilizador_id`,`lida`),
  ADD KEY `idx_tipo_data` (`tipo`,`data_criacao`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id_perfil`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices de tabela `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publicacao_id` (`publicacao_id`);

--
-- Índices de tabela `poll_opcoes`
--
ALTER TABLE `poll_opcoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Índices de tabela `poll_votos`
--
ALTER TABLE `poll_votos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_poll` (`poll_id`,`utilizador_id`),
  ADD KEY `opcao_id` (`opcao_id`),
  ADD KEY `utilizador_id` (`utilizador_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT de tabela `conversas`
--
ALTER TABLE `conversas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `poll_opcoes`
--
ALTER TABLE `poll_opcoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `poll_votos`
--
ALTER TABLE `poll_votos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `publicacao_likes`
--
ALTER TABLE `publicacao_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT de tabela `publicacao_medias`
--
ALTER TABLE `publicacao_medias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT de tabela `publicacao_salvas`
--
ALTER TABLE `publicacao_salvas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  MODIFY `id_publicacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

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
-- Restrições para tabelas `conversas`
--
ALTER TABLE `conversas`
  ADD CONSTRAINT `conversas_ibfk_1` FOREIGN KEY (`utilizador1_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversas_ibfk_2` FOREIGN KEY (`utilizador2_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_3` FOREIGN KEY (`publicacao_id`) REFERENCES `publicacoes` (`id_publicacao`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_4` FOREIGN KEY (`comentario_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfis`
--
ALTER TABLE `perfis`
  ADD CONSTRAINT `perfis_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`);

--
-- Restrições para tabelas `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`publicacao_id`) REFERENCES `publicacoes` (`id_publicacao`) ON DELETE CASCADE;

--
-- Restrições para tabelas `poll_opcoes`
--
ALTER TABLE `poll_opcoes`
  ADD CONSTRAINT `poll_opcoes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `poll_votos`
--
ALTER TABLE `poll_votos`
  ADD CONSTRAINT `poll_votos_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votos_ibfk_2` FOREIGN KEY (`opcao_id`) REFERENCES `poll_opcoes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votos_ibfk_3` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

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
