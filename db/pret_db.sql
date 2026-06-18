-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 02:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pret_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `pret_bancaire`
--

CREATE TABLE `pret_bancaire` (
  `id` int(11) NOT NULL,
  `numero_compte` varchar(20) NOT NULL,
  `nom_client` varchar(100) NOT NULL,
  `nom_banque` varchar(100) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_pret` date NOT NULL,
  `taux_de_pret` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pret_bancaire`
--

INSERT INTO `pret_bancaire` (`id`, `numero_compte`, `nom_client`, `nom_banque`, `montant`, `date_pret`, `taux_de_pret`) VALUES
(1, 'ACC001', 'Rakoto Jean', 'BNI', 5000000.00, '2024-01-15', 0.05),
(2, 'ACC002', 'Rabe Marie', 'BOA', 3000000.00, '2024-02-20', 0.07),
(5, '333', 'barbare', 'Bank of zazio ', 99999.00, '2000-01-01', 0.06),
(7, '6156651', 'macs', 'société général', 1000.00, '2026-06-25', 0.10),
(8, '123456', 'boby', 'boursorama', 9000.00, '2026-06-25', 0.05),
(9, '852963', 'barbara', 'banqued\'élmore', 543.00, '0004-03-05', 435.00);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `mot_de_passe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `login`, `mot_de_passe`) VALUES
(1, 'admin', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pret_bancaire`
--
ALTER TABLE `pret_bancaire`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pret_bancaire`
--
ALTER TABLE `pret_bancaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
