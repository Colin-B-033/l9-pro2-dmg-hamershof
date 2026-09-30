-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 23 sep 2026 om 13:35
-- Serverversie: 11.4.13-MariaDB-cll-lve
-- PHP-versie: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rocglas_hf`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `agenda`
--

CREATE TABLE `agenda` (
  `agenda_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_name` varchar(255) DEFAULT NULL,
  `color` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(500) NOT NULL,
  `event_date` date NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `events`
--

INSERT INTO `events` (`id`, `title`, `slug`, `category`, `image`, `event_date`, `start_time`, `end_time`, `location`, `description`, `is_free`, `created_at`, `updated_at`) VALUES
(1, 'Paasbrunch', 'paasbrunch-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-04-01', '10:00', '14:00', 'Restaurantgebied', 'Geniet van een heerlijke paasbrunch met speciale paasgerechten en activiteiten voor kinderen.', 0, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(2, 'Paaseieren Zoeken', 'paaseieren-zoeken-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-04-01', '14:00', '16:00', 'Centrale Plaza', 'Traditioneel paaseieren zoeken voor de kinderen met prijzen voor iedereen.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(3, 'Meivakantie Kinderweek', 'meivakantie-kinderweek-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-04-29', '10:00', '17:00', 'Speelplein', 'Speciale activiteiten voor kinderen tijdens de meivakantie met workshops en spelletjes.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(4, 'Meivakantie Familiedag', 'meivakantie-familiedag-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-05-04', '11:00', '18:00', 'Hele winkelcentrum', 'Een dag vol familie-activiteiten tijdens de meivakantie.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(5, 'Hemelvaartsdag Markt', 'hemelvaartsdag-markt-2024', 'Markt', '/public/assets/leegpandhamershof.png', '2024-05-09', '10:00', '17:00', 'Buitenplein', 'Lokale markt met ambachtelijke producten en lekkernijen.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(6, 'Pinksteren Familiefestival', 'pinksteren-familiefestival-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-05-19', '12:00', '20:00', 'Centrale Plaza', 'Groot familiefeest met live muziek, workshops en kinderactiviteiten.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(7, 'Zomeravond Markt', 'zomeravond-markt-2024', 'Markt', '/public/assets/leegpandhamershof.png', '2024-07-01', '16:00', '21:00', 'Buitenplein', 'Avondmarkt met lokale producten en zomerse sfeer.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(8, 'Herfstvakantie Kinderactiviteiten', 'herfstvakantie-kinderactiviteiten-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-10-12', '10:00', '16:00', 'Speelplein', 'Herfstactiviteiten voor kinderen met pompoenen snijden en herfstversieringen maken.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(9, 'Kerstboom Ontbranding', 'kerstboom-ontbranding-2024', 'Familie', '/public/assets/leegpandhamershof.png', '2024-11-30', '17:00', '19:00', 'Centrale Plaza', 'OfficiÃ«le ontbranding van de grote kerstboom met kerstliederen en warme chocolademelk.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(10, 'Kerstmarkt', 'kerstmarkt-2024', 'Markt', '/public/assets/leegpandhamershof.png', '2024-12-01', '10:00', '20:00', 'Hele winkelcentrum', 'Magische kerstmarkt met handgemaakte cadeaus, lekkernijen en kerstversieringen.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(11, 'Kerstdiner', 'kerstdiner-2024', 'Culinair', '/public/assets/leegpandhamershof.png', '2024-12-25', '18:00', '22:00', 'Restaurantgebied', 'Speciaal kerstmenu met traditionele gerechten en feestelijke ambiance.', 0, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(12, 'Oudejaarsavond Feest', 'oudejaarsavond-feest-2024', 'Muziek', '/public/assets/leegpandhamershof.png', '2024-12-31', '20:00', '01:00', 'Centrale Plaza', 'Groot eindejaarsfeest met live muziek, champagne en vuurwerk.', 0, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(13, 'Voorjaarsvakantie Wintersport', 'voorjaarsvakantie-wintersport-2025', 'Sport', '/public/assets/leegpandhamershof.png', '2025-02-17', '10:00', '17:00', 'Sportplein', 'Wintersport demonstraties en workshops voor jong en oud.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(14, 'Voorjaarsvakantie Carnaval', 'voorjaarsvakantie-carnaval-2025', 'Familie', '/public/assets/leegpandhamershof.png', '2025-02-25', '14:00', '18:00', 'Centrale Plaza', 'Carnavalsoptocht en feestelijke activiteiten voor het hele gezin.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(15, 'Paasconcerten', 'paasconcerten-2025', 'Muziek', '/public/assets/leegpandhamershof.png', '2025-04-20', '15:00', '17:00', 'Muziekplein', 'Klassieke paasconcerten met lokale muzikanten.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(16, 'Bevrijdingsdag Picknick', 'bevrijdingsdag-picknick-2025', 'Familie', '/public/assets/leegpandhamershof.png', '2025-05-05', '12:00', '18:00', 'Buitenplein', 'Gezellige picknick met live muziek en activiteiten.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(17, 'Moederdag Brunch', 'moederdag-brunch-2025', 'Familie', '/public/assets/leegpandhamershof.png', '2025-05-11', '10:00', '14:00', 'Restaurantgebied', 'Speciale moederdag brunch met bloemen en attenties.', 0, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(18, 'Vaderdag BBQ', 'vaderdag-bbq-2025', 'Culinair', '/public/assets/leegpandhamershof.png', '2025-06-15', '12:00', '20:00', 'Buitenplein', 'Gezellige vaderdag BBQ met verschillende soorten vlees en bijgerechten.', 0, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(19, 'Zomeravond Film', 'zomeravond-film-2025', 'Familie', '/public/assets/leegpandhamershof.png', '2025-07-15', '21:00', '23:00', 'Buitenplein', 'Gratis openlucht filmavond met zomerse films voor het hele gezin.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(20, 'Zomer Festival', 'zomer-festival-2025', 'Muziek', '/public/assets/leegpandhamershof.png', '2025-08-10', '14:00', '23:00', 'Centrale Plaza', 'Groot zomerfestival met diverse artiesten en activiteiten.', 0, '2026-04-08 09:47:57', '2026-04-08 09:47:57'),
(21, 'Prinsjesdag Oranje Markt', 'prinsjesdag-oranje-markt-2025', 'Markt', '/public/assets/leegpandhamershof.png', '2025-09-15', '10:00', '17:00', 'Buitenplein', 'Oranje gekleurde markt ter ere van Prinsjesdag.', 1, '2026-04-08 09:47:57', '2026-04-08 09:47:57');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `map_areas`
--

CREATE TABLE `map_areas` (
  `map_area_id` int(11) NOT NULL,
  `winkel_id` int(11) NOT NULL,
  `coordinate` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `news_image`
--

CREATE TABLE `news_image` (
  `image_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `size` varchar(50) NOT NULL,
  `availability` varchar(100) NOT NULL,
  `status_class` varchar(50) NOT NULL COMMENT 'beschikbaar or bijna-beschikbaar',
  `image` varchar(500) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `properties`
--

INSERT INTO `properties` (`id`, `title`, `slug`, `location`, `size`, `availability`, `status_class`, `image`, `summary`, `description`, `features`, `details`, `created_at`, `updated_at`) VALUES
(1, 'Ruime Winkelunit Centrum', 'ruime-winkelunit-centrum', 'Locatie A - Begane Grond', '125 m²', 'Beschikbaar', 'beschikbaar', '/public/assets/leegpandhamershof.png', 'Moderne winkelruimte in het hart van het winkelcentrum met veel daglicht en uitstekende zichtbaarheid.', 'Deze ruime winkelunit biedt een royale etalage, een hoge plafonds en een flexibele indeling voor conceptstores, mode of lifestyle-formules.', '[\"Ruime gevel met veel zichtbaarheid\", \"Directe toegang vanaf de begane grond\", \"Hoge voetgangersstroom en centrale locatie\"]', '{\"Huurprijs\": \"Op aanvraag\", \"Verdieping\": \"Begane grond\", \"Oppervlakte\": \"125 m²\", \"Parkeerplaatsen\": \"Inclusief parkeermogelijkheden\"}', '2026-04-08 13:04:32', '2026-04-08 13:04:32'),
(2, 'Hoekpand met Etalage', 'hoekpand-met-etalage', 'Locatie B - Hoofdplein', '85 m²', 'Beschikbaar', 'beschikbaar', '/public/assets/leegpandhamershof.png', 'Strategisch gelegen hoekpand met extra etalageruimte en hoge voetgangersfrequentie.', 'Een aantrekkelijke hoeklocatie die perfect is voor visuele concepten, horeca of specialistische retail met veel zichtbaarheid.', '[\"Grote hoeketalage\", \"Hoge passage van bezoekers\", \"Veel lichtinval en presentatie mogelijkheden\"]', '{\"Huurprijs\": \"Op aanvraag\", \"Verdieping\": \"Begane grond\", \"Oppervlakte\": \"85 m²\", \"Parkeerplaatsen\": \"Ruime parkeermogelijkheid nabij\"}', '2026-04-08 13:04:32', '2026-04-08 13:04:32'),
(3, 'Compacte Retail Space', 'compacte-retail-space', 'Locatie C - Zijgang', '55 m²', 'Binnenkort Beschikbaar', 'bijna-beschikbaar', '/public/assets/leegpandhamershof.png', 'Ideale ruimte voor specialty retail of dienstverlening, volledig gerenoveerd.', 'Een compact pand met moderne afwerking, uitstekend geschikt voor conceptstores, beauty of dienstverlening met een warme uitstraling.', '[\"Gerenoveerde winkelruimte\", \"Ideaal voor niche-concepten\", \"Verhoogde exposure in een drukke zijgang\"]', '{\"Huurprijs\": \"Op aanvraag\", \"Verdieping\": \"Begane grond\", \"Oppervlakte\": \"55 m²\", \"Parkeerplaatsen\": \"450+ parkeerplaatsen in nabijheid\"}', '2026-04-08 13:04:32', '2026-04-08 13:04:32'),
(4, 'Premium Winkelruimte', 'premium-winkelruimte', 'Locatie D - Entree Gebied', '180 m²', 'Beschikbaar', 'beschikbaar', '/public/assets/leegpandhamershof.png', 'Grote winkelunit met hoge plafonds en flexibele indeling, perfect voor flagship stores.', 'Deze premium ruimte biedt een representatieve uitstraling en veel flexibiliteit voor formules die ruimte en impact nodig hebben.', '[\"Hoge plafonds en open zichtlijnen\", \"Ruime winkelvloer\", \"Achterruimte geschikt voor opslag of kantoor\"]', '{\"Huurprijs\": \"Op aanvraag\", \"Verdieping\": \"Begane grond\", \"Oppervlakte\": \"180 m²\", \"Parkeerplaatsen\": \"Gemakkelijke toegang tot entree en parkeergelegenheid\"}', '2026-04-08 13:04:32', '2026-04-08 13:04:32');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `winkels`
--

CREATE TABLE `winkels` (
  `winkel_id` int(11) NOT NULL,
  `winkel_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `opening_hours` text DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `winkels`
--

INSERT INTO `winkels` (`winkel_id`, `winkel_name`, `category_id`, `description`, `logo`, `cover_image`, `phone`, `email`, `website`, `location`, `opening_hours`, `owner_id`, `created_at`, `updated_at`) VALUES
(33, 'electronica winkel', 4, 'test', 'uploads/elektronica-winkel.png', 'uploads/elektronica-winkel.png', '12345678', 'winkel123@gmail.com', 'ja', 'ergens', NULL, NULL, '2026-04-15 11:04:53', '2026-04-15 11:04:53'),
(34, 'eten en drinken winkel', 2, 'eten', 'uploads/eten-drinkenwinkel.png', 'uploads/eten-drinkenwinkel.png', '12345656789', 'dirnken@gmail.com', '', 'Pakistan', NULL, NULL, '2026-04-15 11:06:00', '2026-04-15 11:06:00'),
(35, 'mode % accesoires', 1, 'mode', 'uploads/kleding.png', 'uploads/kleding.png', '', '', '', '', NULL, NULL, '2026-04-15 11:07:36', '2026-04-15 11:07:36'),
(36, 'Restaurant', 6, 'eten', 'uploads/restaurant.png', 'uploads/restaurant.png', '', '', '', '', NULL, NULL, '2026-04-15 11:08:51', '2026-04-15 11:08:51'),
(37, 'Jumbo', 3, 'jumbo', 'uploads/jumbo.png', 'uploads/jumbo.png', '', '', '', '', NULL, NULL, '2026-04-15 11:09:04', '2026-04-15 11:09:04'),
(38, 'wonen', 5, 'wonen', 'uploads/wonen.png', 'uploads/wonen.png', '', '', '', '', NULL, NULL, '2026-04-15 11:09:16', '2026-04-15 11:09:16');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `winkel_categories`
--

CREATE TABLE `winkel_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `winkel_categories`
--

INSERT INTO `winkel_categories` (`category_id`, `category_name`) VALUES
(4, 'Elektronica'),
(2, 'Eten & Drinken'),
(1, 'Mode & Accesoires'),
(6, 'Restaurant'),
(3, 'supermarkt'),
(5, 'Wonen & Livestyle');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `winkel_images`
--

CREATE TABLE `winkel_images` (
  `image_id` int(11) NOT NULL,
  `winkel_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `winkel_images`
--

INSERT INTO `winkel_images` (`image_id`, `winkel_id`, `image_path`) VALUES
(19, 33, 'uploads/elektronica-winkel.png'),
(20, 34, 'uploads/eten-drinkenwinkel.png'),
(21, 35, 'uploads/kleding.png'),
(22, 36, 'uploads/restaurant.png'),
(23, 37, 'uploads/jumbo.png'),
(24, 38, 'uploads/wonen.png');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `winkel_openingstijden`
--

CREATE TABLE `winkel_openingstijden` (
  `id` int(11) NOT NULL,
  `winkel_id` int(11) NOT NULL,
  `dag` varchar(20) NOT NULL,
  `open_tijd` time DEFAULT NULL,
  `sluit_tijd` time DEFAULT NULL,
  `gesloten` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `winkel_openingstijden`
--

INSERT INTO `winkel_openingstijden` (`id`, `winkel_id`, `dag`, `open_tijd`, `sluit_tijd`, `gesloten`) VALUES
(22, 33, 'Maandag', NULL, NULL, 1),
(23, 33, 'Dinsdag', '09:00:00', '18:00:00', 0),
(24, 33, 'Woensdag', '09:00:00', '18:00:00', 0),
(25, 33, 'Donderdag', '09:00:00', '18:00:00', 0),
(26, 33, 'Vrijdag', '09:00:00', '18:00:00', 0),
(27, 33, 'Zaterdag', '09:00:00', '18:00:00', 0),
(28, 33, 'Zondag', NULL, NULL, 1),
(29, 34, 'Maandag', '09:00:00', '18:00:00', 0),
(30, 34, 'Dinsdag', '09:00:00', '18:00:00', 0),
(31, 34, 'Woensdag', NULL, NULL, 1),
(32, 34, 'Donderdag', '09:00:00', '18:00:00', 0),
(33, 34, 'Vrijdag', '09:00:00', '18:00:00', 0),
(34, 34, 'Zaterdag', '09:00:00', '18:00:00', 0),
(35, 34, 'Zondag', NULL, NULL, 1),
(36, 35, 'Maandag', '09:00:00', '18:00:00', 0),
(37, 35, 'Dinsdag', '09:00:00', '18:00:00', 0),
(38, 35, 'Woensdag', '09:00:00', '18:00:00', 0),
(39, 35, 'Donderdag', '09:00:00', '18:00:00', 0),
(40, 35, 'Vrijdag', '09:00:00', '18:00:00', 0),
(41, 35, 'Zaterdag', '09:00:00', '18:00:00', 0),
(42, 35, 'Zondag', NULL, NULL, 1),
(43, 36, 'Maandag', '09:00:00', '18:00:00', 0),
(44, 36, 'Dinsdag', '09:00:00', '18:00:00', 0),
(45, 36, 'Woensdag', '09:00:00', '18:00:00', 0),
(46, 36, 'Donderdag', '09:00:00', '18:00:00', 0),
(47, 36, 'Vrijdag', NULL, NULL, 1),
(48, 36, 'Zaterdag', '09:00:00', '18:00:00', 0),
(49, 36, 'Zondag', NULL, NULL, 1),
(50, 37, 'Maandag', '09:00:00', '18:00:00', 0),
(51, 37, 'Dinsdag', '09:00:00', '18:00:00', 0),
(52, 37, 'Woensdag', '09:00:00', '18:00:00', 0),
(53, 37, 'Donderdag', '09:00:00', '18:00:00', 0),
(54, 37, 'Vrijdag', '09:00:00', '18:00:00', 0),
(55, 37, 'Zaterdag', '09:00:00', '18:00:00', 0),
(56, 37, 'Zondag', NULL, NULL, 1),
(57, 38, 'Maandag', '09:00:00', '18:00:00', 0),
(58, 38, 'Dinsdag', '09:00:00', '18:00:00', 0),
(59, 38, 'Woensdag', '09:00:00', '18:00:00', 0),
(60, 38, 'Donderdag', '09:00:00', '18:00:00', 0),
(61, 38, 'Vrijdag', '09:00:00', '18:00:00', 0),
(62, 38, 'Zaterdag', NULL, NULL, 1),
(63, 38, 'Zondag', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `winkel_services`
--

CREATE TABLE `winkel_services` (
  `service_id` int(11) NOT NULL,
  `winkel_id` int(11) NOT NULL,
  `service_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `service_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`agenda_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexen voor tabel `map_areas`
--
ALTER TABLE `map_areas`
  ADD PRIMARY KEY (`map_area_id`),
  ADD KEY `winkel_id` (`winkel_id`);

--
-- Indexen voor tabel `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `news_image`
--
ALTER TABLE `news_image`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexen voor tabel `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexen voor tabel `winkels`
--
ALTER TABLE `winkels`
  ADD PRIMARY KEY (`winkel_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexen voor tabel `winkel_categories`
--
ALTER TABLE `winkel_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexen voor tabel `winkel_images`
--
ALTER TABLE `winkel_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `winkel_id` (`winkel_id`);

--
-- Indexen voor tabel `winkel_openingstijden`
--
ALTER TABLE `winkel_openingstijden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `winkel_id` (`winkel_id`);

--
-- Indexen voor tabel `winkel_services`
--
ALTER TABLE `winkel_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `winkel_id` (`winkel_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `agenda`
--
ALTER TABLE `agenda`
  MODIFY `agenda_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT voor een tabel `map_areas`
--
ALTER TABLE `map_areas`
  MODIFY `map_area_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `news_image`
--
ALTER TABLE `news_image`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `winkels`
--
ALTER TABLE `winkels`
  MODIFY `winkel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT voor een tabel `winkel_categories`
--
ALTER TABLE `winkel_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT voor een tabel `winkel_images`
--
ALTER TABLE `winkel_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT voor een tabel `winkel_openingstijden`
--
ALTER TABLE `winkel_openingstijden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT voor een tabel `winkel_services`
--
ALTER TABLE `winkel_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `map_areas`
--
ALTER TABLE `map_areas`
  ADD CONSTRAINT `map_areas_ibfk_1` FOREIGN KEY (`winkel_id`) REFERENCES `winkels` (`winkel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `news_image`
--
ALTER TABLE `news_image`
  ADD CONSTRAINT `news_image_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `winkels`
--
ALTER TABLE `winkels`
  ADD CONSTRAINT `winkels_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `winkel_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `winkels_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `winkel_images`
--
ALTER TABLE `winkel_images`
  ADD CONSTRAINT `winkel_images_ibfk_1` FOREIGN KEY (`winkel_id`) REFERENCES `winkels` (`winkel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `winkel_openingstijden`
--
ALTER TABLE `winkel_openingstijden`
  ADD CONSTRAINT `winkel_openingstijden_ibfk_1` FOREIGN KEY (`winkel_id`) REFERENCES `winkels` (`winkel_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `winkel_services`
--
ALTER TABLE `winkel_services`
  ADD CONSTRAINT `winkel_services_ibfk_1` FOREIGN KEY (`winkel_id`) REFERENCES `winkels` (`winkel_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
