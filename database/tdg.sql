-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-04-2025 a las 03:59:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tdg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `directory_path` varchar(255) NOT NULL,
  `entry_file` varchar(100) NOT NULL DEFAULT 'index.html',
  `active` tinyint(1) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modules`
--

INSERT INTO `modules` (`id`, `name`, `slug`, `description`, `icon`, `directory_path`, `entry_file`, `active`, `order_index`, `created_at`, `updated_at`) VALUES
(4, 'Consulta RA X Materia', 'consulta-ra-x-materia', 'Módulo que trata sobre los resultados de aprendizaje por materia', 'fas fa-search', 'module-consulta-ra-x-materia', 'index.html', 1, 2, '2025-04-05 02:31:39', '2025-04-05 02:31:39'),
(6, 'Buscador de asignaturas', 'buscador-de-asignaturas', 'El módulo tratará sobre buscar asignaturas', 'fas fa-book', 'module-buscador-de-asignaturas', 'index.html', 1, 2, '2025-04-07 16:02:43', '2025-04-07 16:02:43'),
(7, 'Analisis contribucion espacios A RA', 'analisis-contribucion-espacios-a-ra', 'El módulo presenta gráficas sobre la sumatoria de contribución de materias', 'far fa-chart-bar', 'module-analisis-contribucion-espacios-a-ra', 'index.html', 1, 2, '2025-04-07 16:05:16', '2025-04-07 16:05:16'),
(8, 'Habilidades RA', 'habilidades-ra', 'El módulo trata sobre las habilidades y competencias', 'fas fa-lightbulb', 'module-habilidades-ra', 'index.html', 1, 3, '2025-04-07 16:06:30', '2025-04-07 16:06:30'),
(9, 'RA X Area', 'ra-x-area', 'El módulo sobre la descripción de resultados de aprendizaje', 'fas fa-graduation-cap', 'module-ra-x-area', 'index.html', 1, 4, '2025-04-07 16:08:24', '2025-04-07 16:08:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `module_access_logs`
--

CREATE TABLE `module_access_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `access_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `module_access_logs`
--

INSERT INTO `module_access_logs` (`id`, `user_id`, `module_id`, `ip_address`, `user_agent`, `access_timestamp`, `session_id`) VALUES
(31, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-05 02:33:03', 'vj240n9vvcouq5baph0nhrpctt'),
(33, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-05 02:34:37', 'vj240n9vvcouq5baph0nhrpctt'),
(34, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-05 02:34:51', 'vj240n9vvcouq5baph0nhrpctt'),
(35, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-05 02:36:27', 'vj240n9vvcouq5baph0nhrpctt'),
(38, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-06 03:18:31', '13u5ds4p63ecsk2mcl3sbtcadc'),
(39, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-06 03:18:37', '13u5ds4p63ecsk2mcl3sbtcadc'),
(48, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-06 18:31:02', '1jdtnsu2fd5tt0f9ij78qqd6o3'),
(54, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-06 23:36:01', 'e63633e1v7q9qai2jlg6malvi6'),
(59, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-06 23:54:00', 'e63633e1v7q9qai2jlg6malvi6'),
(60, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-06 23:57:10', 'e63633e1v7q9qai2jlg6malvi6'),
(63, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 00:13:26', 'e63633e1v7q9qai2jlg6malvi6'),
(64, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 00:15:51', 'e63633e1v7q9qai2jlg6malvi6'),
(65, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 00:18:55', 'e63633e1v7q9qai2jlg6malvi6'),
(69, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 00:19:55', 'e63633e1v7q9qai2jlg6malvi6'),
(71, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 03:08:48', '7r919itvfm6v1nkhtd0124lu71'),
(77, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 03:32:02', 'kl90e1nj9drk27mucljfghlgpr'),
(81, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 13:18:36', 'fek5b18e24kch7kto1l7gne3dn'),
(82, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 13:54:23', 'fek5b18e24kch7kto1l7gne3dn'),
(83, 1, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 14:07:07', '2uqbpbko675jtcs65dk6a8rsuo'),
(88, 1, 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:09:43', 'nhffpbndaeguq6o9qklb1d8gug'),
(89, 1, 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:09:50', 'nhffpbndaeguq6o9qklb1d8gug'),
(90, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:09:54', 'nhffpbndaeguq6o9qklb1d8gug'),
(91, 1, 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:09:57', 'nhffpbndaeguq6o9qklb1d8gug'),
(92, 1, 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:10:21', 'nhffpbndaeguq6o9qklb1d8gug'),
(93, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:10:27', 'nhffpbndaeguq6o9qklb1d8gug'),
(94, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:10:53', 'nhffpbndaeguq6o9qklb1d8gug'),
(95, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:10:56', 'nhffpbndaeguq6o9qklb1d8gug'),
(96, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:11:27', 'nhffpbndaeguq6o9qklb1d8gug'),
(97, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:12:02', 'nhffpbndaeguq6o9qklb1d8gug'),
(99, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:13:22', 'nhffpbndaeguq6o9qklb1d8gug'),
(100, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:14:02', 'nhffpbndaeguq6o9qklb1d8gug'),
(101, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:14:27', 'nhffpbndaeguq6o9qklb1d8gug'),
(102, 1, 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:14:43', 'nhffpbndaeguq6o9qklb1d8gug'),
(103, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 16:14:45', 'nhffpbndaeguq6o9qklb1d8gug'),
(111, 1, 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 23:23:13', '8dkdcrn7g4gj4eorl2764h17u3'),
(112, 1, 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 23:23:16', '8dkdcrn7g4gj4eorl2764h17u3'),
(113, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 23:23:19', '8dkdcrn7g4gj4eorl2764h17u3'),
(114, 1, 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 23:23:22', '8dkdcrn7g4gj4eorl2764h17u3'),
(115, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-07 23:23:27', '8dkdcrn7g4gj4eorl2764h17u3'),
(118, 1, 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:02:31', '8kt1obhcfckrr8mu3omitkhmtp'),
(119, 1, 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:26:42', '8kt1obhcfckrr8mu3omitkhmtp'),
(120, 1, 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:27:59', '8kt1obhcfckrr8mu3omitkhmtp'),
(121, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:28:03', '8kt1obhcfckrr8mu3omitkhmtp'),
(122, 1, 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:28:09', '8kt1obhcfckrr8mu3omitkhmtp'),
(123, 1, 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:28:13', '8kt1obhcfckrr8mu3omitkhmtp'),
(124, 1, 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:28:17', '8kt1obhcfckrr8mu3omitkhmtp'),
(125, 1, 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-08 01:29:30', '8kt1obhcfckrr8mu3omitkhmtp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'view', 'Puede ver el módulo'),
(2, 'edit', 'Puede editar datos en el módulo'),
(3, 'admin', 'Tiene acceso administrativo al módulo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrador con acceso completo'),
(2, 'user', 'Usuario estándar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_module_permissions`
--

CREATE TABLE `role_module_permissions` (
  `role_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `role_module_permissions`
--

INSERT INTO `role_module_permissions` (`role_id`, `module_id`, `permission_id`) VALUES
(1, 4, 1),
(1, 4, 2),
(1, 4, 3),
(1, 6, 1),
(1, 6, 2),
(1, 6, 3),
(1, 7, 1),
(1, 7, 2),
(1, 7, 3),
(1, 8, 1),
(1, 8, 2),
(1, 8, 3),
(1, 9, 1),
(1, 9, 2),
(1, 9, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `code`, `full_name`, `active`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$kR51mxf5g.njX/bjQjq65.XxKKFQLjLeTNzkWu/DqTuH8cE3th.Ra', 'admin@example.com', 'admincode123', 'Administrador', 1, '2025-03-27 23:54:37', '2025-04-08 00:51:26'),
(2, '20221578001', '$2y$10$OLjCNPnvRGGwiucuNvT6wubYoFPP69uGLLU0nnYA70gCf/5rWlWYi', 'xetipe7196@oronny.com', '957a684a5f603920', 'Luis López Lozada', 1, '2025-03-29 03:02:56', '2025-03-30 01:39:28'),
(4, 'twitter', '$2y$10$aHQSMrKxH3dt4jkz7vus3eHlAmTdnzlxMHWxPFaYKAYI/XrCaTssm', 'twitter@twitter.com', '0aed9ab342cf2f88', 'Franco', 0, '2025-03-30 00:06:55', NULL),
(7, 'ali', '$2y$10$tdVq4QX/q1Y0.yg0y7BrGuD4XBjZLIoy9/h0j01J/C2uVthCn3NVS', 'azeu9mry40@knmcadibav.com', '572c87233bfe939d', 'Ali baba baboso', 1, '2025-03-30 04:27:46', NULL),
(8, 'testuser1', '$2y$10$tbOLehKMFyEOVVeIQHhTm.yDQhgSzIih.26MyuV4/2Kecbq2ASIg2', 'mapiwas737@exclussi.com', 'a3ddd7a78f4a5aaa', 'User test', 1, '2025-04-03 23:11:09', '2025-04-08 01:02:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, '2025-03-27 23:54:37'),
(2, 2, '2025-03-29 03:02:56'),
(4, 2, '2025-03-30 00:06:55'),
(6, 1, '2025-03-30 02:26:47'),
(7, 2, '2025-03-30 04:27:46'),
(8, 2, '2025-04-03 23:11:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `module_access_logs`
--
ALTER TABLE `module_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `access_timestamp` (`access_timestamp`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_password_reset_token` (`token`),
  ADD KEY `idx_password_reset_expires` (`expires`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  ADD PRIMARY KEY (`role_id`,`module_id`,`permission_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `module_access_logs`
--
ALTER TABLE `module_access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `module_access_logs`
--
ALTER TABLE `module_access_logs`
  ADD CONSTRAINT `module_access_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `module_access_logs_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  ADD CONSTRAINT `role_module_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_module_permissions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_module_permissions_ibfk_3` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
