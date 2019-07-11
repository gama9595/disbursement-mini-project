-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 11 Jul 2019 pada 18.05
-- Versi Server: 10.1.16-MariaDB
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flip`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `disburse`
--

CREATE TABLE `disburse` (
  `id` int(11) NOT NULL,
  `amount` int(14) NOT NULL,
  `status` varchar(30) NOT NULL,
  `timestamp` datetime NOT NULL,
  `bank_code` varchar(50) NOT NULL,
  `account_number` int(14) NOT NULL,
  `beneficiary_name` varchar(30) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `receipt` text,
  `time_served` datetime NOT NULL,
  `fee` int(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `disburse`
--
ALTER TABLE `disburse`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
