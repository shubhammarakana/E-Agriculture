-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 12:09 PM
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
-- Database: `e_agriculture_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) UNSIGNED DEFAULT NULL,
  `guest_token_id` varchar(64) DEFAULT NULL,
  `crop_id` int(6) UNSIGNED DEFAULT NULL,
  `chemical_id` int(11) DEFAULT NULL,
  `seed_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `guest_token_id`, `crop_id`, `chemical_id`, `seed_id`, `quantity`, `created_at`) VALUES
(58, NULL, 'TEST_TOKEN_e9ea69b3bf', NULL, 999, NULL, 2.00, '2026-01-17 08:18:02'),
(75, 124, NULL, NULL, 24, NULL, 1.00, '2026-01-19 04:49:45'),
(77, NULL, 'ec5a1ee8ff0ac120824c8a84ef79367b60b750cea6091b0dfd60d510de09ca78', 44, NULL, NULL, 1.00, '2026-01-29 13:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `chemicals`
--

CREATE TABLE `chemicals` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active_ingredient` varchar(255) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `pack_size` varchar(50) DEFAULT NULL,
  `crop_suitability` text DEFAULT NULL,
  `safety_label` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chemicals`
--

INSERT INTO `chemicals` (`id`, `seller_id`, `name`, `description`, `category`, `price`, `stock`, `image_path`, `created_at`, `active_ingredient`, `brand`, `pack_size`, `crop_suitability`, `safety_label`, `status`) VALUES
(10, 21, 'almona', 'azcadscd  scvdavc dz dsvw vcx vw vs cvsw vW VSC VW V VCW VSD CDW F', 'Pesticide', 1200.00, 4, 'images/chem_6968f035d1e85.png', '2026-01-15 13:48:37', 'nitrogen 60%, phospet 20%, daiemoinioum 20%', 'iquiade', '2L', 'cotton, magafadi', 'Blue', 'Active'),
(11, 21, 'Urea', 'jqijfduenfndfu3rwf  curewhf  fvurfr3  ds vour3fnr fdsvurefr  evreihbhr n fvreurn fdvueern revreunmr nvefjev', 'Fertilizer', 450.00, 110, 'images/chem_6969b6b7e9890.jpg', '2026-01-16 03:55:35', 'Glayposfet 100%', 'FNC0', '50kg', 'cotton, magafadi etc...', 'Blue', 'Active'),
(12, 21, 'Basalin', 'jiqewjfe d coirefer   vourewf rewwcourewnfrw fdcwrefrw m', 'Nutrient', 350.00, 26, 'images/chem_6969b7104b079.jpg', '2026-01-16 03:57:04', '-----------', 'Blue barry', '5L', 'All over crop', 'Green', 'Active'),
(13, 21, 'Boscalid', 'sacfew  frw fer sd c wref wed crew f  dvc ds rev fdv ds dw f', 'Fungicide', 850.00, 40, 'images/chem_6969b7dbbf1e8.png', '2026-01-16 04:00:27', 'Boron 20%,  naitrojen 80%', 'Endura (BASF)', '10L', 'Coton', 'Green', 'Active'),
(14, 21, 'TSP (Triple Super Phosphate)', 'erdfre fve rf re fv req  d v ds cs csd  a sdssdfdsferte s cwfrewdssdfwrefr f sdewf', 'Fertilizer', 445.00, 36, 'images/chem_6969b86abbe95.png', '2026-01-16 04:02:50', 'phosphate 100%', 'Phosphorus supply', '10L', 'All Vagetables', 'Green', 'Active'),
(15, 21, 'NPK Fertilizers', 'Early growth, root strength', 'Fertilizer', 1200.00, 64, 'images/chem_6969b90d73d77.jpg', '2026-01-16 04:05:33', 'Nitrogen + Phosphorus (N + P)', 'N, P, K 3-in-1 mix', '20KG', 'All Over crops', 'Green', 'Active'),
(16, 21, 'Wettable Sulphur / Sulfex', 'ajsdhewjk w fiurwefr  fuciwef ds cewufw  cihrewfnm wncjhewhf rewf', 'Herbicide', 350.00, 44, 'images/chem_6969b97c1e288.png', '2026-01-16 04:07:24', 'Sulphur', 'Fungicide/miticide', '10KG', 'Vagetables', 'Green', 'Active'),
(17, 21, 'Vitavax', 'Seed treatment against fungal disease', 'Fungicide', 500.00, 66, 'images/chem_6969ba0374bad.png', '2026-01-16 04:09:39', 'Carboxin', 'Carboxin', '5LTR', 'Mandavi (magafadi), Jira', 'Yellow', 'Active'),
(18, 21, 'Endura / Emerald', 'Foliar disease control', 'Nutrient', 320.00, 26, 'images/chem_6969ba6e3defb.jpg', '2026-01-16 04:11:26', 'SDHI fungicide', 'Boscalid', '2L', 'Coton, Tal, jira', 'Green', 'Active'),
(19, 21, 'Topsin / Roko / Moti', 'Broad spectrum', 'Pesticide', 750.00, 64, 'images/chem_6969babe6ff5a.png', '2026-01-16 04:12:46', 'Systemic fungicide', 'Thiophanate-methyl', '10L', 'Dhana, Chana', 'Red', 'Active'),
(20, 21, 'Abamectin', 'acf rw wrf t3 ds cds fte a c de rev dsrresresstrtr vds frthtrsgrse  reteg  vdsgtrtrt', 'Pesticide', 450.00, 27, 'images/chem_6969bb4cac2e4.jpg', '2026-01-16 04:15:08', 'Boron 10%', 'Vertimec', '10L', 'Tuver', 'Green', 'Active'),
(21, 21, 'Avatar', 'jwehfoiwrjf r fjrewf w clkjwenf  dwcjwrnf rwdw cvljrwnfrwf  dsclkjewfrew few fjewnwrefjrewfjrewrewfrewj f', 'Nutrient', 880.00, 90, 'images/chem_6969bbe7a405e.jpg', '2026-01-16 04:17:43', 'Uren 100%', 'Glyphosate', '10L', 'Ajamo', 'Green', 'Active'),
(23, 103, 'profenosyper', 'acdsvfvfv  f dwv ds cds cw v fsv dsvdsv ', 'Herbicide', 750.00, 34, 'images/chem_696b30ff6f4a3.jpg', '2026-01-17 06:49:35', 'naitroger 80%, phospet 20%', 'iiffo', '10L', 'coton', 'Blue', 'Active'),
(24, 103, 'Monocotto', 'adsfadf ds fvdsvsd  dsvf svdsvwsvsvs sdcds scsdwvdsvcdsv dsvdss ssdv svdsavdsafv fwscv savdsavfdvsdvsver d sfre hte  f AFEWTRWDSFdsaFRWGssdsafeffdfrwa reagresdrarragreasd garewfwfarwgrwfagread wfrewafdsfrewafra ', 'Pesticide', 880.00, 34, 'images/chem_696b798f3cfa4.png', '2026-01-17 11:59:11', 'Nitrogen + Phosphorus (N + P)', 'Atzat', '5L', 'Coton', 'Red', 'Active'),
(25, 119, 'salfar', 'ajskcdfcw df rwfe vfe', 'Pesticide', 880.00, 4, 'images/chem_696bb06d6d356.jpg', '2026-01-17 15:53:17', 'salfar 100%', 'iscro', '10L', 'all over crops', 'Green', 'Active'),
(26, 119, 'DAP', 'jkdsFw  wfrewfwdc ds cWFWECDS FF RWEG5 H DA TR J6U  EF dsaf W WA Fw wqWFwgrwafdreadsvSDfEWDSAcDSAteayrjku  vreadvDSAv yteasvDS jytFDSVDStr svfdsWRGA D FREADSWEGA Fsdvtrhyt ACRWYUREHTRA FESEVSDVATE dsVDVDSVDSV SDVDVAVsdVSDVADF VsdVsdDFSsdVsd V', 'Fertilizer', 2200.00, 275, 'images/chem_696c5832377b4.png', '2026-01-18 03:49:06', 'daiamonium 75%, phosphate 25%', 'Vraj', '50KG', 'Coton(kapas), Mandavi(magfadi)', 'Blue', 'Active'),
(27, 119, 'Urea', 'asdsac d cdsv s xacdsc dsafwfwefdwr xzcsdsccsardsc sDCSd', 'Fertilizer', 750.00, 133, 'images/chem_696c58b36af36.jpg', '2026-01-18 03:51:15', 'uren 100%', 'sentostic', '50KG', 'all over crops', 'Red', 'Active'),
(28, 128, 'Basalin', 'sadFIJEWF DSVREQ  AX A SVEWA VA', 'Pesticide', 350.00, 19, 'images/chem_696dba4f41a83.png', '2026-01-19 04:59:59', 'R 30 N 70', 'biocon', '1L', 'all over crops', 'Green', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `chemical_cart`
--

CREATE TABLE `chemical_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chemical_cart`
--

INSERT INTO `chemical_cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(3, 12, 7, 1, '2026-01-12 04:42:33');

-- --------------------------------------------------------

--
-- Table structure for table `chemical_orders`
--

CREATE TABLE `chemical_orders` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed') DEFAULT 'Pending',
  `delivery_status` enum('Processing','Shipped','Delivered') DEFAULT 'Processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chemical_orders`
--

INSERT INTO `chemical_orders` (`id`, `buyer_id`, `seller_id`, `total_amount`, `payment_status`, `delivery_status`, `created_at`) VALUES
(1, 12, 13, 500.00, 'Pending', 'Delivered', '2026-01-09 06:14:47'),
(2, 12, 10, 500.00, 'Pending', 'Processing', '2026-01-09 06:14:47'),
(3, 12, 13, 570.00, 'Pending', 'Delivered', '2026-01-10 10:55:44'),
(4, 12, 13, 250.00, 'Pending', 'Delivered', '2026-01-10 10:59:40'),
(5, 12, 13, 570.00, 'Pending', 'Delivered', '2026-01-10 11:37:43'),
(6, 12, 10, 500.00, 'Pending', 'Processing', '2026-01-10 11:38:27'),
(7, 12, 13, 250.00, 'Pending', 'Delivered', '2026-01-10 11:38:48'),
(8, 12, 13, 1800.00, 'Pending', 'Delivered', '2026-01-10 13:12:30'),
(9, 12, 13, 570.00, 'Pending', 'Delivered', '2026-01-11 16:47:17'),
(10, 12, 13, 250.00, 'Pending', 'Delivered', '2026-01-12 05:18:27'),
(11, 12, 13, 250.00, 'Pending', 'Delivered', '2026-01-12 05:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `chemical_order_items`
--

CREATE TABLE `chemical_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chemical_order_items`
--

INSERT INTO `chemical_order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 5, 2, 250.00),
(2, 2, 1, 1, 500.00),
(3, 3, 6, 1, 570.00),
(4, 4, 5, 1, 250.00),
(5, 5, 6, 1, 570.00),
(6, 6, 1, 1, 500.00),
(7, 7, 5, 1, 250.00),
(8, 8, 7, 1, 1800.00),
(9, 9, 6, 1, 570.00),
(10, 10, 5, 1, 250.00),
(11, 11, 5, 1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_unit` varchar(20) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `stock_status` enum('In Stock','Low Stock','Out of Stock') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `farmer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `name`, `category`, `description`, `price`, `price_unit`, `image_path`, `stock_status`, `created_at`, `farmer_id`) VALUES
(27, 'potato(bataka)', 'Vegetables', 'uia  jahfw djf tjkujdia  sdfiuw  sdiofre s cfwhfw  scskuw  scwfrw  s ciwjfw  flkwe  venls  sdjwflijsdls  sjfsus', 45.00, 'kg', 'images/crop_6969b4280b581.jpg', 'In Stock', '2026-01-16 03:44:40', 21),
(28, 'cabbage', 'Vegetables', 'joidsaa eiufde jcisauiu qeuwq fdscu0qeufpsaj csau', 15.00, 'kg', 'images/crop_6969b44109663.jpg', 'In Stock', '2026-01-16 03:45:05', 21),
(29, 'btinggle(ringan)', 'Vegetables', 'sid ewupiod oo oipoeflwe  fdoiwe  foiewkn ciy  fiuwhfw  cvt  oinm fwiu ciu w', 35.00, 'kg', 'images/crop_6969b4702b48e.jpg', 'In Stock', '2026-01-16 03:45:52', 21),
(30, 'karela', 'Vegetables', 'ou piufwef djoifwref sdvoirewjfrew  durw d vr fvkjfe uwf w fwjdnr vkjf f', 50.00, 'kg', 'images/crop_6969b49057f64.jpg', 'In Stock', '2026-01-16 03:46:24', 21),
(31, 'kakadi', 'Vegetables', 'jkoiuaduw s ucwf wcs jnwfieqnaschwb .c fueq dc ewufm wcdiueqjfc  ', 35.00, 'kg', 'images/crop_6969b4aca61bf.jpg', 'In Stock', '2026-01-16 03:46:52', 21),
(32, 'Garlik(lasan)', 'Vegetables', 'uahyfiuewf  sduchewufkje s cuowehufef', 10.00, 'kg', 'images/crop_6969b4ccc8973.jpg', 'In Stock', '2026-01-16 03:47:24', 21),
(33, 'Okra(Bhindi)', 'Vegetables', 'uij nm u  oiuoeQIFOIC IODEQ  CUH  ACUFOEq,mac ds cvkjd smd cjnww c ', 45.00, 'kg', 'images/crop_6969b4f1a1778.jpg', 'In Stock', '2026-01-16 03:48:01', 21),
(34, 'palak', 'Vegetables', 'joijfiorewfnd voijrwhrwm  vuowhrwnmd vnffru swiuriwr  x sjkw nmjsnw ndsbvijew', 15.00, 'kg', 'images/crop_6969b52750a7b.jpg', 'In Stock', '2026-01-16 03:48:55', 21),
(35, 'Muli', 'Vegetables', 'joiasfwe cwyef ew nmsbiwhf ds xc wi s ciusw vmnois  oij    sjhudiiv s vsiuv  vui cvyt sjhnm n 8yn ', 10.00, 'kg', 'images/crop_6969b54f6df42.jpg', 'In Stock', '2026-01-16 03:49:35', 21),
(36, 'Capsicum(simla mirch)', 'Vegetables', 'njoiufjweiuof vc;ouewrfj3wn dsvoiuw  vrwoufjw dsnrwufsd  dsvourw v dsvwuvn ds s ;jvhrvfrew v ds swrevwr  ssds fnrw ds dsjsvijvf ds dsjnsc cnwoiuf cdncwurnrw nrw vojnvrewjw re vjrewonforw v ;jwnfwkr  rrwojnff', 35.00, 'kg', 'images/crop_6969b5831cda5.jpg', 'In Stock', '2026-01-16 03:50:27', 21),
(37, 'Green onion', 'Vegetables', 'dsferw wrefrewf rew  rewg rewv ds wr3g fd v r3 fd vre gre fv dsc fwr sf', 15.00, 'kg', 'images/crop_6969b5a902db4.jpg', 'In Stock', '2026-01-16 03:51:05', 21),
(38, 'Apple', 'Fruits', 'djkfrewf  fdsviurenre vfdhe efvdeuregn fe vdfouhre dfvoure f vjren jbirenvfen', 120.00, 'kg', 'images/crop_6969b5c553b30.jpg', 'In Stock', '2026-01-16 03:51:33', 21),
(39, 'Banana', 'Fruits', 'doiuajfwe fdhuwnfw dcwiurefew fds cuwrefrwhfrfv fdfrwnf  vfd ivrwfrnm nrew frwfnrwdds v', 40.00, 'kg', 'images/crop_6969b5dceac29.jpg', 'In Stock', '2026-01-16 03:51:56', 21),
(40, 'watermallon(tarbuch)', 'Fruits', 'ifpoi3wjfewf d vourwefr  dsc;ourewf mwcdsunourewnf d vcjdnfurwf wf cijdwfhrw fwreoucd cdoucewo cnew crewnf nfd v fijvrev', 150.00, 'kg', 'images/crop_6969b60a593e3.jpg', 'In Stock', '2026-01-16 03:52:42', 21),
(41, 'apple', 'Fruits', 'uisfywfphudwfo;kjrewf rewrewufyurwprw', 150.00, 'kg', 'images/crop_696b295ab815b.jpg', 'In Stock', '2026-01-17 06:16:58', 1),
(42, 'sakariya', 'Vegetables', 'jdoief weufrn;lji[ \'oivjṁv fv.nej;nj ', 20.00, 'kg', 'images/crop_696b32c79d075.jpg', 'In Stock', '2026-01-17 06:57:11', 103),
(43, 'AAdu', 'Vegetables', ' sjffecsdefv  cdefdvs sgtefv vscsc  cfwrfrwefvfv  vevevrevfe vcewfre cv  verevfeaAEF VETVFDVEF AF', 10.00, 'kg', 'images/crop_696b753b3588c.jpg', 'In Stock', '2026-01-17 11:40:43', 103),
(44, 'Capsicum', 'Vegetables', 'sadcwdfrv jkmmoijm mjklml,i,m,m.,lok;l,ml,nouklm\'.m\'mko kkihuhjln jnkjn;jkikmpoij ijoiuimjpjj', 150.00, 'kg', 'images/crop_696b75661253a.jpg', 'In Stock', '2026-01-17 11:41:26', 103),
(45, 'Brokoly', 'Vegetables', 'safdwefevfd vdfvrgcv dfdvcacvdave  scvdsfv sxcdsvcfd vcds vdsa vda zxvdfvf vfdv d fd vfdv dfv', 70.00, 'kg', 'images/crop_696b758a8230c.jpg', 'In Stock', '2026-01-17 11:42:02', 103),
(46, 'Brinjal', 'Vegetables', 'sadwdfvfedcvfd hbd fdvfdvsdfr  dfvfdvddaadavfe dfvfdvdf vdfv dvd v df sdvdaf e fds  dv fdae  da vdaf v  d vd vdf v fdv g dsadc av f  f vads vda df  vfd', 15.00, 'kg', 'images/crop_696b75abc23e1.jpg', 'In Stock', '2026-01-17 11:42:35', 103),
(47, 'Cabbage', 'Vegetables', 'asdsd fds vfdav d vadsvad v da vdsa  dv d v dsacadsvdfv  sj  sd fra  ts f ae re  f are h tra re yj yt szd dgzdsgdsfd f s ss h', 12.00, 'kg', 'images/crop_696b75d14861f.jpg', 'In Stock', '2026-01-17 11:43:13', 103),
(48, 'graps', 'Fruits', 'jlsadkjsa dsacmlnjdWCNDwmc .dscj DNC JCNDS C;KNDbckjdWCDw c;kjdWKC SMD CNDWK N DSDS;KJC .SCOJDWDS C', 50.00, 'kg', 'images/crop_696b9fb7855ea.jpg', 'In Stock', '2026-01-17 14:41:59', 119);

-- --------------------------------------------------------

--
-- Table structure for table `crop_analytics`
--

CREATE TABLE `crop_analytics` (
  `id` int(6) UNSIGNED NOT NULL,
  `crop_name` varchar(100) NOT NULL,
  `region` varchar(100) DEFAULT NULL,
  `demand_score` int(3) DEFAULT NULL,
  `price_trend` enum('Up','Down','Stable') DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_tracking`
--

CREATE TABLE `delivery_tracking` (
  `id` int(6) UNSIGNED NOT NULL,
  `order_id` int(6) UNSIGNED NOT NULL,
  `status` enum('Pending','Accepted','Packed','Shipped','Delivered','Cancelled') NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'System',
  `location` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disease_reports`
--

CREATE TABLE `disease_reports` (
  `id` int(6) UNSIGNED NOT NULL,
  `farmer_id` int(6) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `severity` varchar(50) DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farmer_profiles`
--

CREATE TABLE `farmer_profiles` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) UNSIGNED NOT NULL,
  `bio` text DEFAULT NULL,
  `verification_status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `verification_doc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_crops`
--

CREATE TABLE `master_crops` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `local_name` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` enum('Cereal','Vegetable','Fruit','Pulse','Oilseed','Other') DEFAULT 'Other',
  `season` enum('Kharif','Rabi','Zaid','All-Season') DEFAULT 'All-Season',
  `growth_duration` int(5) DEFAULT NULL COMMENT 'In Days',
  `soil_type` varchar(100) DEFAULT NULL,
  `water_req` enum('Low','Medium','High') DEFAULT 'Medium',
  `fertilizer_rec` text DEFAULT NULL,
  `pests_diseases` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `is_visible_farmers` tinyint(1) DEFAULT 1,
  `is_visible_buyers` tinyint(1) DEFAULT 1,
  `is_market_listed` tinyint(1) DEFAULT 1,
  `is_seasonal_guide_listed` tinyint(1) DEFAULT 1,
  `is_ai_enabled` tinyint(1) DEFAULT 0,
  `ai_model_id` varchar(100) DEFAULT NULL,
  `ai_yield_formula` varchar(255) DEFAULT NULL,
  `is_ai_advisory_visible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `type` enum('order','payment','system','alert') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(6) UNSIGNED NOT NULL,
  `crop_id` int(6) UNSIGNED DEFAULT NULL,
  `farmer_id` int(6) UNSIGNED DEFAULT NULL,
  `buyer_id` int(6) UNSIGNED DEFAULT NULL,
  `buyer_name` varchar(100) DEFAULT NULL,
  `buyer_address` text DEFAULT NULL,
  `buyer_phone` varchar(20) DEFAULT NULL,
  `quantity` int(10) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Accepted','Packed','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `crop_id`, `farmer_id`, `buyer_id`, `buyer_name`, `buyer_address`, `buyer_phone`, `quantity`, `total_price`, `status`, `order_date`, `total_amount`, `created_at`) VALUES
(12, NULL, 107, 108, 'perin', 'jamangar', '7990079737', NULL, NULL, 'Delivered', '2026-01-17 03:57:40', 470.00, '2026-01-17 03:57:40'),
(13, NULL, 103, 116, 'sandip bhai', 'lalpurroad jamangar', '9909091465', NULL, NULL, 'Delivered', '2026-01-17 12:01:33', 858.50, '2026-01-17 12:01:33'),
(14, NULL, 103, 116, 'sandip bhai', 'lalpurroad jamangar', '9909091465', NULL, NULL, 'Delivered', '2026-01-17 12:03:30', 974.00, '2026-01-17 12:03:30'),
(15, NULL, 119, 122, 'rohit', 'lalpurroad jamangar', '7896541230', NULL, NULL, 'Delivered', '2026-01-17 17:18:41', 102.50, '2026-01-17 17:18:41'),
(16, NULL, 119, 116, 'sandip bhai', 'lalpurroad jamangar', '9909091465', NULL, NULL, 'Delivered', '2026-01-18 04:44:23', 5544.00, '2026-01-18 04:44:23'),
(17, NULL, 103, 116, 'sandip bhai', 'lalpurroad jamangar', '9909091465', NULL, NULL, 'Delivered', '2026-01-18 06:00:50', 60.50, '2026-01-18 06:00:50'),
(18, NULL, 119, 116, 'sandip bhai', 'lalpurroad jamangar', '9909091465', NULL, NULL, 'Delivered', '2026-01-18 06:34:01', 1281.00, '2026-01-18 06:34:01'),
(19, NULL, 119, 123, 'kevin r', 'lalpurroad jamangar', '9537266317', NULL, NULL, 'Delivered', '2026-01-18 06:44:00', 837.50, '2026-01-18 06:44:00'),
(20, NULL, 119, 123, 'kevin r', 'rampar,lalpurroad', '9537266317', NULL, NULL, 'Delivered', '2026-01-18 06:47:09', 1260.00, '2026-01-18 06:47:09'),
(21, NULL, 103, 103, 'darshan vora', 'lalpurroad jamangar', '7096503635', NULL, NULL, 'Delivered', '2026-01-19 05:09:09', 1197.00, '2026-01-19 05:09:09');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(6) UNSIGNED NOT NULL,
  `order_id` int(6) UNSIGNED NOT NULL,
  `crop_id` int(6) UNSIGNED DEFAULT NULL,
  `chemical_id` int(6) UNSIGNED DEFAULT NULL,
  `seed_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `crop_id`, `chemical_id`, `seed_id`, `quantity`, `price_per_unit`, `subtotal`) VALUES
(1, 1, 4, NULL, NULL, 1.00, 255.00, 255.00),
(2, 3, 4, NULL, NULL, 5.00, 255.00, 1275.00),
(3, 4, 6, NULL, NULL, 4.00, 400.00, 1600.00),
(4, 5, 5, NULL, NULL, 1.00, 200.00, 200.00),
(5, 6, 8, NULL, NULL, 2.00, 50.00, 100.00),
(6, 6, 10, NULL, NULL, 2.00, 20.00, 40.00),
(7, 6, 5, NULL, NULL, 1.00, 200.00, 200.00),
(8, 6, 9, NULL, NULL, 1.00, 500.00, 500.00),
(9, 7, 9, NULL, NULL, 1.00, 500.00, 500.00),
(10, 7, 18, NULL, NULL, 1.00, 72.00, 72.00),
(11, 8, 12, NULL, NULL, 1.00, 20.00, 20.00),
(12, 9, NULL, 9, NULL, 1.00, 750.00, 750.00),
(13, 10, 26, NULL, NULL, 1.00, 50.00, 50.00),
(14, 11, 26, NULL, NULL, 1.00, 50.00, 50.00),
(15, 12, NULL, 22, NULL, 1.00, 400.00, 400.00),
(16, 13, 42, NULL, NULL, 1.00, 20.00, 20.00),
(17, 13, NULL, 23, NULL, 1.00, 750.00, 750.00),
(18, 14, NULL, 24, NULL, 1.00, 880.00, 880.00),
(19, 15, 48, NULL, NULL, 1.00, 50.00, 50.00),
(20, 16, NULL, 25, NULL, 1.00, 880.00, 880.00),
(21, 16, NULL, 26, NULL, 2.00, 2200.00, 4400.00),
(22, 17, NULL, NULL, 2, 1.00, 10.00, 10.00),
(23, 18, NULL, NULL, 1, 1.00, 1200.00, 1200.00),
(24, 18, NULL, NULL, 2, 2.00, 10.00, 20.00),
(25, 19, NULL, 27, NULL, 1.00, 750.00, 750.00),
(26, 20, NULL, NULL, 1, 1.00, 1200.00, 1200.00),
(27, 21, 42, NULL, NULL, 2.00, 20.00, 40.00),
(28, 21, NULL, 23, NULL, 1.00, 750.00, 750.00),
(29, 21, NULL, 28, NULL, 1.00, 350.00, 350.00),
(30, 22, NULL, 24, NULL, 1.00, 880.00, 880.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(6) UNSIGNED NOT NULL,
  `order_id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `amount`, `payment_method`, `status`, `transaction_id`, `created_at`) VALUES
(11, 12, 108, 470.00, 'COD', 'Completed', 'TXN696b08b4b3ad7', '2026-01-17 03:57:40'),
(12, 13, 116, 858.50, 'COD', 'Completed', 'TXN696b7a1dc6845', '2026-01-17 12:01:33'),
(13, 14, 116, 974.00, 'COD', 'Completed', 'TXN696b7a920d3a2', '2026-01-17 12:03:30'),
(14, 15, 122, 102.50, 'COD', 'Completed', 'TXN696bc471746d6', '2026-01-17 17:18:41'),
(15, 16, 116, 5544.00, 'UPI', 'Completed', 'TXN696c6527e7835', '2026-01-18 04:44:23'),
(16, 17, 116, 60.50, 'NetBanking', 'Completed', 'TXN696c7712048a5', '2026-01-18 06:00:50'),
(17, 18, 116, 1281.00, 'COD', 'Completed', 'TXN696c7ed9682b8', '2026-01-18 06:34:01'),
(18, 19, 123, 837.50, 'NetBanking', 'Completed', 'TXN696c81301803c', '2026-01-18 06:44:00'),
(19, 20, 123, 1260.00, 'UPI', 'Completed', 'TXN696c81edb7da1', '2026-01-18 06:47:09'),
(20, 21, 103, 1197.00, 'COD', 'Completed', 'TXN696dbc751d6f6', '2026-01-19 05:09:09'),
(21, 22, 130, 974.00, 'COD', 'Completed', 'TXN696dc58e4dc52', '2026-01-19 05:47:58');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(6) UNSIGNED NOT NULL,
  `farmer_id` int(6) UNSIGNED NOT NULL,
  `buyer_id` int(6) UNSIGNED NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seeds`
--

CREATE TABLE `seeds` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL COMMENT 'Hybrid, Organic, Desi, Imported',
  `crop_type` varchar(100) NOT NULL COMMENT 'Wheat, Rice, Cotton, etc',
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL COMMENT 'Available Stock',
  `weight` varchar(50) NOT NULL COMMENT 'Pack Size e.g. 1kg',
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seeds`
--

INSERT INTO `seeds` (`id`, `seller_id`, `name`, `category`, `crop_type`, `price`, `quantity`, `weight`, `description`, `image_path`, `status`, `created_at`) VALUES
(1, 119, 'coton', 'Organic', 'coton', 1200.00, 21, '500G', 'lcpadsd v fdsav daf vda fv dsav ads vfe vdsa v eav fda vads sa', 'images/seed_696c5ba45cf2c.jpg', 'Active', '2026-01-18 04:03:48'),
(2, 103, 'Tomato', 'Desi (Native)', 'Tomato', 10.00, 19, '100gm', 'jsd ewfreq ereqrreq4dsafwqt sdfreqadwe fergersa q eewqrfe', 'images/seed_696c71cf74dc5.png', 'Active', '2026-01-18 05:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE `site_content` (
  `id` int(6) UNSIGNED NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`id`, `section_key`, `title`, `content`, `icon`, `image_path`, `updated_at`) VALUES
(1, 'hero_section', 'Revolutionizing Agriculture with <span class=\"highlight\">Artificial Intelligence</span>', 'Empowering farmers, connecting buyers, and building a sustainable future through technology.', '', 'images/hero-bg.jpg', '2026-01-07 11:11:55'),
(2, 'vision', 'Our Vision', 'To create a world where every farmer gets a fair price, and every buyer gets quality produce, supported by transparent and intelligent digital infrastructure.', 'fas fa-eye', '', '2026-01-07 11:11:55'),
(3, 'mission', 'Our Mission', 'Eliminating middlemen, reducing food waste, and optimizing crop planning through advanced AI analytics and direct market access.', 'fas fa-bullseye', '', '2026-01-07 11:11:55'),
(4, 'problem_1', 'Unfair Pricing', 'Farmers often receive a fraction of the market price due to multiple layers of middlemen.', 'fas fa-hand-holding-usd', '', '2026-01-07 11:11:55'),
(5, 'problem_2', 'Limited Market Access', 'Reliance on local mandis limits the customer base and bargaining power of small-scale farmers.', 'fas fa-store-slash', '', '2026-01-07 11:11:55'),
(6, 'problem_3', 'Unpredictable Conditions', 'Lack of real-time data on weather and demand leads to poor crop planning and potential losses.', 'fas fa-cloud-rain', '', '2026-01-07 11:11:55'),
(7, 'contact_info', 'Contact Details', 'support@agriai.com|+1 (555) 123-4567|Tech Valley, CA', '', '', '2026-01-07 11:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('farmer','buyer','admin','vendor') NOT NULL,
  `password` varchar(255) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `land_size` decimal(10,2) DEFAULT NULL,
  `crops` varchar(255) DEFAULT NULL,
  `farm_name` varchar(100) DEFAULT NULL,
  `bank_details` varchar(255) DEFAULT NULL,
  `business_name` varchar(100) DEFAULT NULL,
  `purchase_prefs` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `status` enum('Active','Pending','Blocked') DEFAULT 'Active',
  `verification_status` enum('Verified','Unverified','Pending') DEFAULT 'Unverified',
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `address`, `role`, `password`, `state`, `district`, `location`, `land_size`, `crops`, `farm_name`, `bank_details`, `business_name`, `purchase_prefs`, `created_at`, `profile_image`, `status`, `verification_status`, `remember_token`) VALUES
(103, 'darshan vora', 'admin@gmail.com', '7096503635', NULL, 'admin', '$2y$10$VhOHYOqNRi/.Rdpg56JnveSYG5ntHYMhPrs6wRApigc2JLGXBVzZW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-16 08:45:22', '', 'Active', 'Unverified', NULL),
(108, 'perin', 'perin@gmail.com', '7990079737', NULL, 'buyer', '$2y$10$1VX.prV9tOgboiytD1tqeeK7pZWZxcqEd0NWKsoEZDx7tgWFs2TM.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 03:57:29', NULL, 'Active', 'Unverified', NULL),
(116, 'sandip bhai', 's@gmail.com', '9909091465', NULL, 'buyer', '$2y$10$u1PS/Vs5RXF7sxYBnwInuOUL8oFXhwIipsC76REXE5flAYJFm82pm', NULL, NULL, 'lalpurroad jamangar', NULL, NULL, NULL, NULL, '', NULL, '2026-01-17 06:35:14', 'images/users/user_116_1768711541.jpg', 'Active', 'Unverified', NULL),
(122, 'rohit', 'rohit123@gmail.com', '7896541230', NULL, 'buyer', '$2y$10$eV1qtHoEPi36taKOhS1KEOcG.q2qXn14HIyLD62YXzBWyoote3LfK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 17:17:15', NULL, 'Active', 'Unverified', NULL),
(123, 'kevin r', 'kevin@gmail.com', '9537266317', NULL, 'buyer', '$2y$10$I2tv1ihPXGPf6Tiu2X/oTOEDne3WJdZkJWY4T.3D8PLqn85u4dnge', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 06:43:49', 'images/users/user_123_1768718661.png', 'Active', 'Unverified', NULL),
(124, 'Darvora Admin', 'darvora575@gmail.com', '7096503635', 'lalpurroad jamangar', 'admin', '$2y$10$ZX8dnyMSydN2TfRlRUHdw.AfLaiG.Mq2M87grJGAfb5PybpZbqLbu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 09:25:34', 'uploads/admin_1768731179.jpg', 'Active', 'Unverified', NULL),
(125, 'Shubham Admin', 'shubhammarkana290@gmail.com', '0000000000', NULL, 'admin', '$2y$10$pSRLWIzBk8TYhCLpMf/znugeS7nak0C0rUKA7j10qpkoVWxxQWVJy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 09:25:35', NULL, 'Active', 'Unverified', NULL),
(126, 'Shubham Admin', 'shubhammarakana290@gmail.com', '0000000000', NULL, 'admin', '$2y$10$5DqWmNMxF2Xm5HZ025/ByOEmaV8NTB0Kpy.HpNEZLI7ByFBiEC4b.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 10:09:33', NULL, 'Active', 'Unverified', NULL),
(127, 'kunj', 'k@gmail.com', '7862042651', NULL, 'vendor', '$2y$10$aTel7fZCvnEfqGJ0fExOeO9JXkuYBu66Gm/c03vjumptaCbSNc3p.', NULL, NULL, 'jamanar', NULL, NULL, NULL, NULL, 'pvt ltd', NULL, '2026-01-18 10:14:06', 'uploads/vendor_1768731337.jpg', 'Active', 'Verified', NULL),
(128, 'rohit', 'vaghelarohit654@gmail.com', '7069666454', NULL, 'vendor', '$2y$10$J/2/Pb0HWUg8DbbVbF72eeOrC0m5RNPaE/7QASp76e1wLojhPJYWW', NULL, NULL, 'no address', NULL, NULL, NULL, NULL, 'no company', NULL, '2026-01-19 04:54:45', 'uploads/vendor_1768798678.png', 'Active', 'Verified', NULL),
(129, 'parth', 'parth@gmail.com', '7891215113', NULL, 'buyer', '$2y$10$LiYXSbLcT0VwSsgo1yu8rO5VG2f3x0RtQTuNMQIYZBOnNXwsLU/8i', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 05:14:46', NULL, 'Active', 'Unverified', NULL),
(130, 'karan pansuriya', 'karan@gmail.com', '7418529630', NULL, 'buyer', '$2y$10$35.dozVEtvJvMAjIZcdq0.a9cwBDkL6s2pVg5ZQdZDhINh0Kj0H72', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 05:20:32', NULL, 'Active', 'Unverified', NULL),
(131, 'monil', 'monil@gmail.com', '7878563520', NULL, 'vendor', '$2y$10$N/.T7PoRSgIHx4bT2kiqy.16cBMDbrAaaYfDO2ZvlIjK.I9Zh3g4S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 07:52:06', '', 'Active', 'Verified', NULL),
(135, 'RUTU', 'rutu@gmail.com', '2585225800', NULL, 'farmer', '$2y$10$WyicMIaozbhmI1MiUzj4oO6nzDRkIKrkHfQmEsLaWSuDMhimeRiW2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 08:15:29', '', 'Active', 'Unverified', NULL),
(137, 'RUTU', 'r@gmail.com', '7539518520', NULL, 'farmer', '$2y$10$TgytJpFNtSSwFfsUAm8vuut7n2c9T4sl8exoJpUO1yiIQ49v9.sGK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 08:21:36', '', 'Active', 'Unverified', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) UNSIGNED NOT NULL,
  `crop_id` int(6) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `crop_id` (`crop_id`),
  ADD KEY `cart_ibfk_1` (`chemical_id`),
  ADD KEY `guest_token_id` (`guest_token_id`),
  ADD KEY `fk_cart_seed` (`seed_id`);

--
-- Indexes for table `chemicals`
--
ALTER TABLE `chemicals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chemical_cart`
--
ALTER TABLE `chemical_cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chemical_orders`
--
ALTER TABLE `chemical_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chemical_order_items`
--
ALTER TABLE `chemical_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_analytics`
--
ALTER TABLE `crop_analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `disease_reports`
--
ALTER TABLE `disease_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmer_profiles`
--
ALTER TABLE `farmer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `master_crops`
--
ALTER TABLE `master_crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `crop_id` (`crop_id`),
  ADD KEY `fk_order_items_seed` (`seed_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seeds`
--
ALTER TABLE `seeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_idx` (`seller_id`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `chemicals`
--
ALTER TABLE `chemicals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `chemical_cart`
--
ALTER TABLE `chemical_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chemical_orders`
--
ALTER TABLE `chemical_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `chemical_order_items`
--
ALTER TABLE `chemical_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `crop_analytics`
--
ALTER TABLE `crop_analytics`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disease_reports`
--
ALTER TABLE `disease_reports`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farmer_profiles`
--
ALTER TABLE `farmer_profiles`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `master_crops`
--
ALTER TABLE `master_crops`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seeds`
--
ALTER TABLE `seeds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_content`
--
ALTER TABLE `site_content`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_seed` FOREIGN KEY (`seed_id`) REFERENCES `seeds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chemical_order_items`
--
ALTER TABLE `chemical_order_items`
  ADD CONSTRAINT `chemical_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `chemical_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD CONSTRAINT `delivery_tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmer_profiles`
--
ALTER TABLE `farmer_profiles`
  ADD CONSTRAINT `farmer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_seed` FOREIGN KEY (`seed_id`) REFERENCES `seeds` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
