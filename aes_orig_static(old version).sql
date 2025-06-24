-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 02:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aes_orig`
--

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `division_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `division_name`) VALUES
(1, 'IT Department'),
(2, 'Finance Department'),
(3, 'Human Resources');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id_file` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `file_name_source` varchar(255) NOT NULL,
  `file_name_finish` varchar(255) NOT NULL,
  `file_url` text NOT NULL,
  `file_size_kb` float DEFAULT NULL,
  `alg_used` varchar(25) NOT NULL,
  `process_time_ms` float DEFAULT NULL,
  `operation_type` varchar(20) NOT NULL,
  `hash_check` varchar(64) DEFAULT NULL,
  `status` enum('1','2') NOT NULL COMMENT '1=Terenkripsi, 2=Terdekripsi',
  `keterangan` text DEFAULT NULL,
  `password_salt_hex` varchar(32) DEFAULT NULL,
  `file_iv_hex` varchar(32) DEFAULT NULL,
  `kdf_iterations` int(11) DEFAULT 10000,
  `tgl_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `tgl_encrypt` timestamp NULL DEFAULT NULL,
  `tgl_decrypt` timestamp NULL DEFAULT NULL,
  `sumber_id_file` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`id_file`, `username`, `file_name_source`, `file_name_finish`, `file_url`, `file_size_kb`, `alg_used`, `process_time_ms`, `operation_type`, `hash_check`, `status`, `keterangan`, `password_salt_hex`, `file_iv_hex`, `kdf_iterations`, `tgl_upload`, `tgl_encrypt`, `tgl_decrypt`, `sumber_id_file`) VALUES
(1, 'super', '(Before Methodology))(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.docx', 'enc_1749532085__Before_Methodology___ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.docx.enc', 'dashboard/hasil_enkripsi/enc_1749532085__Before_Methodology___ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.docx.enc', 5291.36, 'AES-128-CBC', 6.2232, 'encrypt', '6baa06f6ce58981643877a0c17319356742670373ec48a8311f26c98722e1656', '1', '1234', '342c7a0f70547ad8cf5d9725ccf02d70', '0267af46ccac3cf32e80570164a6a91c', 10000, '2025-06-10 00:08:05', '2025-06-10 00:08:05', NULL, NULL),
(2, 'super', 'Backup(Before Method)(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.docx', 'enc_1749576739_Backup_Before_Method__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.docx.enc', 'dashboard/hasil_enkripsi/enc_1749576739_Backup_Before_Method__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.docx.enc', 5291.36, 'AES-128-CBC', 6.1989, 'encrypt', '2ce565fceeed2ac2959d6f94621ec04a95196471712f87ded8f7c2b9e3b7f5be', '1', '1234', 'd73c7da5a02734b22acdfc1be1fcf310', 'f44e1410e35ac78efe5b4c1a48a612e8', 10000, '2025-06-10 12:32:19', '2025-06-10 12:32:19', NULL, NULL),
(3, 'super', '(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'enc_1749576763__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 'dashboard/hasil_enkripsi/enc_1749576763__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 710.53, 'AES-256-CBC', 1.0018, 'encrypt', '7359d86b14b51a5a164c40a45f6e961a57d8a8bb07e54db166b98650dc530b63', '1', '1234', '5f7a731c586e03e605c02971ac565c66', 'c59760c89d8daf9ab48b0b481e83a173', 10000, '2025-06-10 12:32:43', '2025-06-10 12:32:43', NULL, NULL),
(4, 'super', '(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'enc_1749576781__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 'dashboard/hasil_enkripsi/enc_1749576781__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 710.53, 'AES-128-CBC', 0.7679, 'encrypt', '54fda8b735cd285c4d6c91473206a4993cc839bf35c1097818c5a8fc8980fb00', '1', '1234', '9af301685ba6d8f4e836d0847dc4c950', 'fee7b9a5fabf6b8076602de7186f2e15', 10000, '2025-06-10 12:33:01', '2025-06-10 12:33:01', NULL, NULL),
(5, 'super', 'Surat-Izin_11-april-25_Faizal.pdf', 'dec_1749577146_Surat-Izin_11-april-25_Faizal.pdf', 'decrypted_result/dec_1749577146_Surat-Izin_11-april-25_Faizal.pdf', 66.7, 'AES-128-CBC', 0.96, 'dekripsi (simulasi)', '1c96d4934bfef97d9a66612597be7b4e812001d728cb1ec6d041ed60a539470a', '2', '1234', '51789bda6178e13e514c961e1821be19', '068d59c21989f59dbe8311c99dcb4a1c', 10000, '2025-06-10 12:35:25', '2025-06-10 12:35:25', NULL, NULL),
(6, 'Admin', '(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'enc_1750141476__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 'dashboard/encrypted_result/enc_1750141476__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 710.53, 'AES-128-CBC', 3.4311, 'encrypt', '41909a68f14a169321448b4f4d16a0b0b8c4347607a496fc26acd6ca8ff109a1', '1', '1234', '77bb49dda6d5f999126596d62427710b', '4335ed6b0ad5f8a7d4d1d23075cb47da', 10000, '2025-06-17 01:24:36', '2025-06-17 01:24:36', NULL, NULL),
(7, 'Admin', '(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'enc_1750141498__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 'dashboard/encrypted_result/enc_1750141498__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 710.53, 'AES-256-CBC', 1.102, 'encrypt', 'f887435d5b393d13b5d2579e9945d42a73e81b43283ca9c197e9e1dce824f6a3', '1', '1234', '57bf4eae12ee3cac6fb6b6ecaea6dced', '4cbe8486c1ed872a0391fd2bd2362b01', 10000, '2025-06-17 01:24:58', '2025-06-17 01:24:58', NULL, NULL),
(8, 'Admin', '(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'dec_1750181741__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'decrypted_result/dec_1750181741__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 710.53, 'AES-128-CBC', 3.8, 'dekripsi (simulasi)', 'ef5724db0abe2769783265d38d74ed2783b9ece59c1ea59825b0b878e4324d0f', '2', '1234', 'b04387ae10ca82c903792fb1d9e447d8', 'e8f060690cd53b2dda09a8d37848a00e', 10000, '2025-06-17 01:29:56', '2025-06-17 01:29:56', NULL, NULL),
(9, 'Admin', '(ENG)_Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf', 'enc_1750141813__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 'dashboard/encrypted_result/enc_1750141813__ENG__Faizal_4F_2141720246_Proposal-Skripsi_Development.pdf.enc', 710.53, 'AES-128-CBC', 0.742, 'encrypt', '4103f19876b043387e6bb172eea9b05cfcaa124334e2f28987f43fcfc66693c5', '1', '1234', '4703462edddf206cede9993124cf4fa8', 'd23c44dfad73c22e6e0416134028691a', 10000, '2025-06-17 01:30:13', '2025-06-17 01:30:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `join_date` timestamp NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NULL DEFAULT NULL,
  `role` enum('superadmin','admin','reviewer') NOT NULL DEFAULT 'admin',
  `status` enum('1','2') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `job_title`, `division_id`, `join_date`, `last_activity`, `role`, `status`) VALUES
(2, 'Admin', '$2y$10$G3r0anxiHwxyaK22sDwQNuyi6lZU6DCwDN4ozErOKpy3zUKOSiEtO', 'Super Admin', 'Admin', NULL, '2025-05-25 12:22:27', '2025-06-18 04:56:36', 'superadmin', '1'),
(3, 'Divisi', '$2y$10$Sz41luCRJWZXQGslxDexieWingTnC5sM9haq2FALrwgJx1x50ZT.i', 'Master Divisi', ' Master Divisi', 1, '2025-05-25 12:22:27', '2025-06-16 06:24:55', 'admin', '1'),
(4, 'User', '$2y$10$9K0.xgypOEpv7skaMhA7luSRaAKGU69DTxWr3.kkZ5Rd2Xfy6ZC7O', 'Master User', 'Master User', 3, '2025-05-25 12:22:27', '2025-05-28 02:52:39', 'reviewer', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id_file`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_id` (`division_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `id_file` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
