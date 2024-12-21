-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 20, 2024 lúc 05:11 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `sieuthi_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id_cart` int(11) NOT NULL,
  `user_username` varchar(15) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price_at_cart` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `id_category` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`id_category`, `name`, `is_active`, `image`) VALUES
(10, 'Thực phẩm tươi sống ', 1, '6761c95485ccc.jpg'),
(11, 'Thực phẩm đóng gói', 1, '6761c995dd598.jpg'),
(12, 'Đồ uống', 1, '6761c9be009f6.jpg'),
(13, 'Sản phẩm sữa', 0, '672f2b352ea35.jpg'),
(14, 'Đồ gia dụng', 1, '6761c9ea269cc.jpg'),
(15, 'Rau củ quả', 0, '6761ca60b0c00.jpg'),
(16, 'test', 0, '672f2b423775c.jpg'),
(17, 'thúy trọng', 0, '6762dde7c2bf5.jpg'),
(18, 'thúy', 0, '6762e2203155f.jpg'),
(19, 'thúy', 0, '676442234f1ab.jpg'),
(20, 'test', 0, '6765805122544.jpg'),
(21, 'test', 0, '676582ce47c0d.jpg'),
(22, 'test', 0, '67658361529a2.jpg'),
(23, 'test', 0, '67658430a9b0e.jpg'),
(24, 'test', 0, '676585a17cbc0.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `user_username` varchar(15) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_price` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id_order`, `user_username`, `order_date`, `total_price`, `payment_method`, `status`) VALUES
(30, 'test', '2024-11-07 23:58:27', 200000, 'COD', 'Hoàn thành'),
(31, 'test', '2024-11-08 00:01:13', 600000, 'COD', 'Hoàn thành'),
(32, 'test', '2024-11-08 00:05:01', 200000, 'COD', 'Hoàn thành'),
(33, 'test', '2024-11-08 12:51:09', 400000, 'COD', 'Hoàn thành'),
(34, 'test', '2024-11-09 15:57:52', 200000, 'Online', 'Hoàn thành'),
(35, 'test', '2024-12-02 12:31:51', 12, 'COD', 'Hoàn thành'),
(36, 'test1', '2024-12-02 12:53:01', 12, 'Online', 'Hoàn thành'),
(37, 'test1', '2024-12-02 14:09:11', 1080000, 'COD', 'Đang duyệt'),
(38, 'test1', '2024-12-02 14:09:49', 1440000, 'COD', 'Đang duyệt'),
(39, 'test1', '2024-12-02 14:12:29', 1440000, 'COD', 'Đang duyệt'),
(40, 'test1', '2024-12-02 14:13:57', 1440000, 'COD', 'Đang duyệt'),
(41, 'test1', '2024-12-02 14:16:45', 2720000, 'COD', 'Đang duyệt'),
(42, 'test1', '2024-12-02 14:16:57', 2520000, 'COD', 'Đang duyệt'),
(43, 'test1', '2024-12-02 14:17:00', 2400000, 'COD', 'Đang duyệt'),
(44, 'test1', '2024-12-02 14:28:03', 19800000, 'COD', 'Đang duyệt'),
(45, 'test2', '2024-12-02 14:29:05', 123000, 'COD', 'Đang duyệt'),
(46, 'test2', '2024-12-02 14:31:37', 1320000, 'COD', 'Đang duyệt'),
(47, 'test2', '2024-12-02 14:33:07', 1200000, 'COD', 'Đang duyệt'),
(48, 'test2', '2024-12-02 14:37:24', 240000, 'COD', 'Đang duyệt'),
(49, 'test2', '2024-12-02 22:57:39', 1320000, 'COD', 'Đang duyệt'),
(50, 'test2', '2024-12-02 22:59:29', 1080000, 'COD', 'Đang duyệt'),
(51, 'test2', '2024-12-02 23:01:41', 1320000, 'COD', 'Đang duyệt'),
(52, 'test2', '2024-12-02 23:09:02', 1200000, 'COD', 'Đang duyệt'),
(53, 'test2', '2024-12-02 23:09:23', 120000, 'COD', 'Đang duyệt'),
(54, 'test2', '2024-12-02 23:09:49', 0, 'COD', 'Đang duyệt'),
(55, 'test2', '2024-12-02 23:10:13', 0, 'COD', 'Đang duyệt'),
(56, 'test2', '2024-12-02 23:12:06', 1320000, 'COD', 'Đang duyệt'),
(57, 'test2', '2024-12-02 23:37:45', 1200000, 'COD', 'Đang duyệt'),
(58, 'test2', '2024-12-03 00:08:36', 0, 'COD', 'Đang duyệt'),
(59, 'test2', '2024-12-03 00:10:50', 0, 'COD', 'Đang duyệt'),
(60, 'test2', '2024-12-03 00:20:21', 480000, 'COD', 'Đang duyệt'),
(61, 'test2', '2024-12-03 00:25:13', 1200000, 'COD', 'Đang duyệt'),
(62, 'test2', '2024-12-03 00:25:38', 1200000, 'COD', 'Đang duyệt'),
(63, 'test2', '2024-12-03 00:27:58', 1080000, 'COD', 'Đang duyệt'),
(64, 'test2', '2024-12-03 00:31:39', 1080000, 'COD', 'Đang duyệt'),
(65, 'test2', '2024-12-03 00:32:18', 840000, 'COD', 'Đang duyệt'),
(66, 'test2', '2024-12-03 00:32:36', 840000, 'COD', 'Đang duyệt'),
(67, 'test2', '2024-12-03 00:35:02', 480000, 'COD', 'Đang duyệt'),
(68, 'test2', '2024-12-03 00:35:50', 1200000, 'COD', 'Đang duyệt'),
(69, 'test2', '2024-12-03 00:40:04', 960000, 'COD', 'Đang duyệt'),
(70, 'test2', '2024-12-03 00:40:20', 840000, 'COD', 'Đang duyệt'),
(71, 'test2', '2024-12-03 00:40:49', 720000, 'COD', 'Đang duyệt'),
(72, 'test2', '2024-12-03 00:41:15', 120012, 'COD', 'Đang duyệt'),
(73, 'test2', '2024-12-03 00:41:50', 120144, 'COD', 'Hoàn thành'),
(74, 'test2', '2024-12-03 19:30:52', 9504000, 'COD', 'Hoàn thành'),
(75, 'Thúy', '2024-12-17 00:11:33', 24, 'COD', 'Đang duyệt'),
(76, 'Thúy', '2024-12-17 00:12:40', 200000, 'COD', 'Đang duyệt'),
(77, 'trang', '2024-12-20 23:08:59', 240000, 'COD', 'Đang duyệt');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_detail`
--

CREATE TABLE `order_detail` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `price_at_purchase` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_detail`
--

INSERT INTO `order_detail` (`order_id`, `product_id`, `product_name`, `price_at_purchase`, `quantity`) VALUES
(30, 21, 'Rau cải bắp', 200000, 1),
(31, 21, 'Rau cải bắp', 200000, 3),
(32, 21, 'Rau cải bắp', 200000, 1),
(33, 21, 'Rau cải bắp', 200000, 1),
(33, 22, 'cà rốt', 200000, 1),
(34, 22, 'cà rốt', 200000, 1),
(35, 23, 'test', 12, 1),
(36, 23, 'test', 12, 1),
(37, 25, 'test5', 120000, 6),
(39, 25, 'test5', 120000, 11),
(40, 25, 'test5', 120000, 2),
(41, 22, 'cà rốt', 200000, 1),
(41, 25, 'test5', 120000, 1),
(42, 25, 'test5', 120000, 1),
(43, 21, 'Rau cải bắp', 200000, 12),
(44, 22, 'cà rốt', 200000, 99),
(45, 24, 'test3', 123000, 1),
(46, 25, 'test5', 120000, 11),
(47, 25, 'test5', 120000, 10),
(48, 25, 'test5', 120000, 2),
(49, 25, 'test5', 120000, 11),
(50, 25, 'test5', 120000, 9),
(51, 25, 'test5', 120000, 11),
(52, 25, 'test5', 120000, 10),
(53, 25, 'test5', 120000, 1),
(56, 25, 'test5', 120000, 11),
(57, 25, 'test5', 120000, 10),
(60, 25, 'test5', 120000, 4),
(61, 25, 'test5', 120000, 10),
(62, 25, 'test5', 120000, 10),
(63, 25, 'test556', 120000, 9),
(64, 25, 'test556', 120000, 9),
(65, 25, 'test556', 120000, 7),
(66, 25, 'test556', 120000, 7),
(67, 25, 'test556', 120000, 4),
(68, 25, 'test556', 120000, 10),
(69, 25, 'test556', 120000, 8),
(70, 25, 'test556', 120000, 7),
(71, 25, 'test556', 120000, 6),
(72, 23, 'test', 12, 1),
(72, 25, 'test556', 120000, 1),
(73, 23, 'test', 12, 12),
(73, 25, 'test556', 120000, 1),
(74, 25, 'test556', 105600, 90),
(75, 23, 'test', 12, 2),
(76, 21, 'Rau cải bắp', 200000, 1),
(77, 26, 'Cá chim trắng biển', 120000, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `id_product` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `shortdesc` varchar(100) DEFAULT NULL,
  `longdesc` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(100) DEFAULT 'assets/uploads/default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `Inprice` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`id_product`, `name`, `shortdesc`, `longdesc`, `price`, `quantity`, `category_id`, `image`, `created_at`, `deleted_at`, `is_active`, `Inprice`) VALUES
(21, 'Rau cải bắp', 'đây là rau cải bắp', 'hello is me', 200000, 99, 15, 'assets/uploads/pngtree-fresh-vegetable-canola-png-image_3925482.jpg', '2024-11-07 16:46:49', NULL, 0, 150001),
(22, 'cà rốt', 'Đây là 1 cà rốt', 'cà rốt thơm ngon', 200000, 100, 15, 'assets/uploads/pngtree-fresh-vegetable-canola-png-image_3925482.jpg', '2024-11-08 05:49:54', NULL, 0, 130000),
(23, 'test', 'tes', 'ttess', 12, 41, 11, 'assets/uploads/default.jpg', '2024-11-28 16:35:47', NULL, 0, 123),
(24, 'test3', 'test3', 'test3', 123000, 33, 12, 'assets/uploads/default.jpg', '2024-12-02 06:01:33', NULL, 0, 110000),
(25, 'test556', 'test5', 'test5', 120000, 100, 13, 'assets/uploads/default.jpg', '2024-12-02 06:22:15', NULL, 0, 100000),
(26, 'Cá chim trắng biển', 'Cá chim trắng biển Mini Market được lựa chọn từ những con cá tươi sống 100%', 'Cá chim không chỉ là một món ăn ngon mà còn là loại thực phẩm đặc biệt bổ dưỡng. Thịt cá rất chắc và thơm, là nguyên liệu cho nhiều món ăn ngon. Cá chim trắng biển VinMart được lựa chọn từ những con cá tươi sống 100% và được bảo quản ở nhiệt độ lạnh thích hợp nên vẫn giữ được độ tươi ngon. Sản phẩm được kiểm nghiệm kĩ lưỡng về vệ sinh an toàn thực phẩm, đảm bảo sức khỏe cho người tiêu dùng.\r\nThịt cá cung cấp nhiều protein và lipit thiết yếu cho cơ thể. Bạn có thể dùng cá chim để chế biến nhiều món ăn ngon cho gia đình như: cá chim chiên, nấu canh, kho, nướng hay cá chim rim gừng tỏi đều rất ngon và phù hợp với khẩu vị người Việt...', 120000, 998, 10, 'assets/uploads/default.jpg', '2024-12-17 19:02:57', NULL, 1, 89000),
(27, 'Trứng gà', 'Trứng gà tươi sạch', 'd', 30000, 234, 10, 'assets/uploads/default.jpg', '2024-12-17 19:07:37', NULL, 1, 23000),
(28, 'Thịt lợn', 'a', 'a', 23000, 12, 10, 'assets/uploads/default.jpg', '2024-12-17 19:09:19', NULL, 1, 10000),
(29, 'Ba chỉ bò Mỹ', 'q', 'a', 100000, 123, 10, 'assets/uploads/default.jpg', '2024-12-17 19:11:10', NULL, 1, 40000),
(30, 'Tôm', 'a', 'a', 190000, 1234, 10, 'assets/uploads/default.jpg', '2024-12-17 19:12:50', NULL, 1, 34000),
(31, 'Bánh đa nem', 'â', 'â', 12000, 111, 11, 'assets/uploads/default.jpg', '2024-12-17 19:15:10', NULL, 1, 1000),
(32, 'Phở Hoàng Gia', 'a', 'a', 12000, 12, 11, 'assets/uploads/default.jpg', '2024-12-17 19:16:21', NULL, 1, 9000),
(33, 'Mỳ tôm', 'g', 'g', 23000, 2345, 11, 'assets/uploads/default.jpg', '2024-12-17 19:17:33', NULL, 0, 20000),
(34, 'Đỗ xanh', 'a', 'a', 12000, 345, 11, 'assets/uploads/default.jpg', '2024-12-17 19:18:57', NULL, 1, 8000),
(35, 'Bánh pía', 'g', 'h', 40000, 5677, 11, 'assets/uploads/default.jpg', '2024-12-17 19:20:28', NULL, 1, 20000),
(36, 'Sữa hộp', 'd', 'd', 390000, 111, 12, 'assets/uploads/default.jpg', '2024-12-17 19:21:07', NULL, 1, 340000),
(37, 'Sữa Ensure', 'g', 'j', 500000, 4499, 12, 'assets/uploads/default.jpg', '2024-12-17 19:21:49', NULL, 1, 200000),
(38, 'Nước dừa', 'h', 'j', 34000, 30000, 12, 'assets/uploads/default.jpg', '2024-12-17 19:22:15', NULL, 1, 30000),
(39, 'Sữa chua', 'k', 'r', 34000, 45, 12, 'assets/uploads/default.jpg', '2024-12-17 19:22:54', NULL, 1, 23900),
(40, 'Trà sữa', 'c', 'c', 23900, 34, 12, 'assets/uploads/default.jpg', '2024-12-17 19:23:36', NULL, 1, 12000),
(41, 'Bếp điện', 'v', 'v', 1000000, 5999, 14, 'assets/uploads/default.jpg', '2024-12-17 19:24:31', NULL, 1, 500000),
(42, 'Nồi cơm', 'rr', 'r', 390000, 344, 14, 'assets/uploads/default.jpg', '2024-12-17 19:25:16', NULL, 1, 100000),
(43, 'Dây điện', 'f', 'f', 56000, 12, 14, 'assets/uploads/default.jpg', '2024-12-17 19:26:12', NULL, 1, 23000),
(44, 'Rổ', 'a', 'a', 23000, 45, 14, 'assets/uploads/default.jpg', '2024-12-17 19:27:47', NULL, 1, 19000),
(45, 'Thớt', 's', 's', 45000, 4444, 14, 'assets/uploads/default.jpg', '2024-12-17 19:28:38', NULL, 1, 30000),
(46, 'Cá hồi', 'f', 'f', 1230000, 12345, 10, 'assets/uploads/default.jpg', '2024-12-19 14:58:58', NULL, 1, 234500),
(47, 'Cánh gà', 's', 'sss', 123000, 12345, 10, 'assets/uploads/default.jpg', '2024-12-19 15:00:39', NULL, 1, 34500),
(48, 'Đùi gà', 'qưe', 'qqww', 456000, 23444, 10, 'assets/uploads/default.jpg', '2024-12-19 15:01:33', NULL, 1, 45000),
(49, 'Giò heo', 'ggg', 'ggg', 234000, 2333, 10, 'assets/uploads/default.jpg', '2024-12-19 15:03:49', NULL, 1, 123000),
(50, 'Thịt xay', 'bbbb', 'bbbbb', 100000, 5555, 10, 'assets/uploads/default.jpg', '2024-12-19 15:04:53', NULL, 1, 79000),
(51, 'Mít sấy', 'v', 'v', 500000, 1222, 11, 'assets/uploads/default.jpg', '2024-12-19 15:07:38', NULL, 1, 345000),
(52, 'Sầu riêng sấy', 'ss', 'ss', 123000, 3333, 11, 'assets/uploads/default.jpg', '2024-12-19 15:09:12', NULL, 1, 23000),
(53, 'Chuối cuộn', 'ccc', 'ccc', 1222222, 1111, 11, 'assets/uploads/default.jpg', '2024-12-19 15:10:03', NULL, 1, 12356),
(54, 'Mực sấy', 'cccc', 'cccc', 11111111, 23, 11, 'assets/uploads/default.jpg', '2024-12-19 15:11:03', NULL, 0, 1111);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id_product_image` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(100) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id_product_image`, `product_id`, `image_path`, `is_primary`, `created_at`) VALUES
(57, 24, 'assets/uploads/674d4d3d315ac_Screenshot 2024-07-14 232646.png', 1, '2024-12-02 06:01:33'),
(60, 23, 'assets/uploads/674df1fc2c427_Screenshot 2024-07-14 232646.png', 1, '2024-12-02 17:44:28'),
(61, 23, 'assets/uploads/674df1fc2dfd6_Screenshot 2024-08-25 223100.png', 0, '2024-12-02 17:44:28'),
(62, 23, 'assets/uploads/674df1fc2f591_Screenshot 2024-09-03 231741.png', 0, '2024-12-02 17:44:28'),
(72, 21, 'assets/uploads/6761922033f57_674fc01bb6fca_OIP.jpg', 1, '2024-12-17 15:00:48'),
(73, 22, 'assets/uploads/6761923a38c1c_675666ad1a41f_Screenshot 2024-07-14 232646.png', 1, '2024-12-17 15:01:14'),
(75, 27, 'assets/uploads/6761cbf945d07_579141.jpg', 1, '2024-12-17 19:07:37'),
(76, 28, 'assets/uploads/6761cc5f2a921_53063672_254357038841109_4779748080129933312_n.jpg', 1, '2024-12-17 19:09:19'),
(77, 29, 'assets/uploads/6761ccceac7dc_Thit-bo-nhap-khau-khong-nhan-mac.jpg', 1, '2024-12-17 19:11:10'),
(78, 30, 'assets/uploads/6761cd320de0e_26a533ee46f45dd22e9fdf4d5f419a08.png_720x720q80.png', 1, '2024-12-17 19:12:50'),
(79, 31, 'assets/uploads/6761cdbee9348_74936f6769b6074ea89e3f4d77a06135.png_720x720q80.png', 1, '2024-12-17 19:15:10'),
(80, 32, 'assets/uploads/6761ce05bcdad_3f0fb4b36f3064b359c7cc1c22fdfde3.png_720x720q80.png', 1, '2024-12-17 19:16:21'),
(81, 33, 'assets/uploads/6761ce4d5d927_images.jpg', 1, '2024-12-17 19:17:33'),
(82, 34, 'assets/uploads/6761cea16feb2_do xanh vo 200g.jpg', 1, '2024-12-17 19:18:57'),
(83, 35, 'assets/uploads/6761cefc50298_unnamed.png', 1, '2024-12-17 19:20:28'),
(84, 36, 'assets/uploads/6761cf23eb6fb_674fc01bb5688_i.jpg', 1, '2024-12-17 19:21:07'),
(85, 37, 'assets/uploads/6761cf4d79a90_674fc01bb6fca_OIP.jpg', 1, '2024-12-17 19:21:49'),
(86, 38, 'assets/uploads/6761cf67890b7_674fc01bb62be_OIP (2).jpg', 1, '2024-12-17 19:22:15'),
(87, 39, 'assets/uploads/6761cf8e8178f_674fc01bb5e0e_OIP (1).jpg', 1, '2024-12-17 19:22:54'),
(88, 40, 'assets/uploads/6761cfb873453_6759a25d332bc_67507606d15cb_ss.jpg', 1, '2024-12-17 19:23:36'),
(89, 41, 'assets/uploads/6761cfef91ec2_bep-dien-tu-comet-cm5426-1-1.jpg', 1, '2024-12-17 19:24:31'),
(90, 42, 'assets/uploads/6761d01c7a086_3d31edb1fc180bfd3fbcb0d2b34e397c.jpg', 1, '2024-12-17 19:25:16'),
(91, 43, 'assets/uploads/6761d054a6c77_1x2_5 vangbw600.jpg', 1, '2024-12-17 19:26:12'),
(92, 44, 'assets/uploads/6761d0b3d0cf5_383c4a66b9216ab239463fb40bb5b46a.jpg_720x720q80.jpg', 1, '2024-12-17 19:27:47'),
(93, 45, 'assets/uploads/6761d0e6754c5_03bc0e070a1cf21550c9faab11e89913_tn.jpg', 1, '2024-12-17 19:28:38'),
(94, 46, 'assets/uploads/676434b2350dc_ca-hoi-4-1.jpg', 1, '2024-12-19 14:58:58'),
(95, 47, 'assets/uploads/676435179ff8e_thit-dui-canh-ga-vietmart-sieu-thi-thuc-pham-viet-o-nhat.jpg', 1, '2024-12-19 15:00:39'),
(96, 48, 'assets/uploads/6764354d0b221_OIP (3).jpg', 1, '2024-12-19 15:01:33'),
(97, 49, 'assets/uploads/676435d5da648_R.png', 1, '2024-12-19 15:03:49'),
(98, 50, 'assets/uploads/6764361568d28_R.jpg', 1, '2024-12-19 15:04:53'),
(99, 51, 'assets/uploads/676436ba073a8_R (1).jpg', 1, '2024-12-19 15:07:38'),
(100, 52, 'assets/uploads/67643718e6232_4457276662bb55659f868cb8a1dea9fc.jpg_720x720q80.jpg', 1, '2024-12-19 15:09:12'),
(101, 53, 'assets/uploads/6764374b0e401_vn-11134207-7qukw-lfq7aq84leh014.jpg', 1, '2024-12-19 15:10:03'),
(102, 54, 'assets/uploads/67643787b14be_142020965863721328818200249191424.jpg', 1, '2024-12-19 15:11:03'),
(103, 26, 'assets/uploads/6765273edad59_cachim2.jpg', 0, '2024-12-20 08:13:50'),
(104, 26, 'assets/uploads/6765273edb827_cachim1.jpg', 1, '2024-12-20 08:13:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id_review` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_username` varchar(15) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id_review`, `product_id`, `user_username`, `order_id`, `rating`, `review_text`, `created_at`) VALUES
(2, 23, 'test', 35, 2, 'csacsad', '2024-12-02 05:44:08'),
(4, 23, 'test1', 36, 3, 'Life is full of surprises—some joyful, others challenging. It\'s through these moments, both good and bad, that we grow stronger, wiser, and more grateful for the blessings we often take for granted', '2024-12-02 05:53:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `isAdmin` tinyint(1) DEFAULT 0,
  `isDisabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `fullname`, `phone`, `email`, `address`, `isAdmin`, `isDisabled`) VALUES
(1, 'admin', 'admin', 'Admin User', '0900000000', 'admin@example.com', '123 Admin St', 1, 0),
(12, 'test', 'test', 'test', '34324', 'test@test.com', 'sfewf', 0, 0),
(13, 'testtest', 'testtest', 'testtest', '01234567890', 'testtest@gmail.com', NULL, 0, 0),
(14, 'hellokitty', 'hellokitty', 'hellokitty', '123213213', 'hellokitty@gmail.com', NULL, 0, 0),
(15, 'hieubigay', 'hieubigay', 'hieubigay', '1232132312', 'hieubigay@gmail.com', NULL, 0, 0),
(16, 'test1', 'test1', 'test1', '3423432', 'test1@f.com', 'dewdwe', 0, 0),
(17, 'test2', 'test2', 'test2', '21321', 'test2@f.com', '    ', 0, 0),
(18, 'test4', 'test4', 'test4', '213213', 'test4@g.com', NULL, 0, 0),
(19, 'Thúy', '12345', 'Vũ Diệu Thúy', '0918183155', 'dieuthuy240303@gmail.com', 'aaaaa', 0, 0),
(20, 'trang', '123456', 'trang', '0234567890', 'phamhatrang03@gmail.com', 'HN', 0, 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id_cart`),
  ADD KEY `FK_user_cart` (`user_username`),
  ADD KEY `FK_product_cart` (`product_id`);

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_category`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `FK_user_orders` (`user_username`);

--
-- Chỉ mục cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `FK_product_order_detail` (`product_id`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `FK_category_product` (`category_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id_product_image`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `FK_product_review` (`product_id`),
  ADD KEY `FK_user_review` (`user_username`),
  ADD KEY `FK_order_id` (`order_id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id_cart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT cho bảng `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id_product_image` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_product_cart` FOREIGN KEY (`product_id`) REFERENCES `product` (`id_product`),
  ADD CONSTRAINT `FK_user_cart` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_user_orders` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`);

--
-- Các ràng buộc cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `FK_orders_order_detail` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `FK_product_order_detail` FOREIGN KEY (`product_id`) REFERENCES `product` (`id_product`);

--
-- Các ràng buộc cho bảng `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_category_product` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`);

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id_product`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `FK_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `FK_order_review` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `FK_product_review` FOREIGN KEY (`product_id`) REFERENCES `product` (`id_product`),
  ADD CONSTRAINT `FK_user_review` FOREIGN KEY (`user_username`) REFERENCES `user` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
