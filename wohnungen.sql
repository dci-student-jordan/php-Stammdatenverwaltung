-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Jun 2024 um 09:27
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `wohnungen`
--
CREATE DATABASE IF NOT EXISTS `wohnungen` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `wohnungen`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `angebot`
--

CREATE TABLE `angebot` (
  `id` int(11) NOT NULL,
  `raum_id` int(11) NOT NULL,
  `gesamt_preis` decimal(6,2) DEFAULT NULL,
  `erledigt` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `angebot`
--

INSERT INTO `angebot` (`id`, `raum_id`, `gesamt_preis`, `erledigt`) VALUES
(30, 1, 247.21, 1),
(31, 4, 259.72, 1),
(32, 2, 225.71, 1),
(33, 3, 461.17, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `angebot_inventar`
--

CREATE TABLE `angebot_inventar` (
  `id` int(11) NOT NULL,
  `angebot_id` int(11) NOT NULL,
  `inventar_id` int(11) NOT NULL,
  `anzahl` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `angebot_inventar`
--

INSERT INTO `angebot_inventar` (`id`, `angebot_id`, `inventar_id`, `anzahl`) VALUES
(23, 30, 28, 3),
(24, 30, 24, 2),
(25, 30, 26, 1),
(26, 30, 23, 12),
(27, 31, 28, 3),
(28, 31, 24, 3),
(29, 31, 26, 1),
(30, 31, 17, 1),
(31, 31, 23, 10),
(32, 32, 27, 3),
(33, 32, 24, 2),
(34, 32, 26, 1),
(35, 32, 23, 8),
(36, 33, 28, 4),
(37, 33, 24, 6),
(38, 33, 26, 2),
(39, 33, 23, 13);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inventar`
--

CREATE TABLE `inventar` (
  `id` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  `hersteller` varchar(100) DEFAULT NULL,
  `produkt` varchar(250) NOT NULL,
  `farbe` varchar(50) DEFAULT NULL,
  `preis` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `inventar`
--

INSERT INTO `inventar` (`id`, `typ`, `hersteller`, `produkt`, `farbe`, `preis`) VALUES
(17, 10, 'Gira', 'Lichtschalter 010800', 'weiß', 2.49),
(19, 12, 'Rahmix', 'Türrahmen \"Rähmchen\"', 'weiß', 79.89),
(20, 20, 'Velux', 'Fenster 0815', 'schwarz', 233.59),
(22, 22, 'TOUCAN-T', 'Twek', '', 2.97),
(23, 21, '', 'Fußleiste', 'holzoptik', 6.77),
(24, 18, 'Gr4tec', '5W LED Einbaustrahler', '', 23.56),
(25, 14, 'Busch-Jaeger', 'Schutzkontakt-Steckdosen 20EUC-914', '', 9.83),
(26, 19, '', 'Kompaktheizkörper BH_DOC_87864', 'RAL9016 Verkehrsweiß', 110.00),
(27, 14, 'Gira', 'Steckdose 418828', 'schwarz', 4.81),
(28, 14, 'Jung', 'A1520WW Schuko Steckdose', 'alpinweiss', 2.95);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inventar_typ`
--

CREATE TABLE `inventar_typ` (
  `id` int(11) NOT NULL,
  `typ` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `inventar_typ`
--

INSERT INTO `inventar_typ` (`id`, `typ`) VALUES
(10, 'Lichtschalter'),
(12, 'Türrahmen'),
(14, 'Steckdose'),
(18, 'Einbaustrahler'),
(19, 'Heizkörper'),
(20, 'Fenster'),
(21, 'Fußleiste'),
(22, 'Teppich');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `raum`
--

CREATE TABLE `raum` (
  `id` int(11) NOT NULL,
  `wohnung_id` int(11) NOT NULL,
  `qm` decimal(5,2) DEFAULT NULL,
  `notiz` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `raum`
--

INSERT INTO `raum` (`id`, `wohnung_id`, `qm`, `notiz`) VALUES
(1, 8, 17.10, 'Küche'),
(2, 8, 8.00, 'Flur'),
(3, 8, 20.60, 'Wohnzimmer'),
(4, 14, 12.30, 'Bad'),
(5, 2, 11.00, 'Flur'),
(8, 8, 16.00, 'Schlafzimmer'),
(9, 14, 6.50, 'Flur'),
(10, 14, 21.00, 'Wohnzimmer'),
(11, 14, 15.10, 'Schlafzimmer'),
(13, 8, 9.90, 'Bad'),
(14, 2, 47.60, 'Wohnkücke'),
(15, 2, 38.40, 'Waschraum'),
(16, 9, 23.00, 'Wohnzimmer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `raum_inventar`
--

CREATE TABLE `raum_inventar` (
  `id` int(11) NOT NULL,
  `raum_id` int(11) NOT NULL,
  `inventar_id` int(11) NOT NULL,
  `menge` int(11) NOT NULL,
  `mengen_mass` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `raum_inventar`
--

INSERT INTO `raum_inventar` (`id`, `raum_id`, `inventar_id`, `menge`, `mengen_mass`) VALUES
(7, 1, 17, 2, 'Stück'),
(8, 1, 19, 1, ''),
(15, 2, 19, 1, ''),
(16, 2, 22, 7, 'm2'),
(17, 2, 17, 2, ''),
(18, 3, 20, 2, 'Stück'),
(19, 3, 17, 3, 'Stück'),
(20, 13, 25, 1, 'Stk'),
(21, 1, 24, 5, ''),
(27, 1, 28, 3, ''),
(28, 1, 26, 1, ''),
(29, 1, 23, 12, ''),
(30, 4, 28, 3, ''),
(31, 4, 24, 3, ''),
(32, 4, 26, 1, ''),
(33, 4, 17, 1, ''),
(34, 4, 23, 10, ''),
(35, 2, 27, 3, ''),
(36, 2, 24, 2, ''),
(37, 2, 26, 1, ''),
(38, 2, 23, 8, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `vorname` varchar(35) DEFAULT NULL,
  `nachname` varchar(35) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  `pw` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `vorname`, `nachname`, `user`, `is_admin`, `pw`) VALUES
(1, 'Bob', 'Blue', 'bobby@blue.buddy', 1, '$2y$10$KueUCOo1VujT79Y/4q21E.XdAYrFuovTJ6f5ZjgERhH1/dfsacRdW'),
(2, 'Jenny', 'Danner', 'jenny@dan.ner', 0, '$2y$10$4NBwbSZXgUjhrUHnYM8oxOUz1Vf2PO2lEA.JvAcrzKSJT6c7JP6pG'),
(3, 'Benjamin', 'Button', 'ben@jam.in', NULL, '$2y$10$w/UYUIBgQef.qGS0grzMFuLdOMpmM2bwUb.0r.V3YWh4oGufoWj32');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wohnung`
--

CREATE TABLE `wohnung` (
  `id` int(11) NOT NULL,
  `strasse` varchar(255) DEFAULT NULL,
  `hausnummer` varchar(25) DEFAULT NULL,
  `plz` int(11) DEFAULT NULL,
  `stadt` varchar(50) DEFAULT NULL,
  `bundesland` varchar(50) DEFAULT NULL,
  `etage` int(11) DEFAULT NULL,
  `groesse` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `wohnung`
--

INSERT INTO `wohnung` (`id`, `strasse`, `hausnummer`, `plz`, `stadt`, `bundesland`, `etage`, `groesse`) VALUES
(2, 'Beim Acker', '5a', 32657, 'Lemgo', 'Nordrhein-Westfalen', 3, '97 qm'),
(5, 'Umme Ecke', '3', 24321, 'Behrensdorf', 'Schleswig-Holstein', 21, '62 qm'),
(7, 'Beim Bock', '79', 56317, 'Linkenbach', 'Rheinland-Pfalz', NULL, '151 qm'),
(8, 'Aufer Ollen', '17', 89522, 'Heidenheim an der Brenz', 'Baden-Württemberg', 2, '71,6 qm'),
(9, 'Beim Acker', '5a', 32657, 'Lemgo', 'Nordrhein-Westfalen', 2, '97 qm'),
(12, 'Kurze Kurve', '198', 67454, 'Haßloch', 'Rheinland-Pfalz', 5, '37 qm'),
(13, 'Kurze Kurve', '198', 67454, 'Haßloch', 'Rheinland-Pfalz', 4, '37 qm'),
(14, 'Aufer Ollen', '17', 89522, 'Heidenheim an der Brenz', 'Baden-Württemberg', 1, '54,9 qm'),
(15, 'Umme Ecke', '3', 24321, 'Behrensdorf', 'Schleswig-Holstein', 20, '62 qm'),
(16, 'Umme Ecke', '3', 24321, 'Behrensdorf', 'Schleswig-Holstein', 19, '62 qm'),
(17, 'Aufer Ollen', '17', 89522, 'Heidenheim an der Brenz', 'Baden-Württemberg', 3, '51 qm'),
(18, 'Beim Bock', '79', 56317, 'Linkenbach', 'Rheinland-Pfalz', 1, '151 qm'),
(19, 'Am Arsch der Heide', '36', NULL, 'Irgendwo', NULL, NULL, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `angebot`
--
ALTER TABLE `angebot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_angebot_raum` (`raum_id`);

--
-- Indizes für die Tabelle `angebot_inventar`
--
ALTER TABLE `angebot_inventar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_angebot_inventar_inventar` (`inventar_id`),
  ADD KEY `angebot-inventar_angebot` (`angebot_id`);

--
-- Indizes für die Tabelle `inventar`
--
ALTER TABLE `inventar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_inventar_type` (`typ`);

--
-- Indizes für die Tabelle `inventar_typ`
--
ALTER TABLE `inventar_typ`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `raum`
--
ALTER TABLE `raum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_raum_wohnung` (`wohnung_id`);

--
-- Indizes für die Tabelle `raum_inventar`
--
ALTER TABLE `raum_inventar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_raum-inventar_raum` (`raum_id`),
  ADD KEY `FK_raum-inventar_inventar` (`inventar_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `wohnung`
--
ALTER TABLE `wohnung`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `angebot`
--
ALTER TABLE `angebot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT für Tabelle `angebot_inventar`
--
ALTER TABLE `angebot_inventar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT für Tabelle `inventar`
--
ALTER TABLE `inventar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT für Tabelle `inventar_typ`
--
ALTER TABLE `inventar_typ`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT für Tabelle `raum`
--
ALTER TABLE `raum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT für Tabelle `raum_inventar`
--
ALTER TABLE `raum_inventar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `wohnung`
--
ALTER TABLE `wohnung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `angebot_inventar`
--
ALTER TABLE `angebot_inventar`
  ADD CONSTRAINT `FK_angebot_inventar_inventar` FOREIGN KEY (`inventar_id`) REFERENCES `inventar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `angebot-inventar_angebot` FOREIGN KEY (`angebot_id`) REFERENCES `angebot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `inventar`
--
ALTER TABLE `inventar`
  ADD CONSTRAINT `FK_inventar_type` FOREIGN KEY (`typ`) REFERENCES `inventar_typ` (`id`);

--
-- Constraints der Tabelle `raum`
--
ALTER TABLE `raum`
  ADD CONSTRAINT `FK_raum_wohnung` FOREIGN KEY (`wohnung_id`) REFERENCES `wohnung` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `raum_inventar`
--
ALTER TABLE `raum_inventar`
  ADD CONSTRAINT `FK_raum-inventar_inventar` FOREIGN KEY (`inventar_id`) REFERENCES `inventar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_raum-inventar_raum` FOREIGN KEY (`raum_id`) REFERENCES `raum` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
