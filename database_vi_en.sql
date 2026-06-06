-- ============================================
-- Database: 013
-- Language: Vietnamese / English (comment song ngữ)
-- Purpose: Cấu trúc database cho hệ thống giải trí trực tuyến
-- ============================================

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table: activities (Hoạt động / Activities)
-- ----------------------------
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT 'Loại hoạt động / Activity type',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề / Title',
  `entitle` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tiêu đề tiếng Anh / English title',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung / Content',
  `encontent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nội dung tiếng Anh / English content',
  `memo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú nội dung / Content memo',
  `enmemo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú tiếng Anh / English memo',
  `apply_count` int(11) NOT NULL DEFAULT '0' COMMENT 'Số lượt đăng ký / Apply count',
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh banner / Banner image',
  `can_apply` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Có thể đăng ký, 0=Không / 1=Can apply, 0=Cannot',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Hoạt động, 0=Vô hiệu / 1=Active, 0=Disabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: activity_apply (Đăng ký hoạt động / Activity apply)
-- ----------------------------
DROP TABLE IF EXISTS `activity_apply`;
CREATE TABLE `activity_apply` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL COMMENT 'ID hoạt động / Activity ID',
  `user_id` int(11) NOT NULL COMMENT 'ID người dùng / User ID',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Chờ duyệt, 2=Đã duyệt, 3=Từ chối / 1=Pending, 2=Approved, 3=Rejected',
  `check_time` datetime DEFAULT NULL COMMENT 'Thời gian duyệt / Check time',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: activity_types (Loại hoạt động / Activity types)
-- ----------------------------
DROP TABLE IF EXISTS `activity_types`;
CREATE TABLE `activity_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên loại / Type name',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Khả dụng, 0=Vô hiệu / 1=Active, 0=Disabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_extensions (Tiện ích mở rộng Admin / Admin extensions)
-- ----------------------------
DROP TABLE IF EXISTS `admin_extensions`;
CREATE TABLE `admin_extensions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0',
  `options` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_extensions_name_unique` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_extension_histories (Lịch sử tiện ích Admin / Admin extension histories)
-- ----------------------------
DROP TABLE IF EXISTS `admin_extension_histories`;
CREATE TABLE `admin_extension_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `detail` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `admin_extension_histories_name_index` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_menu (Menu Admin / Admin menu)
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `show` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_permissions (Quyền Admin / Admin permissions)
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '0',
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_permissions_slug_unique` (`slug`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_permission_menu (Liên kết quyền - menu / Permission menu mapping)
-- ----------------------------
DROP TABLE IF EXISTS `admin_permission_menu`;
CREATE TABLE `admin_permission_menu` (
  `permission_id` bigint(20) NOT NULL,
  `menu_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `admin_permission_menu_permission_id_menu_id_unique` (`permission_id`,`menu_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_roles (Vai trò Admin / Admin roles)
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_roles_slug_unique` (`slug`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_role_menu (Liên kết vai trò - menu / Role menu mapping)
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu` (
  `role_id` bigint(20) NOT NULL,
  `menu_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `admin_role_menu_role_id_menu_id_unique` (`role_id`,`menu_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_role_permissions (Liên kết vai trò - quyền / Role permissions mapping)
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `role_id` bigint(20) NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `admin_role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_role_users (Liên kết vai trò - người dùng admin / Admin user roles)
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users` (
  `role_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `admin_role_users_role_id_user_id_unique` (`role_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_settings (Cài đặt Admin / Admin settings)
-- ----------------------------
DROP TABLE IF EXISTS `admin_settings`;
CREATE TABLE `admin_settings` (
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`slug`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: admin_users (Người dùng Admin / Admin users)
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_users_username_unique` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: agent_apply (Đăng ký đại lý / Agent application)
-- ----------------------------
DROP TABLE IF EXISTS `agent_apply`;
CREATE TABLE `agent_apply` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số điện thoại / Mobile',
  `apply_info` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin đăng ký / Apply info',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Chờ, 2=Duyệt, 3=Từ chối / 1=Pending, 2=Approved, 3=Rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: agent_settlements (Phương thức thanh toán đại lý / Agent settlement plans)
-- ----------------------------
DROP TABLE IF EXISTS `agent_settlements`;
CREATE TABLE `agent_settlements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên phương thức / Name',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Điểm, 2=Hoa hồng / 1=Rebate, 2=Commission',
  `realperson` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Người thật / Live casino',
  `electron` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Điện tử / Slots',
  `joker` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Bài / Card games',
  `sport` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Thể thao / Sports',
  `fish` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Bắn cá / Fishing',
  `lottery` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Xổ số / Lottery',
  `e_sport` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Thể thao điện tử / E-sports',
  `member_fs` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả thành viên / Member rebate',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái / Status',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: apis (Danh sách API game / Game APIs)
-- ----------------------------
DROP TABLE IF EXISTS `apis`;
CREATE TABLE `apis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `api_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã API / API code',
  `api_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên API / API name',
  `api_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư API / API balance',
  `game_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loại game / Game type',
  `plat_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loại nền tảng / Platform type',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái / Status',
  `app_state` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái App / App status',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_by` int(11) DEFAULT NULL COMMENT 'Sắp xếp / Order',
  `app_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon App / App icon',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: articles (Bài viết / Articles)
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT 'Tiêu đề / Title',
  `enname` varchar(255) DEFAULT NULL COMMENT 'Tiêu đề tiếng Anh / English title',
  `cateid` int(10) DEFAULT NULL COMMENT 'ID danh mục / Category ID',
  `content` text COMMENT 'Nội dung / Content',
  `encontent` text COMMENT 'Nội dung tiếng Anh / English content',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stor` int(5) DEFAULT NULL COMMENT 'Thứ tự / Order',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: articlescate (Danh mục bài viết / Article categories)
-- ----------------------------
DROP TABLE IF EXISTS `articlescate`;
CREATE TABLE `articlescate` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT 'Tên danh mục / Category name',
  `Created_at` datetime DEFAULT NULL,
  `Updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: banks (Ngân hàng / Banks)
-- ----------------------------
DROP TABLE IF EXISTS `banks`;
CREATE TABLE `banks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã ngân hàng / Bank code',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên ngân hàng / Bank name',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Sắp xếp / Order',
  `max_amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Hạn mức tối đa / Max amount',
  `bank_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hình ảnh / Image',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái / Status',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: banners (Banner quảng cáo / Banners)
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=PC, 2=Mobile',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tiêu đề / Title',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn ảnh / Image path',
  `jump_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL chuyển hướng / Jump URL',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Sắp xếp / Order',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Hiển thị, 0=Ẩn / 1=Show, 0=Hide',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: code_pay (Mã thanh toán / Payment codes)
-- ----------------------------
DROP TABLE IF EXISTS `code_pay`;
CREATE TABLE `code_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mch_id` varchar(255) DEFAULT NULL COMMENT 'ID merchant / Merchant ID',
  `key` varchar(255) DEFAULT NULL COMMENT 'Khóa bảo mật / Key',
  `content` varchar(255) DEFAULT NULL COMMENT 'Nội dung / Content',
  `status` int(1) DEFAULT '1' COMMENT '0=Vô hiệu, 1=Kích hoạt / 0=Disabled, 1=Enabled',
  `payimg` varchar(255) DEFAULT NULL COMMENT 'Ảnh QR / QR image',
  `min_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số tiền tối thiểu / Min amount',
  `max_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số tiền tối đa / Max amount',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: failed_jobs (Các tác vụ thất bại / Failed jobs)
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: gamereport (Báo cáo game / Game report)
-- ----------------------------
DROP TABLE IF EXISTS `gamereport`;
CREATE TABLE `gamereport` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT '0' COMMENT 'ID thành viên / User ID',
  `addtime` int(10) DEFAULT '0' COMMENT 'Thời gian báo cáo (timestamp)',
  `pid` int(10) DEFAULT '0' COMMENT 'ID đại lý / Agent ID',
  `recnum` int(10) DEFAULT '0' COMMENT 'Số lần nạp / Recharge count',
  `rechangenum` int(10) DEFAULT '0' COMMENT 'Số lần chuyển?',
  `totalrechange` decimal(10,2) DEFAULT '0.00' COMMENT 'Tổng nạp / Total deposit',
  `withdrawnum` int(10) DEFAULT '0' COMMENT 'Số lần rút / Withdraw count',
  `totalwithdraw` decimal(10,2) DEFAULT '0.00' COMMENT 'Tổng rút / Total withdraw',
  `betnum` int(10) DEFAULT NULL COMMENT 'Số lần cược / Bet count',
  `totalbet` decimal(10,2) DEFAULT '0.00' COMMENT 'Tổng cược / Total bet',
  `totalvalidamount` decimal(10,2) DEFAULT NULL COMMENT 'Tổng cược hợp lệ / Total valid bet',
  `totalwinloss` decimal(10,2) DEFAULT '0.00' COMMENT 'Thắng/thua / Win/loss',
  `redpackectnum` int(10) DEFAULT '0' COMMENT 'Số lần nhận hồng bao / Red packet count',
  `totalredpackect` decimal(10,2) DEFAULT '0.00' COMMENT 'Tổng hồng bao / Total red packet',
  `releasewater` decimal(10,2) DEFAULT NULL COMMENT 'Hoàn trả / Rebate',
  `rakeback` decimal(10,2) DEFAULT NULL COMMENT 'Hoa hồng / Rakeback',
  `rakebacknum` int(10) DEFAULT NULL COMMENT 'Số lần hoa hồng',
  `releasewaternum` int(10) DEFAULT NULL COMMENT 'Số lần hoàn trả',
  `isagent` int(1) DEFAULT '0' COMMENT 'Có phải đại lý? / Is agent?',
  `updated_at` varchar(20) DEFAULT NULL,
  `created_at` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: game_lists (Danh sách game / Game lists)
-- ----------------------------
DROP TABLE IF EXISTS `game_lists`;
CREATE TABLE `game_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên nền tảng / Platform name',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên game / Game name',
  `name_en` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên tiếng Anh / English name',
  `keywords` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Từ khóa / Keywords',
  `game_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã game / Game code',
  `game_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon game',
  `game_title_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh tiêu đề',
  `category_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID danh mục / Category ID',
  `order_by` int(11) NOT NULL DEFAULT '0' COMMENT 'Sắp xếp / Order',
  `is_hot` int(11) NOT NULL DEFAULT '0' COMMENT '1=Hot, 0=Không',
  `is_new` int(11) NOT NULL DEFAULT '0' COMMENT '1=Mới, 0=Không',
  `is_recommend` int(11) NOT NULL DEFAULT '0' COMMENT '1=Gợi ý, 0=Không',
  `is_pc` int(11) NOT NULL DEFAULT '1' COMMENT '1=Hiển thị PC',
  `is_mobile` int(11) NOT NULL DEFAULT '1' COMMENT '1=Hiển thị Mobile',
  `site_state` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái web / Site status',
  `app_state` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái App / App status',
  `is_top` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Danh mục cha? / Top category?',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `check_yes_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_no_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_logo_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=527 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: game_records (Lịch sử cược / Game records)
-- ----------------------------
DROP TABLE IF EXISTS `game_records`;
CREATE TABLE `game_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên đăng nhập / Username',
  `bet_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã cược / Bet ID',
  `bet_time` datetime NOT NULL COMMENT 'Thời gian cược / Bet time',
  `platform_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nền tảng / Platform',
  `game_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Loại game / Game type',
  `bet_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số tiền cược / Bet amount',
  `valid_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tiền cược hợp lệ / Valid bet',
  `win_loss` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Kết quả (+/-) / Win/loss',
  `is_back` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=Đã hoàn trả, 0=Chưa / Rebated?',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=Đã thanh toán, 2=Chưa, 0=Không hợp lệ / 1=Settled, 2=Pending, 0=Invalid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: messages (Tin nhắn / Messages)
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT 'ID thành viên nhận (0=toàn bộ)',
  `vip_id` int(11) DEFAULT NULL COMMENT 'Cấp VIP nhận / Target VIP level',
  `isagent` int(1) DEFAULT '0' COMMENT '1=Gửi đại lý, 2=VIP blacklist',
  `type` int(11) NOT NULL COMMENT '1=Thông báo, 2=Khuyến mãi, 3=Công bố / 1=Notice, 2=Promo, 3=Announcement',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề / Title',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung / Content',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: migrations (Lịch sử migration / Migrations)
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: password_resets (Đặt lại mật khẩu / Password resets)
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: pay_setting (Cài đặt nhận thanh toán / Pay settings)
-- ----------------------------
DROP TABLE IF EXISTS `pay_setting`;
CREATE TABLE `pay_setting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bank_id` int(11) NOT NULL COMMENT 'ID ngân hàng / Bank ID',
  `bank_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Số tài khoản / Account number',
  `bank_owner` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chủ tài khoản / Account holder',
  `bank_address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chi nhánh / Branch',
  `info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú / Info',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Khả dụng, 0=Vô hiệu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: recharge (Lịch sử nạp tiền / Recharge history)
-- ----------------------------
DROP TABLE IF EXISTS `recharge`;
CREATE TABLE `recharge` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã đơn hệ thống / System order number',
  `out_trade_no` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã đơn merchant / Merchant order number',
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `amount` decimal(10,2) NOT NULL COMMENT 'Số tiền / Amount',
  `cash_fee` decimal(8,2) NOT NULL COMMENT 'Phí / Fee',
  `real_money` decimal(10,2) NOT NULL COMMENT 'Tiền thực nhận / Actual received',
  `pay_way` tinyint(4) NOT NULL COMMENT '1=Chuyển khoản, 2=ZGPay, ...',
  `bank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ngân hàng / Bank',
  `bank_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số tài khoản / Account number',
  `bank_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chi nhánh / Branch',
  `bank_owner` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chủ tài khoản / Account holder',
  `info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú / Note',
  `usdt_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tỷ giá USDT / USDT rate',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Chờ, 2=Thành công, 3=Thất bại',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: red_envelopes (Hồng bao / Red envelopes config)
-- ----------------------------
DROP TABLE IF EXISTS `red_envelopes`;
CREATE TABLE `red_envelopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day_flow` decimal(10,2) DEFAULT NULL COMMENT 'Doanh thu ngày / Daily turnover',
  `recharge` decimal(10,2) DEFAULT NULL COMMENT 'Số tiền nạp / Recharge amount',
  `flow_money` decimal(10,2) DEFAULT NULL COMMENT 'Doanh thu yêu cầu / Required turnover',
  `money` decimal(10,2) DEFAULT NULL COMMENT 'Số tiền hồng bao / Red packet amount',
  `start_time` timestamp NULL DEFAULT NULL COMMENT 'Thời gian bắt đầu / Start time',
  `end_time` timestamp NULL DEFAULT NULL COMMENT 'Thời gian kết thúc / End time',
  `status` int(1) DEFAULT '1' COMMENT '0=Disable, 1=Enable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: sessions (Phiên đăng nhập / Sessions)
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: suggestions (Góp ý / Suggestions)
-- ----------------------------
DROP TABLE IF EXISTS `suggestions`;
CREATE TABLE `suggestions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT 'Loại vấn đề / Issue type',
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung / Content',
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đính kèm / Image',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: syslog (Nhật ký hệ thống / System log)
-- ----------------------------
DROP TABLE IF EXISTS `syslog`;
CREATE TABLE `syslog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '10',
  `type` int(2) NOT NULL DEFAULT '0',
  `memo` varchar(255) DEFAULT NULL,
  `addtime` varchar(20) NOT NULL,
  `updated_at` varchar(20) NOT NULL,
  `created_at` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: system_config (Cấu hình hệ thống / System config)
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: templates (Giao diện / Templates)
-- ----------------------------
DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên giao diện / Template name',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ảnh thu nhỏ / Thumbnail',
  `client_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=PC, 2=WAP, 3=App',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT 'Sắp xếp / Order',
  `template_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã giao diện / Template ID',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Khả dụng, 0=Vô hiệu, 2=Đang dùng',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: transfer_logs (Lịch sử chuyển tiền game / Transfer logs)
-- ----------------------------
DROP TABLE IF EXISTS `transfer_logs`;
CREATE TABLE `transfer_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã đơn / Order number',
  `api_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Loại API / API type',
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `transfer_type` tinyint(4) NOT NULL COMMENT '0=Chuyển vào game, 1=Rút về',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số tiền chuyển / Transfer amount',
  `cash_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Phí / Fee',
  `real_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Thực nhận / Actual amount',
  `before_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư trước / Balance before',
  `after_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư sau / Balance after',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Thành công, 0=Thất bại, 2=Chờ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `platform_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nền tảng / Platform',
  `addtime` int(10) DEFAULT '0',
  `settlementsday` int(2) DEFAULT '0' COMMENT 'Ngày thanh toán',
  `betid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã cược / Bet ID',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú / Remark',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: userredpacket (Hồng bao thành viên / User red packets)
-- ----------------------------
DROP TABLE IF EXISTS `userredpacket`;
CREATE TABLE `userredpacket` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT 'ID thành viên / User ID',
  `redpacketid` int(10) NOT NULL COMMENT 'ID hồng bao / Red packet ID',
  `redpacketfee` decimal(10,2) NOT NULL COMMENT 'Số tiền hồng bao / Red packet amount',
  `money` decimal(10,2) NOT NULL COMMENT 'Số tiền yêu cầu?',
  `redpacketmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tiền hồng bao thực nhận',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT 'Trạng thái',
  `usetime` varchar(30) DEFAULT NULL COMMENT 'Thời gian sử dụng / Use time',
  `isuse` int(1) DEFAULT '0' COMMENT 'Đã sử dụng? / Used?',
  `created_at` varchar(30) NOT NULL,
  `updated_at` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table: users (Thành viên / Users)
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT 'ID đại lý trên / Parent agent ID',
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên đăng nhập / Username',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mật khẩu / Password',
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token API',
  `realname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên thật / Real name',
  `vip` int(11) NOT NULL DEFAULT '1' COMMENT 'Cấp VIP / VIP level',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT 'Cấp độ / Level',
  `exp` int(11) NOT NULL DEFAULT '0' COMMENT 'Kinh nghiệm / Experience',
  `paypwd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mật khẩu thanh toán / Payment password',
  `isonline` int(11) NOT NULL DEFAULT '0' COMMENT 'Đang trực tuyến? / Online?',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đại diện / Avatar',
  `allowagent` int(11) NOT NULL DEFAULT '0' COMMENT 'Cho phép làm đại lý? / Allow agent?',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư chính / Main balance',
  `mbalance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư mã cược / Bet balance',
  `totalgame` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tổng cược / Total bet',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số điện thoại / Phone',
  `mail` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email',
  `paysum` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tổng nạp / Total deposit',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái / Status',
  `isdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Đã xóa? / Deleted?',
  `isblack` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Blacklist?',
  `lastip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP cuối / Last IP',
  `last_login_ip_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ IP cuối',
  `logintime` int(11) DEFAULT NULL COMMENT 'Thời gian đăng nhập cuối (timestamp)',
  `sourceurl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nguồn đăng ký / Source URL',
  `loginsum` int(11) NOT NULL DEFAULT '0' COMMENT 'Số lần đăng nhập / Login count',
  `birthday` date DEFAULT NULL COMMENT 'Ngày sinh / Birthday',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `isagent` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=Đại lý, 0=Thành viên / Is agent?',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT 'ID người giới thiệu / Referrer ID',
  `settlement_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ID phương thức thanh toán / Settlement plan ID',
  `fanshuifee` decimal(5,2) DEFAULT NULL COMMENT 'Phí hoàn trả / Rebate fee',
  `settlementday` int(10) DEFAULT '0' COMMENT 'Ngày thanh toán cuối',
  `reg_ip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP đăng ký / Registration IP',
  `transferstatus` int(1) DEFAULT '0' COMMENT '0=Chuyển thường, 1=Miễn chuyển',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_api_token_unique` (`api_token`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: usersmoney (Số dư game theo API / User game balances)
-- ----------------------------
DROP TABLE IF EXISTS `usersmoney`;
CREATE TABLE `usersmoney` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `ag_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `allbet_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bbin_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bg_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `og_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pt_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gd_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dg_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qt_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ky_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ig_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `jdb_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fg_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `avia_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `leg_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bng_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dt_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gg_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL,
  `vrbet_money` decimal(10,2) DEFAULT '0.00',
  `hlgame_money` decimal(10,2) DEFAULT '0.00',
  `hbb_money` decimal(10,2) DEFAULT '0.00',
  `qg_money` decimal(10,2) DEFAULT '0.00',
  `hc_money` decimal(10,2) DEFAULT '0.00',
  `play99_money` decimal(10,2) DEFAULT '0.00',
  `yb_money` decimal(10,2) DEFAULT '0.00',
  `ly_money` decimal(10,2) DEFAULT '0.00',
  `kx_money` decimal(10,2) DEFAULT '0.00',
  `dfw_money` decimal(10,2) DEFAULT '0.00',
  `xsj_money` decimal(10,2) DEFAULT '0.00',
  `ld_money` decimal(10,2) DEFAULT '0.00',
  `ae_money` decimal(10,2) DEFAULT '0.00',
  `oap_money` decimal(10,2) DEFAULT '0.00',
  `ia_money` decimal(10,2) DEFAULT '0.00',
  `sy_money` decimal(10,2) DEFAULT '0.00',
  `xsbo_money` decimal(10,2) DEFAULT '0.00',
  `ps_money` decimal(10,2) DEFAULT '0.00',
  `habaner_money` decimal(10,2) DEFAULT '0.00',
  `jz_money` decimal(10,2) DEFAULT '0.00',
  `cmd_money` decimal(10,2) DEFAULT '0.00',
  `sbtest_money` decimal(10,2) DEFAULT '0.00',
  `wm_money` decimal(10,2) DEFAULT '0.00',
  `zeus_money` decimal(10,2) DEFAULT '0.00',
  `cg_money` decimal(10,2) DEFAULT '0.00',
  `icg_money` decimal(10,2) DEFAULT '0.00',
  `pp_money` decimal(10,2) DEFAULT '0.00',
  `pg_money` decimal(10,2) DEFAULT '0.00',
  `sg_money` decimal(10,2) DEFAULT '0.00',
  `vg_money` decimal(10,2) DEFAULT '0.00',
  `tc_money` decimal(10,2) DEFAULT '0.00',
  `datqp_money` decimal(10,2) DEFAULT '0.00',
  `tm_money` decimal(10,2) DEFAULT '0.00',
  `ap_money` decimal(10,2) DEFAULT '0.00',
  `kx2_money` decimal(10,2) DEFAULT '0.00',
  `imone_money` decimal(10,2) DEFAULT '0.00',
  `obgzr_money` decimal(10,2) DEFAULT '0.00',
  `obgqp_money` decimal(10,2) DEFAULT '0.00',
  `saba_money` decimal(10,2) DEFAULT '0.00',
  `obgcp_money` decimal(10,2) DEFAULT '0.00',
  `obgdj_money` decimal(10,2) DEFAULT '0.00',
  `obgty_money` decimal(10,2) DEFAULT '0.00',
  `obgdy_money` decimal(10,2) DEFAULT '0.00',
  `dl_money` decimal(10,2) DEFAULT '0.00',
  `obgpy_money` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: user_api (Tài khoản API game của thành viên / User game API accounts)
-- ----------------------------
DROP TABLE IF EXISTS `user_api`;
CREATE TABLE `user_api` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `api_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_pass` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã API / API code',
  `api_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số dư API / API balance',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: user_cards (Thẻ ngân hàng của thành viên / User bank cards)
-- ----------------------------
DROP TABLE IF EXISTS `user_cards`;
CREATE TABLE `user_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `bank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ngân hàng / Bank',
  `bank_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số tài khoản / Account number',
  `bank_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chi nhánh / Branch',
  `bank_owner` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chủ tài khoản / Account holder',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: user_messages (Tin nhắn đã nhận / User messages)
-- ----------------------------
DROP TABLE IF EXISTS `user_messages`;
CREATE TABLE `user_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `message_id` int(11) NOT NULL COMMENT 'ID tin nhắn / Message ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: user_operate_logs (Nhật ký thao tác thành viên / User operation logs)
-- ----------------------------
DROP TABLE IF EXISTS `user_operate_logs`;
CREATE TABLE `user_operate_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `type` tinyint(4) NOT NULL COMMENT 'Loại: 1=Đăng nhập, 2=Đăng xuất, 3=Thao tác, 4=Agent đăng nhập, ...',
  `login_ua` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User Agent',
  `login_ip` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP đăng nhập',
  `ip_address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Địa chỉ IP',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả / Description',
  `info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin thêm / Extra info',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: user_vip (Cấp VIP / VIP levels)
-- ----------------------------
DROP TABLE IF EXISTS `user_vip`;
CREATE TABLE `user_vip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vipname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên cấp / Level name',
  `viptype` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Loại hoàn trả / Rebate type',
  `recharge` decimal(12,2) DEFAULT NULL COMMENT 'Điều kiện nạp / Recharge condition',
  `flow` decimal(12,2) DEFAULT NULL COMMENT 'Điều kiện doanh thu / Turnover condition',
  `realperson` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả người thật / Live rebate %',
  `electron` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả điện tử / Slots rebate %',
  `joker` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả bài / Card rebate %',
  `sport` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả thể thao / Sports rebate %',
  `fish` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả bắn cá / Fishing rebate %',
  `lottery` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả xổ số / Lottery rebate %',
  `e_sport` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Hoàn trả thể thao điện tử / E-sports rebate %',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Trạng thái / Status',
  `exp` int(11) NOT NULL DEFAULT '0' COMMENT 'Kinh nghiệm yêu cầu / Required exp',
  `is_default` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Mặc định? / Default?',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vrberfee` decimal(6,2) DEFAULT NULL,
  `ldfee` decimal(6,2) DEFAULT NULL,
  `vippic` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh cấp VIP / VIP icon',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table: withdraws (Lịch sử rút tiền / Withdraw history)
-- ----------------------------
DROP TABLE IF EXISTS `withdraws`;
CREATE TABLE `withdraws` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã đơn / Order number',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1=Ngân hàng, 2=USDT',
  `card_id` int(11) NOT NULL COMMENT 'ID thẻ / Card ID',
  `user_id` int(11) NOT NULL COMMENT 'ID thành viên / User ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Số tiền / Amount',
  `cash_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Phí / Fee',
  `real_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Thực nhận / Actual received',
  `usdt_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tỷ giá USDT / USDT rate',
  `info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú / Note',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Chờ duyệt, 2=Đã duyệt, 3=Từ chối',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS=1;