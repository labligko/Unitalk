-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 03, 2025 at 12:39 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `forum_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id_account` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `foto_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id_account`, `username`, `email`, `password`, `foto_profil`, `bio`) VALUES
(4, 'admin', 'labligko@gmail.com', '$2y$10$uU5yMay0eCPlAJSd4Wt8huRapLf5bf2FTtYhBRKdT4k/8XFrbx.Oe', 'assets/media/profile/67fc90cbae15a_WhatsApp Image 2025-02-02 at 06.57.24_5e3614c6.jpg', 'hallloooooooo\r\n'),
(6, 'Sachrbx_1257', 'nilagi3992@ikangou.com', '$2y$10$Jb5MRetICEYpaftVelbFFu8El8LJ3EjS1NaBIIlF3sY.pcRW/TH2.', 'assets/media/profile/67fc8ff69c57e_WhatsApp Image 2025-02-02 at 06.57.23_65994e91.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc efficitur lacus lorem, ut viverra neque porta id. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.'),
(7, 'labligko', 'lab@gmail.com', '$2y$10$4ZiSlgQMheijMgOecPTePu5yRE7p1XOQCnFe.WFfKxyrtjH7NoLii', 'assets/media/profile/6809ee57303f5_Dr_Stone _[Senku].jpeg', 'im new here'),
(8, 'aku_hebat123', 'alfthoriq0@gmail.com', '$2y$10$.sisIwsgBCIqz37W28ABoev3LJh6pwXK.MkSC3ImoFZ5PU72BNe02', 'assets/media/profile/680843853220a_pp_hebat.jpeg', 'aku hebat'),
(9, 'Evos_Gallang', 'galang_evos@gmail.com', '$2y$10$CpRn8alFp9JNVJZhvXoDSucHYZtRmbNCnCZnZ4FIi8r1yyjYU7lEi', 'assets/media/profile/6809eefeec2c6_download.jpeg', 'kami punya emwan🔥'),
(10, 'BabyVanilla', 'BabyVanilla@gmail.com', '$2y$10$JYueOCbplwaoXURFKNpW6O3iuJWxNAYrljALbq/u4Qeew0jnXnYOS', 'assets/media/profile/6809f0120f518_babyvanila3.jpeg', ''),
(11, 'DarkKnight', 'DarkKnight@gmail.com', '$2y$10$4Cl.lj8rGKKzQr2qHrHXsuW2S7GsV001XBd7mbtBfgyhHuYwZFjc.', 'assets/media/profile/6809f05f070a2_darkpp.jpeg', 'lonely 👤'),
(12, 'Feather_of_Heaven', 'Feather@gmail.com', '$2y$10$WNHEqXdQgey.ElqeiXJjm.mqIOMt1HxOvWVPPmfrTjU2Qhet/7vNe', 'assets/media/profile/6809f11371f38_feather.jpeg', ''),
(13, 'KonoDioDa', 'KonoDioDa@gmail.com', '$2y$10$0TrzdqonQNcg3JEsi1e4QeSyhHSHd1o9NXc.y7e/.7Mlyl.yhxvdm', 'assets/media/profile/6809e45a8e0fc_konodioda1.jpg', 'pizza mozarella🍕'),
(14, 'Lolipop', 'Lolipop@gmail.com', '$2y$10$eOEudU0MU6AcHr5JPsf4WuUHSBAVc1oIgb3geLu7lGRsYPCq04NJK', 'assets/media/profile/6809ec28590d3_blue circle anime sticker.jpeg', 'cutie'),
(15, 'NanaIsHere', 'NanaIsHere@gmail.com', '$2y$10$0Q05.sIucrnI.ALrJrlpRufzgUUja1931aAl1UwqJ1X9fCF58VRPG', 'assets/media/profile/6809ed9f5568d_Nana.jpeg', 'Tadaaa....'),
(16, 'RainbowRubyy', 'RainbowRubyy@gmail.com', '$2y$10$/t5qZdVyuPtaRs9FnMGQLeQKnXk0L2rI.xWYlO4K9HSYdsKrT.Cpu', 'assets/media/profile/6809f16f27410_ruby.jpeg', 'hewoo👋');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id_comment` int NOT NULL,
  `id_account` int NOT NULL,
  `id_thread` int NOT NULL,
  `komentar` text NOT NULL,
  `status` enum('read','unread') NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `parent_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id_comment`, `id_account`, `id_thread`, `komentar`, `status`, `waktu`, `parent_id`) VALUES
(31, 4, 26, 'tes', 'read', '2025-01-19 07:26:27', NULL),
(32, 4, 25, 'tes', 'read', '2025-01-19 07:26:41', NULL),
(34, 4, 27, 'test', 'read', '2025-01-30 04:20:54', NULL),
(38, 4, 27, 'jawa', 'read', '2025-04-10 02:06:57', NULL),
(39, 7, 27, 'hola', 'read', '2025-04-12 14:22:35', NULL),
(48, 6, 27, 'heeweorok', 'read', '2025-04-15 13:11:02', NULL),
(85, 6, 27, '@Sachrbx_1257 masih bisa kah?', 'read', '2025-04-16 01:46:46', 48),
(86, 6, 27, 'mwehehehe', 'read', '2025-04-16 02:05:53', NULL),
(109, 6, 9, 'suka suka', 'read', '2025-04-19 15:27:13', NULL),
(110, 6, 27, '@labligko halo halooo', 'read', '2025-04-22 13:41:19', 39),
(115, 15, 84, 'hello', 'read', '2025-04-24 11:40:59', NULL),
(116, 6, 85, 'cool', 'read', '2025-04-25 01:34:13', NULL),
(117, 8, 85, '@Sachrbx_1257 halo', 'read', '2025-04-25 01:54:50', 116);

-- --------------------------------------------------------

--
-- Table structure for table `friendlist`
--

CREATE TABLE `friendlist` (
  `id_friendlist` int NOT NULL,
  `user_id` int NOT NULL,
  `friend_id` int NOT NULL,
  `status` enum('following','blocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'following',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `friendlist`
--

INSERT INTO `friendlist` (`id_friendlist`, `user_id`, `friend_id`, `status`, `created_at`) VALUES
(96, 8, 6, 'following', '2025-04-12 13:56:41'),
(98, 6, 7, 'following', '2025-04-12 18:03:14'),
(111, 4, 6, 'following', '2025-04-21 02:03:54'),
(112, 8, 4, 'following', '2025-04-21 02:09:39'),
(119, 6, 8, 'following', '2025-04-23 01:48:33'),
(120, 8, 7, 'following', '2025-04-25 01:27:51'),
(121, 8, 12, 'following', '2025-04-25 01:28:00'),
(122, 8, 11, 'following', '2025-04-25 01:28:09'),
(123, 15, 8, 'following', '2025-04-25 01:29:18'),
(124, 12, 8, 'following', '2025-04-25 01:29:58'),
(125, 14, 8, 'following', '2025-04-25 01:30:36'),
(126, 11, 8, 'following', '2025-04-25 01:30:55'),
(127, 4, 8, 'following', '2025-04-25 01:31:16'),
(128, 8, 15, 'following', '2025-04-25 01:55:03');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id_like` int NOT NULL,
  `id_thread` int NOT NULL,
  `id_account` int NOT NULL,
  `status` enum('read','unread') NOT NULL,
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id_like`, `id_thread`, `id_account`, `status`, `waktu`) VALUES
(106, 18, 6, 'read', '2025-01-19 07:24:17'),
(107, 9, 6, 'read', '2025-01-19 07:24:46'),
(108, 25, 4, 'read', '2025-01-19 07:27:35'),
(109, 3, 6, 'read', '2025-01-19 07:32:45'),
(114, 26, 4, 'read', '2025-01-19 07:56:56'),
(115, 9, 4, 'read', '2025-01-19 08:02:00'),
(116, 5, 4, 'read', '2025-01-19 08:02:01'),
(119, 15, 6, 'read', '2025-01-22 04:08:51'),
(122, 25, 6, 'read', '2025-01-29 12:07:34'),
(124, 27, 4, 'read', '2025-01-30 04:25:23'),
(126, 14, 4, 'read', '2025-04-10 06:43:15'),
(185, 27, 6, 'read', '2025-04-23 01:48:41'),
(187, 32, 8, 'read', '2025-04-23 02:26:39'),
(191, 84, 15, 'read', '2025-04-24 11:40:33'),
(192, 85, 8, 'read', '2025-04-25 01:25:12'),
(193, 83, 8, 'unread', '2025-04-25 01:25:17'),
(194, 82, 8, 'read', '2025-04-25 01:25:19'),
(195, 81, 8, 'read', '2025-04-25 01:25:21'),
(196, 78, 8, 'unread', '2025-04-25 01:25:25'),
(197, 74, 8, 'unread', '2025-04-25 01:25:28'),
(198, 70, 8, 'read', '2025-04-25 01:25:31'),
(199, 68, 8, 'read', '2025-04-25 01:25:32'),
(200, 3, 8, 'unread', '2025-04-25 01:27:33');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id_message` int NOT NULL,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `message` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id_message`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 6, 4, 'hello', '2025-04-17 09:26:10'),
(2, 4, 6, 'hi', '2025-04-17 09:26:35'),
(3, 6, 4, 'anoo', '2025-04-19 22:07:51'),
(4, 6, 4, 'ayo kita bermain', '2025-04-19 22:08:00'),
(5, 6, 4, 'test', '2025-04-19 22:12:33'),
(6, 6, 4, 'test lagi', '2025-04-19 22:12:43'),
(7, 6, 4, 'text', '2025-04-19 22:13:48'),
(8, 6, 4, 'cek', '2025-04-19 22:17:42'),
(9, 6, 4, 'cek', '2025-04-19 22:17:50'),
(10, 6, 4, 'cek', '2025-04-19 22:18:00'),
(11, 6, 4, 'cek', '2025-04-19 22:18:07'),
(12, 4, 6, 'hello', '2025-04-21 09:02:22'),
(13, 6, 8, 'halo salam kenal...', '2025-04-22 20:55:40'),
(14, 8, 6, 'haaii...', '2025-04-22 20:56:07'),
(15, 8, 6, 'salam kenal jugaa', '2025-04-22 20:56:15'),
(16, 6, 7, 'haaii...', '2025-04-23 08:30:52'),
(17, 8, 6, 'hai juga', '2025-04-23 09:25:05'),
(18, 6, 7, 'tas,pad,', '2025-04-25 08:33:35'),
(19, 8, 6, 'halo selamat pagi', '2025-04-25 08:53:54');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`) VALUES
(20, 'labligko@gmail.com', 'ca12591d01f570d31344e5a88d109870c98be06c7a909aadeb01abf7fe1ca92bfe7bb640b36e9f12396bb5d4538b1324b9ed', '2025-01-14 19:18:15'),
(22, 'labligko@gmail.com', '44186dfad5211340a354f482ea4cea157b4bfe083eae11b0bd00095be9fc997b7ac7d40e348ebd6fbac863f565065790eba3', '2025-01-18 08:46:20');

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `id` int NOT NULL,
  `media` text NOT NULL,
  `captions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_account` int DEFAULT NULL,
  `jenis` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`id`, `media`, `captions`, `tanggal`, `id_account`, `jenis`) VALUES
(3, '974403724_python.png', 'uler', '2024-10-08 12:32:19', 4, 'gambar'),
(4, '831712758_PuTI.png', 'Direktorat Pusat Teknologi Informasi', '2024-10-08 12:33:52', 4, 'gambar'),
(5, '743973233_WhatsApp Image 2024-10-15 at 12.38.39_e6fa1251.jpg', 'ahahaha', '2024-10-19 16:09:03', 4, 'gambar'),
(9, '1974681314_WhatsApp Image 2024-11-21 at 11.12.55_630595a4.jpg', 'testing\r\n', '2024-11-24 11:27:18', 4, 'gambar'),
(11, '148173606_Desain tanpa judul.png', 'D3 Rekayasa Perangkat Lunak Aplikasi', '2024-11-24 14:01:15', 6, 'gambar'),
(14, '133067970_default_profile.jpg', 'profil pic', '2024-11-24 14:33:43', 6, 'gambar'),
(15, '586920167_Riz Parah🤫🤫😎😎🧊🧊.mp4', 'testing video', '2024-11-24 14:34:06', 6, 'video'),
(18, '', 'test test test\r\n', '2024-12-03 06:29:38', 6, 'text'),
(25, '1579868769_download---.jpeg', 'tels', '2025-01-18 00:20:05', 6, 'gambar'),
(26, '227995519_foto.png', 'test foto', '2025-01-18 04:28:04', 4, 'gambar'),
(27, '37678149_converted_5.7K Canon EOS 5D Mark III RAW Video - Full Width Anamorphic Mode - Oberdorla.mp4', 'what', '2025-01-18 16:15:36', 6, 'video'),
(32, '557721687_download_gambar4.jpeg', '                    test', '2025-04-23 02:26:33', 8, 'gambar'),
(33, '1825351875_labligko1.jpeg', 'science is nice', '2025-04-24 06:38:21', 7, 'gambar'),
(34, '323836552_evos_gallang2.jpg', 'hehehe,... skin baru gaesss..😍', '2025-04-24 06:40:55', 9, 'gambar'),
(35, '610621609_konodioda3.jpeg', 'Hari hari yang membosankan😪', '2025-04-24 06:45:23', 13, 'gambar'),
(38, '1122545848_babyvanila1.jpeg', '❤️❤️', '2025-04-24 06:50:31', 10, 'gambar'),
(39, '', 'Di setiap helai bulu, ada kisah yang tak tersampaikan...', '2025-04-24 06:51:44', 12, 'text'),
(41, '106516920_lolipop1❤.jpeg', '💙💙', '2025-04-24 06:55:19', 14, 'gambar'),
(42, '', 'Pelangi datang saat kamu berani bermimpi!❤️🌈', '2025-04-24 06:56:07', 16, 'text'),
(43, '', 'Nana datang~~~😻', '2025-04-24 06:57:31', 15, 'text'),
(44, '1260048366_labligko2.jpeg', '👩🏻‍🔬⚗️🧪🥼', '2025-04-24 06:59:25', 7, 'gambar'),
(46, '1710118075_darkknight2.jpeg', '⚔️⚜', '2025-04-24 07:04:55', 11, 'gambar'),
(47, '', 'I walk alone in the shadow of silence. 🗡', '2025-04-24 07:05:49', 11, 'text'),
(48, '156442131_evos_gallang1.jpg', 'info sparing der..🥶\r\n', '2025-04-24 07:07:31', 9, 'gambar'),
(49, '1804123025_evos_gallang3.jpg', 'siap menjadi proplayer. 😎', '2025-04-24 07:08:38', 9, 'gambar'),
(51, '472910618_konodioda2.jpeg', 'ooiii.... temmmeeee...!😤😤', '2025-04-24 07:10:31', 13, 'gambar'),
(52, '', 'You thought this was a motivational quote... but it was ME, DIO! 😏', '2025-04-24 07:11:37', 13, 'text'),
(53, '973527973_konodioda1.jpg', '😐', '2025-04-24 07:12:13', 13, 'gambar'),
(54, '', 'Sains bukan soal pintar, tapi soal penasaran.✨💡🌟', '2025-04-24 07:15:10', 7, 'text'),
(55, '1365924776_labligko3.jpeg', '', '2025-04-24 07:15:49', 7, 'gambar'),
(57, '385410055_heaven3.jpeg', '                    ⋆˖⁺‧₊☽◯☾₊‧⁺˖⋆', '2025-04-24 07:21:31', 12, 'gambar'),
(58, '343558471_heaven1.jpeg', 'Kingdom ✨🏰', '2025-04-24 07:22:58', 12, 'gambar'),
(59, '', 'Soft days, warm drinks, and gentle moments.🥰🥰', '2025-04-24 07:24:01', 10, 'text'),
(60, '1593775436_babyvanila2.jpeg', '✩₊˚.⋆☾⋆⁺₊✧', '2025-04-24 07:25:51', 10, 'gambar'),
(63, '701874794_nana2.jpeg', '                    ', '2025-04-24 07:30:05', 15, 'gambar'),
(64, '293735815_rainbow1.jpeg', '🌈🌈', '2025-04-24 07:31:46', 16, 'gambar'),
(65, '1831169393_rainbow2.jpeg', '🐰', '2025-04-24 07:32:21', 16, 'gambar'),
(66, '1051694009_rainbow3.jpeg', '🦄🦄', '2025-04-24 07:32:43', 16, 'gambar'),
(67, '', 'Hidup itu kayak permen, manis ketika dinikmati. 🍭', '2025-04-24 07:34:25', 14, 'text'),
(68, '1584189425_darkknight3.jpeg', '🥺🥀❤️‍🩹', '2025-04-24 07:36:29', 11, 'gambar'),
(70, '672114980_darkknight1.jpeg', 'im fail 🥀❤️‍🩹', '2025-04-24 07:38:13', 11, 'gambar'),
(71, '', '                    Push rank bukan sekadar main, ini perjuangan!🔥🔥', '2025-04-24 07:39:06', 9, 'text'),
(72, '1056744848_heaven2.jpeg', 'aurora 🌌\r\n', '2025-04-24 07:40:39', 12, 'gambar'),
(74, '758211272_lolipop2.jpeg', 'time to read book📚🐈', '2025-04-24 07:45:04', 14, 'gambar'),
(75, '410785889_lolipop3.jpeg', 'im little witch🔮🪄', '2025-04-24 07:47:22', 14, 'gambar'),
(77, '1908545153_nana3.jpeg', '😎😎', '2025-04-24 07:50:52', 15, 'gambar'),
(78, '1913862800_nana1.jpeg', 'kyuunnn~~', '2025-04-24 07:51:44', 15, 'gambar'),
(80, '2001980967_babyvan.jpeg', '🌙', '2025-04-24 08:02:07', 10, 'gambar'),
(81, '', 'aku hebat dan aku bangga...', '2025-04-24 08:09:43', 8, 'text'),
(82, '1327563162__ productivité _ motivation _ inspiration….jpeg', 'Produktif💻🖋️', '2025-04-24 08:11:22', 8, 'gambar'),
(83, '1224376827_converted_snapins-ai_3615749373204878597.mp4', 'wow', '2025-04-24 11:36:39', 7, 'video'),
(84, '67738748_converted_snapins-ai_3273446428654945784.mp4', '😙📯📯', '2025-04-24 11:39:02', 15, 'video'),
(85, '1439040651_anime.jpeg', 'pagiii...\r\n', '2025-04-25 01:25:08', 8, 'gambar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id_account`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `id_thread` (`id_thread`),
  ADD KEY `id_account` (`id_account`);

--
-- Indexes for table `friendlist`
--
ALTER TABLE `friendlist`
  ADD PRIMARY KEY (`id_friendlist`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_id` (`friend_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `created_at_2` (`created_at`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id_like`),
  ADD UNIQUE KEY `id_thread` (`id_thread`,`id_account`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `waktu` (`waktu`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id_account` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id_comment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `friendlist`
--
ALTER TABLE `friendlist`
  MODIFY `id_friendlist` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id_like` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_thread`) REFERENCES `threads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_account`) REFERENCES `account` (`id_account`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friendlist`
--
ALTER TABLE `friendlist`
  ADD CONSTRAINT `friendlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id_account`) ON DELETE CASCADE,
  ADD CONSTRAINT `friendlist_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `account` (`id_account`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`id_thread`) REFERENCES `threads` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`id_account`) REFERENCES `account` (`id_account`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `account` (`id_account`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `account` (`id_account`);

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `account` (`id_account`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
