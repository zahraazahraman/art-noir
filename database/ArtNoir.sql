-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 06, 2026 at 06:45 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ArtNoir`
--

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `biography` text DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `birth_year` int(11) DEFAULT NULL,
  `death_year` int(11) DEFAULT NULL,
  `artist_type` enum('historical','community') DEFAULT 'community',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `biography`, `country_id`, `birth_year`, `death_year`, `artist_type`, `user_id`, `created_at`) VALUES
(1, 'Vincent van Gogh', 'Dutch post-impressionist painter.', 120, 1853, 1890, 'historical', NULL, '2026-01-06 02:11:21'),
(2, 'Frida Kahlo', 'Mexican painter known for her self-portraits.', 108, 1907, 1954, 'historical', NULL, '2026-01-06 02:01:21'),
(3, 'Layla Hassan', 'Digital artist and illustrator.', 92, 1997, 2025, 'historical', 2, '2026-01-06 01:51:21'),
(4, 'Omar Saleh', 'Contemporary abstract artist.', 50, 1991, NULL, 'community', 3, '2026-01-06 01:41:21'),
(5, 'Sofia Karam', 'Visual storyteller focusing on surreal art.', 84, 1993, NULL, 'historical', 4, '2026-01-06 01:31:21'),
(14, 'Carl Marcos', 'Passionate about painting.', 84, 1993, NULL, 'historical', NULL, '2026-01-06 00:01:21'),
(25, 'ROMA', 'THIS IS THE BIOGRAPHY OF THE PERSON WHO\'S BUILDING THIS PROJECT.', 30, 2003, NULL, 'community', NULL, '2026-01-07 04:59:46'),
(31, 'NEW ARTIST FOR TESTING', 'THIS IS A TEST FOR ADDING A NEW ARTIST PROFILE FROM THE USER SIDE.', 5, 2010, NULL, 'community', 1, '2026-01-09 11:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `artist_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `year_created` int(11) DEFAULT NULL,
  `votes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`id`, `title`, `description`, `image_path`, `status`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `artist_id`, `category_id`, `year_created`, `votes`, `created_at`) VALUES
(1, 'Starry Night', 'A depiction of the view from Van Gogh\'s asylum room.', 'artworks/starry_night.jpg', 'Approved', NULL, NULL, NULL, 1, 3, 1889, 0, '2025-11-06 02:33:56'),
(2, 'The Two Fridas', 'Dual self-portrait representing Frida\'s identity.', 'artworks/the_two_fridas.jpg', 'Pending', NULL, NULL, NULL, 2, 2, 1939, 0, '2025-11-06 02:33:56'),
(4, 'Echoes of Time', 'Mixed media abstract piece.', 'artworks/echoes_of_time.jpg', 'Rejected', NULL, NULL, NULL, 4, 1, 2025, 5, '2025-11-06 02:33:56'),
(6, 'Sunflowers', 'Part of a series of still-life paintings of sunflowers in a vase.', 'artworks/sunflowers.jpg', 'Approved', NULL, NULL, NULL, 1, 3, 1888, 0, '2025-11-13 02:49:06'),
(7, 'The Bedroom', 'Van Gogh’s depiction of his bedroom in Arles.', 'artworks/the_bedroom.jpg', 'Approved', NULL, NULL, NULL, 1, 3, 1888, 0, '2025-11-13 02:49:06'),
(8, 'Irises', 'A vibrant painting of blooming irises created during his stay at the asylum in Saint-Rémy.', 'artworks/irises.jpg', 'Approved', NULL, NULL, NULL, 1, 3, 1889, 0, '2025-11-13 02:49:06'),
(9, 'Café Terrace at Night', 'Shows a brightly lit café terrace at night under a starlit sky.', 'artworks/cafe_terrace_at_night.jpg', 'Approved', NULL, NULL, NULL, 1, 2, 1888, 0, '2025-11-13 02:49:06'),
(21, 'Irise', 'When white meets darkness', 'artworks/6954fc0bba6587.74983093.jpg', 'Rejected', NULL, NULL, NULL, 14, 1, 2025, 0, '2025-12-26 11:39:50'),
(22, 'ArtNoir', 'This is where art and shadows intertwine.', 'artworks/695c4e0e18c852.97192082.jpg', 'Approved', NULL, NULL, NULL, 5, 4, 2025, 0, '2026-01-05 23:49:34'),
(23, 'Mavors', 'The power of black.', 'artworks/695c5182c92f94.20876845.png', 'Approved', NULL, NULL, NULL, 5, 4, 2024, 0, '2026-01-06 00:04:18'),
(24, 'ZZH', 'Design | Develop | Deliver', 'artworks/695c52be862204.72671820.png', 'Pending', NULL, NULL, NULL, 5, 4, 2025, 0, '2026-01-06 00:06:35'),
(36, 'TESTING', 'THIS IS A TEST FOR ADDING A NEW ARTWORK FROM THE USER SIDE.', 'artworks/695de8703ac519.13293816.png', 'Pending', NULL, NULL, NULL, 25, 3, 2026, 0, '2026-01-07 05:00:32');

--
-- Triggers `artworks`
--
DELIMITER $$
CREATE TRIGGER `trg_artwork_pending_insert` AFTER INSERT ON `artworks` FOR EACH ROW BEGIN
    IF NEW.status = 'Pending' THEN
        INSERT INTO notifications (
            type,
            title,
            message,
            related_table,
            related_id
        )
        VALUES (
            'artwork',
            'New artwork pending approval',
            CONCAT('Artwork "', NEW.title, '" is pending approval.'),
            'artworks',
            NEW.id
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_artwork_pending_update` AFTER UPDATE ON `artworks` FOR EACH ROW BEGIN
    IF OLD.status <> 'Pending' AND NEW.status = 'Pending' THEN
        INSERT INTO notifications (
            type,
            title,
            message,
            related_table,
            related_id
        )
        VALUES (
            'artwork',
            'Artwork pending approval',
            CONCAT('Artwork "', NEW.title, '" was set to pending.'),
            'artworks',
            NEW.id
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Abstract'),
(4, 'Digital Art'),
(3, 'Landscape'),
(2, 'Portrait');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `code`) VALUES
(1, 'Afghanistan', 'AFG'),
(2, 'Albania', 'ALB'),
(3, 'Algeria', 'DZA'),
(4, 'Andorra', 'AND'),
(5, 'Angola', 'AGO'),
(6, 'Antigua and Barbuda', 'ATG'),
(7, 'Argentina', 'ARG'),
(8, 'Armenia', 'ARM'),
(9, 'Australia', 'AUS'),
(10, 'Austria', 'AUT'),
(11, 'Azerbaijan', 'AZE'),
(12, 'Bahamas', 'BHS'),
(13, 'Bahrain', 'BHR'),
(14, 'Bangladesh', 'BGD'),
(15, 'Barbados', 'BRB'),
(16, 'Belarus', 'BLR'),
(17, 'Belgium', 'BEL'),
(18, 'Belize', 'BLZ'),
(19, 'Benin', 'BEN'),
(20, 'Bhutan', 'BTN'),
(21, 'Bolivia', 'BOL'),
(22, 'Bosnia and Herzegovina', 'BIH'),
(23, 'Botswana', 'BWA'),
(24, 'Brazil', 'BRA'),
(25, 'Brunei', 'BRN'),
(26, 'Bulgaria', 'BGR'),
(27, 'Burkina Faso', 'BFA'),
(28, 'Burundi', 'BDI'),
(29, 'Cambodia', 'KHM'),
(30, 'Cameroon', 'CMR'),
(31, 'Canada', 'CAN'),
(32, 'Cape Verde', 'CPV'),
(33, 'Central African Republic', 'CAF'),
(34, 'Chad', 'TCD'),
(35, 'Chile', 'CHL'),
(36, 'China', 'CHN'),
(37, 'Colombia', 'COL'),
(38, 'Comoros', 'COM'),
(39, 'Congo', 'COG'),
(40, 'Costa Rica', 'CRI'),
(41, 'Croatia', 'HRV'),
(42, 'Cuba', 'CUB'),
(43, 'Cyprus', 'CYP'),
(44, 'Czech Republic', 'CZE'),
(45, 'Denmark', 'DNK'),
(46, 'Djibouti', 'DJI'),
(47, 'Dominica', 'DMA'),
(48, 'Dominican Republic', 'DOM'),
(49, 'Ecuador', 'ECU'),
(50, 'Egypt', 'EGY'),
(51, 'El Salvador', 'SLV'),
(52, 'Equatorial Guinea', 'GNQ'),
(53, 'Eritrea', 'ERI'),
(54, 'Estonia', 'EST'),
(55, 'Eswatini', 'SWZ'),
(56, 'Ethiopia', 'ETH'),
(57, 'Fiji', 'FJI'),
(58, 'Finland', 'FIN'),
(59, 'France', 'FRA'),
(60, 'Gabon', 'GAB'),
(61, 'Gambia', 'GMB'),
(62, 'Georgia', 'GEO'),
(63, 'Germany', 'DEU'),
(64, 'Ghana', 'GHA'),
(65, 'Greece', 'GRC'),
(66, 'Grenada', 'GRD'),
(67, 'Guatemala', 'GTM'),
(68, 'Guinea', 'GIN'),
(69, 'Guinea-Bissau', 'GNB'),
(70, 'Guyana', 'GUY'),
(71, 'Haiti', 'HTI'),
(72, 'Honduras', 'HND'),
(73, 'Hungary', 'HUN'),
(74, 'Iceland', 'ISL'),
(75, 'India', 'IND'),
(76, 'Indonesia', 'IDN'),
(77, 'Iran', 'IRN'),
(78, 'Iraq', 'IRQ'),
(79, 'Ireland', 'IRL'),
(80, 'Israel', 'ISR'),
(81, 'Italy', 'ITA'),
(82, 'Jamaica', 'JAM'),
(83, 'Japan', 'JPN'),
(84, 'Jordan', 'JOR'),
(85, 'Kazakhstan', 'KAZ'),
(86, 'Kenya', 'KEN'),
(87, 'Kiribati', 'KIR'),
(88, 'Kuwait', 'KWT'),
(89, 'Kyrgyzstan', 'KGZ'),
(90, 'Laos', 'LAO'),
(91, 'Latvia', 'LVA'),
(92, 'Lebanon', 'LBN'),
(93, 'Lesotho', 'LSO'),
(94, 'Liberia', 'LBR'),
(95, 'Libya', 'LBY'),
(96, 'Liechtenstein', 'LIE'),
(97, 'Lithuania', 'LTU'),
(98, 'Luxembourg', 'LUX'),
(99, 'Madagascar', 'MDG'),
(100, 'Malawi', 'MWI'),
(101, 'Malaysia', 'MYS'),
(102, 'Maldives', 'MDV'),
(103, 'Mali', 'MLI'),
(104, 'Malta', 'MLT'),
(105, 'Marshall Islands', 'MHL'),
(106, 'Mauritania', 'MRT'),
(107, 'Mauritius', 'MUS'),
(108, 'Mexico', 'MEX'),
(109, 'Micronesia', 'FSM'),
(110, 'Moldova', 'MDA'),
(111, 'Monaco', 'MCO'),
(112, 'Mongolia', 'MNG'),
(113, 'Montenegro', 'MNE'),
(114, 'Morocco', 'MAR'),
(115, 'Mozambique', 'MOZ'),
(116, 'Myanmar', 'MMR'),
(117, 'Namibia', 'NAM'),
(118, 'Nauru', 'NRU'),
(119, 'Nepal', 'NPL'),
(120, 'Netherlands', 'NLD'),
(121, 'New Zealand', 'NZL'),
(122, 'Nicaragua', 'NIC'),
(123, 'Niger', 'NER'),
(124, 'Nigeria', 'NGA'),
(125, 'North Korea', 'PRK'),
(126, 'North Macedonia', 'MKD'),
(127, 'Norway', 'NOR'),
(128, 'Oman', 'OMN'),
(129, 'Pakistan', 'PAK'),
(130, 'Palau', 'PLW'),
(131, 'Palestine', 'PSE'),
(132, 'Panama', 'PAN'),
(133, 'Papua New Guinea', 'PNG'),
(134, 'Paraguay', 'PRY'),
(135, 'Peru', 'PER'),
(136, 'Philippines', 'PHL'),
(137, 'Poland', 'POL'),
(138, 'Portugal', 'PRT'),
(139, 'Qatar', 'QAT'),
(140, 'Romania', 'ROU'),
(141, 'Russia', 'RUS'),
(142, 'Rwanda', 'RWA'),
(143, 'Saint Kitts and Nevis', 'KNA'),
(144, 'Saint Lucia', 'LCA'),
(145, 'Saint Vincent and the Grenadines', 'VCT'),
(146, 'Samoa', 'WSM'),
(147, 'San Marino', 'SMR'),
(148, 'Sao Tome and Principe', 'STP'),
(149, 'Saudi Arabia', 'SAU'),
(150, 'Senegal', 'SEN'),
(151, 'Serbia', 'SRB'),
(152, 'Seychelles', 'SYC'),
(153, 'Sierra Leone', 'SLE'),
(154, 'Singapore', 'SGP'),
(155, 'Slovakia', 'SVK'),
(156, 'Slovenia', 'SVN'),
(157, 'Solomon Islands', 'SLB'),
(158, 'Somalia', 'SOM'),
(159, 'South Africa', 'ZAF'),
(160, 'South Sudan', 'SSD'),
(161, 'Spain', 'ESP'),
(162, 'Sri Lanka', 'LKA'),
(163, 'Sudan', 'SDN'),
(164, 'Suriname', 'SUR'),
(165, 'Sweden', 'SWE'),
(166, 'Switzerland', 'CHE'),
(167, 'Syria', 'SYR'),
(168, 'Taiwan', 'TWN'),
(169, 'Tajikistan', 'TJK'),
(170, 'Tanzania', 'TZA'),
(171, 'Thailand', 'THA'),
(172, 'Timor-Leste', 'TLS'),
(173, 'Togo', 'TGO'),
(174, 'Tonga', 'TON'),
(175, 'Trinidad and Tobago', 'TTO'),
(176, 'Tunisia', 'TUN'),
(177, 'Turkey', 'TUR'),
(178, 'Turkmenistan', 'TKM'),
(179, 'Tuvalu', 'TUV'),
(180, 'Uganda', 'UGA'),
(181, 'Ukraine', 'UKR'),
(182, 'United Arab Emirates', 'ARE'),
(183, 'United Kingdom', 'GBR'),
(184, 'United States', 'USA'),
(185, 'Uruguay', 'URY'),
(186, 'Uzbekistan', 'UZB'),
(187, 'Vanuatu', 'VUT'),
(188, 'Vatican City', 'VAT'),
(189, 'Venezuela', 'VEN'),
(190, 'Vietnam', 'VNM'),
(191, 'Yemen', 'YEM'),
(192, 'Zambia', 'ZMB'),
(193, 'Zimbabwe', 'ZWE');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_name`, `sender_email`, `subject`, `content`, `is_read`, `created_at`) VALUES
(4, 'Rola', 'rola@gmail.com', 'Where are my artworks!', 'Tell me where to find my artworks please.', 1, '2026-01-05 23:38:14'),
(5, 'Zahraa Zahraman', 'zahraazahraman@gmail.com', 'CONGRATS', 'I am writing this message to inform you that we are finally complete building this project for today submission.', 1, '2026-01-07 02:42:21'),
(6, 'Zahraa Zahraman', 'zahraazahraman@gmail.com', 'HOPEFULY CONGRATS', 'This is another message to tell you that perhaps now we are done.', 0, '2026-01-07 04:58:15'),
(7, 'THIS IS A TESTING FOR SENDING MESSAGES.', 'THIS IS A TESTING FOR SENDING MESSAGES.', 'THIS IS A TESTING FOR SENDING MESSAGES.', 'THIS IS A TESTING FOR SENDING MESSAGES.', 0, '2026-01-09 11:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_table` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `handled_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `title`, `message`, `related_table`, `related_id`, `is_read`, `handled_by`, `created_at`) VALUES
(1, 'artwork', 'Artwork pending approval', 'Artwork \"Starry Night\" is pending approval.', 'artworks', 1, 1, NULL, '2025-12-31 11:22:02'),
(2, 'artwork', 'Artwork pending approval', 'Artwork \"The Two Fridas\" is pending approval.', 'artworks', 2, 1, NULL, '2025-12-31 11:22:02'),
(3, 'artwork', 'Artwork pending approval', 'Artwork \"Echoes of Time\" is pending approval.', 'artworks', 4, 1, NULL, '2025-12-31 11:22:02'),
(4, 'artwork', 'Artwork pending approval', 'Artwork \"Sunflowers\" is pending approval.', 'artworks', 6, 1, NULL, '2025-12-31 11:22:02'),
(5, 'artwork', 'Artwork pending approval', 'Artwork \"The Bedroom\" is pending approval.', 'artworks', 7, 1, NULL, '2025-12-31 11:22:02'),
(6, 'artwork', 'Artwork pending approval', 'Artwork \"Irises\" is pending approval.', 'artworks', 8, 1, NULL, '2025-12-31 11:22:02'),
(7, 'artwork', 'Artwork pending approval', 'Artwork \"Café Terrace at Night\" is pending approval.', 'artworks', 9, 1, NULL, '2025-12-31 11:22:02'),
(8, 'artwork', 'Artwork pending approval', 'Artwork \"Irise\" is pending approval.', 'artworks', 21, 1, NULL, '2025-12-31 11:22:02'),
(16, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Starry Night\' has been approved.', 'artworks', 1, 1, NULL, '2025-12-31 11:43:31'),
(17, 'artwork_approved', 'Artwork Approved', 'Your artwork \'The Two Fridas\' has been approved.', 'artworks', 2, 1, NULL, '2025-12-31 11:48:19'),
(18, 'artwork_rejected', 'Artwork Rejected', 'Your artwork \'Irise\' has been rejected.', 'artworks', 21, 1, NULL, '2025-12-31 11:51:44'),
(19, 'artwork_rejected', 'Artwork Rejected', 'Your artwork \'Echoes of Time\' has been rejected.', 'artworks', 4, 1, NULL, '2025-12-31 12:10:43'),
(20, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Sunflowers\' has been approved.', 'artworks', 6, 1, NULL, '2026-01-02 02:54:06'),
(21, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Irises\' has been approved.', 'artworks', 8, 1, NULL, '2026-01-02 02:54:30'),
(22, 'artwork_approved', 'Artwork Approved', 'Your artwork \'The Bedroom\' has been approved.', 'artworks', 7, 1, NULL, '2026-01-02 02:54:58'),
(23, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Café Terrace at Night\' has been approved.', 'artworks', 9, 1, NULL, '2026-01-02 02:55:21'),
(24, 'artwork', 'Artwork pending approval', 'Artwork \"Irises\" was set to pending.', 'artworks', 8, 1, NULL, '2026-01-05 07:06:25'),
(25, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Irises\' has been approved.', 'artworks', 8, 1, NULL, '2026-01-05 07:32:44'),
(26, 'artwork', 'Artwork pending approval', 'Artwork \"The Bedroom\" was set to pending.', 'artworks', 7, 1, NULL, '2026-01-05 15:04:37'),
(27, 'artwork_approved', 'Artwork Approved', 'Your artwork \'The Bedroom\' has been approved.', 'artworks', 7, 1, NULL, '2026-01-05 15:30:15'),
(28, 'artwork', 'New artwork pending approval', 'Artwork \"ArtNoir\" is pending approval.', 'artworks', 22, 1, NULL, '2026-01-05 23:49:34'),
(29, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'ArtNoir\' has been submitted and is pending approval.', 'artworks', 22, 1, NULL, '2026-01-05 23:49:34'),
(30, 'artwork', 'New artwork pending approval', 'Artwork \"new artwork\" is pending approval.', 'artworks', 23, 1, NULL, '2026-01-06 00:04:18'),
(31, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'new artwork\' has been submitted and is pending approval.', 'artworks', 23, 1, NULL, '2026-01-06 00:04:18'),
(32, 'artwork', 'New artwork pending approval', 'Artwork \"another new artwork\" is pending approval.', 'artworks', 24, 1, NULL, '2026-01-06 00:06:35'),
(33, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'another new artwork\' has been submitted and is pending approval.', 'artworks', 24, 1, NULL, '2026-01-06 00:06:35'),
(34, 'artwork_approved', 'Artwork Approved', 'Your artwork \'ArtNoir\' has been approved.', 'artworks', 22, 0, NULL, '2026-01-06 00:32:30'),
(35, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Mavors\' has been approved.', 'artworks', 23, 1, NULL, '2026-01-06 00:34:19'),
(36, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Echoes of Time\' has been approved.', 'artworks', 4, 0, NULL, '2026-01-06 00:42:08'),
(37, 'artwork_rejected', 'Artwork Rejected', 'Your artwork \'Echoes of Time\' has been rejected.', 'artworks', 4, 0, NULL, '2026-01-06 00:45:39'),
(38, 'artwork', 'Artwork pending approval', 'Artwork \"Starry Night\" was set to pending.', 'artworks', 1, 1, NULL, '2026-01-06 04:06:23'),
(39, 'artwork_approved', 'Artwork Approved', 'Your artwork \'Starry Night\' has been approved.', 'artworks', 1, 0, NULL, '2026-01-06 04:17:53'),
(40, 'artwork', 'Artwork pending approval', 'Artwork \"Echoes of Time\" was set to pending.', 'artworks', 4, 1, NULL, '2026-01-06 04:57:34'),
(41, 'artwork_rejected', 'Artwork Rejected', 'Your artwork \'Echoes of Time\' has been rejected.', 'artworks', 4, 1, NULL, '2026-01-06 04:58:06'),
(42, 'artwork', 'Artwork pending approval', 'Artwork \"The Two Fridas\" was set to pending.', 'artworks', 2, 1, NULL, '2026-01-06 05:13:08'),
(43, 'artwork', 'New artwork pending approval', 'Artwork \"ER Diagram\" is pending approval.', 'artworks', 25, 0, NULL, '2026-01-06 13:16:45'),
(44, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'ER Diagram\' has been submitted and is pending approval.', 'artworks', 25, 1, NULL, '2026-01-06 13:16:45'),
(45, 'artwork', 'New artwork pending approval', 'Artwork \"the er diagram\" is pending approval.', 'artworks', 26, 0, NULL, '2026-01-06 13:25:59'),
(46, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'the er diagram\' has been submitted and is pending approval.', 'artworks', 26, 0, NULL, '2026-01-06 13:25:59'),
(47, 'artwork', 'New artwork pending approval', 'Artwork \"Screenshort\" is pending approval.', 'artworks', 27, 0, NULL, '2026-01-06 13:29:36'),
(48, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'Screenshort\' has been submitted and is pending approval.', 'artworks', 27, 0, NULL, '2026-01-06 13:29:36'),
(49, 'artwork', 'New artwork pending approval', 'Artwork \"another\" is pending approval.', 'artworks', 28, 0, NULL, '2026-01-06 13:34:15'),
(50, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'another\' has been submitted and is pending approval.', 'artworks', 28, 0, NULL, '2026-01-06 13:34:15'),
(51, 'artwork', 'New artwork pending approval', 'Artwork \"another\" is pending approval.', 'artworks', 29, 0, NULL, '2026-01-06 13:34:15'),
(52, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'another\' has been submitted and is pending approval.', 'artworks', 29, 0, NULL, '2026-01-06 13:34:15'),
(53, 'artwork', 'New artwork pending approval', 'Artwork \"another\" is pending approval.', 'artworks', 30, 0, NULL, '2026-01-06 13:34:15'),
(54, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'another\' has been submitted and is pending approval.', 'artworks', 30, 0, NULL, '2026-01-06 13:34:15'),
(55, 'artwork', 'New artwork pending approval', 'Artwork \"one after another\" is pending approval.', 'artworks', 31, 0, NULL, '2026-01-06 13:40:50'),
(56, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'one after another\' has been submitted and is pending approval.', 'artworks', 31, 0, NULL, '2026-01-06 13:40:50'),
(57, 'artwork', 'New artwork pending approval', 'Artwork \"Irise\" is pending approval.', 'artworks', 32, 1, NULL, '2026-01-06 13:54:02'),
(58, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'Irise\' has been submitted and is pending approval.', 'artworks', 32, 0, NULL, '2026-01-06 13:54:02'),
(59, 'artwork', 'New artwork pending approval', 'Artwork \"PROGRESS\" is pending approval.', 'artworks', 33, 0, NULL, '2026-01-06 23:42:12'),
(60, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'PROGRESS\' has been submitted and is pending approval.', 'artworks', 33, 0, NULL, '2026-01-06 23:42:12'),
(61, 'artwork', 'New artwork pending approval', 'Artwork \"debugging\" is pending approval.', 'artworks', 34, 1, NULL, '2026-01-07 01:32:36'),
(62, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'debugging\' has been submitted and is pending approval.', 'artworks', 34, 0, NULL, '2026-01-07 01:32:36'),
(63, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'debugging\' has been submitted and is pending approval.', 'artworks', 35, 1, NULL, '2026-01-07 04:39:56'),
(64, 'artwork', 'New artwork pending approval', 'Artwork \"debugging\" is pending approval.', 'artworks', 36, 1, NULL, '2026-01-07 05:00:32'),
(65, 'artwork_pending', 'New Artwork Pending Review', 'New artwork \'debugging\' has been submitted and is pending approval.', 'artworks', 36, 1, NULL, '2026-01-07 05:00:32');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `monthly_limit` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `monthly_limit`, `price`) VALUES
(1, 'Canvas', 3, 0.00),
(2, 'Studio', 10, 9.99),
(3, 'Gallery', 30, 24.99);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('User','Admin') DEFAULT 'User',
  `state` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `state`, `created_at`) VALUES
(1, 'Zahraa Zahraman', 'admin@artnoir.com', '$2y$10$WTPBumP2TPnwy1h3sxmSO.zLN.Tic0giUvC1f8gbnl7Ki4tMuKJhS', 'Admin', 'Active', '2025-11-06 02:26:35'),
(2, 'Layla Hassan', 'layla@example.com', '$2y$10$cI0haXKlgk2m4wY.ytcdveJTKN.XMoDueOJ/LqOnhUScWuJLvBCYG', 'User', 'Active', '2025-11-06 02:26:35'),
(3, 'Omar Saleh', 'omar@example.com', '$2y$10$RcH/Q/xASWBuBY7va0Bwy.RDX14VBm6EEXSN9DFIXE3YRmXNvIkIW', 'User', 'Active', '2025-11-06 02:26:35'),
(4, 'Sofia Karam', 'sofia@example.com', '$2y$10$orIDjnufaF33PLOgpmIjlO9j9OqAT7Ka08FdGMXi8NjMyczeYnrCW', 'User', 'Active', '2025-11-06 02:26:35'),
(40, 'Fares Haydar', 'haydar@gmail.com', '$2y$10$v746Zh0IAslW7Ze05KFfK.EbQzC5RvUkEmyvH.UWwwykUzZU/8mHe', 'User', 'Inactive', '2025-11-20 01:29:31'),
(53, 'Kamal Salah', 'kamal.salah@email.com', '$2y$10$yOPUhnxBBITUSwhGXNHQ../Qwhdzskq4qQ9rqtT.mQLbUAmCHXEQ.', 'User', 'Active', '2025-12-09 02:43:35'),
(61, 'Adele Laurie Blue Adkins', 'adele@gmail.com', '$2y$10$9QhJqkHklPjYicvbtM98he2PYl3eg.zc0/8HdoSldNb06qeEOgUGq', 'User', 'Active', '2026-01-05 07:43:32'),
(67, 'new user', 'newemail@gmail.com', '$2y$10$tPzV5MOjPpRdPqCqxC1ZiO2ipR0LDVbMoNB.inIUo3UgcsluZODRW', 'User', 'Active', '2026-01-07 00:29:05'),
(68, 'another', 'newemail2@gmail.com', '$2y$10$pi/9MhCSJo8q0yaZd/WYa.QJYPNZWZXH.BWXIrb9RSZK9bALCurAq', 'User', 'Active', '2026-01-07 00:48:37'),
(69, 'new', 'newuser@gmail.com', '$2y$10$HQdjCfwtC80ScB.wnc8Zf.JlIroeOsBNM4fuZcI9BTA8dusoUqQwK', 'Admin', 'Inactive', '2026-01-07 01:36:43'),
(71, 'newest', 'newest@gmail.com', '$2y$10$C8qFpuu5NusWPcYXKk5gcelJzkhRCJlJ9UeVAvnS4CqwXEcBLjDaq', 'User', 'Active', '2026-01-07 02:46:17'),
(72, 'user', 'user@gmail.com', '$2y$10$.GEybcUfm9Q8DDNoR0yHferjkdBi/NcHol8.NW.OK/GzhBAQB.cuK', 'User', 'Active', '2026-01-07 03:03:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_plans`
--

CREATE TABLE `user_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` date DEFAULT curdate(),
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_plans`
--

INSERT INTO `user_plans` (`id`, `user_id`, `plan_id`, `start_date`, `end_date`) VALUES
(1, 2, 1, '2025-11-01', NULL),
(2, 3, 2, '2025-11-01', '2025-12-01'),
(3, 4, 3, '2025-11-01', '2025-11-01'),
(4, 3, 2, '2025-12-02', '2026-01-02');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `voter_id`, `artwork_id`, `points`, `created_at`) VALUES
(3, 4, 4, 1, '2025-11-06 02:35:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_artist_country` (`country_id`);

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD UNIQUE KEY `unique_code` (`code`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_plans`
--
ALTER TABLE `user_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voter_id` (`voter_id`,`artwork_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `user_plans`
--
ALTER TABLE `user_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artists`
--
ALTER TABLE `artists`
  ADD CONSTRAINT `artists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_artist_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`),
  ADD CONSTRAINT `artworks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `user_plans`
--
ALTER TABLE `user_plans`
  ADD CONSTRAINT `user_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_plans_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`voter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
