-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 10 月 20 日 17:55
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `21c`
--

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_authors`
--

CREATE TABLE `circulate_authors` (
  `id` int(11) NOT NULL COMMENT '管理員編號',
  `aid` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理員登入帳號',
  `pwd` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理員密碼',
  `aname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理員姓名',
  `email` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理員email',
  `enable` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否啟用(0. 不啟用 1.啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='管理員基本資料';

--
-- 傾印資料表的資料 `circulate_authors`
--

INSERT INTO `circulate_authors` (`id`, `aid`, `pwd`, `aname`, `email`, `enable`) VALUES
(1, 'admin', 'PASSWORD', 'ADMIN', 'admin@', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_authors_ipmanage`
--

CREATE TABLE `circulate_authors_ipmanage` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `ipsec1` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class A',
  `ipsec2` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class B',
  `ipsec3` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class C',
  `ipsec4` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class D (開始)',
  `ipsec5` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class D (結束)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='後台介面IP登入管理';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_customer`
--

CREATE TABLE `circulate_customer` (
  `id` int(11) NOT NULL COMMENT 'PK,Auto',
  `name` varchar(100) NOT NULL COMMENT '姓名',
  `email` varchar(100) NOT NULL COMMENT 'email',
  `cellphone` varchar(20) NOT NULL COMMENT '行動電話',
  `telephone` varchar(20) NOT NULL COMMENT '電話號碼',
  `address` varchar(200) NOT NULL COMMENT '地址',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `store_id` int(11) NOT NULL COMMENT '0:為總公司資料，其他：為門店資料FK->store'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='客戶基本資料';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_customer_group`
--

CREATE TABLE `circulate_customer_group` (
  `id` int(11) NOT NULL COMMENT 'PK, Auto',
  `name` varchar(100) NOT NULL COMMENT '群組名稱',
  `store_id` int(11) NOT NULL COMMENT '0:為總公司資料，其他：為門店資料FK->store	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='客戶（分）群組';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_customer_label`
--

CREATE TABLE `circulate_customer_label` (
  `id` int(11) NOT NULL COMMENT 'PK, Auto',
  `caption` varchar(100) NOT NULL COMMENT 'label名稱',
  `store_id` int(11) NOT NULL COMMENT '0:為總公司資料，其他：為門店資料FK->store	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='客戶貼票名稱';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_function`
--

CREATE TABLE `circulate_function` (
  `id` int(11) NOT NULL COMMENT '權限流水號',
  `fid` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '權限代號(OP)',
  `fname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '權限名稱',
  `fsort` int(11) NOT NULL DEFAULT 1 COMMENT '排序',
  `enable` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否啟用 (1.啟用 0.不啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='功能(權限)基本資料';

--
-- 傾印資料表的資料 `circulate_function`
--

INSERT INTO `circulate_function` (`id`, `fid`, `fname`, `fsort`, `enable`) VALUES
(1, 'authors', '管理員管理', 1, 1),
(3, 'storeadmin', '商店登入帳號管理', 3, 1),
(5, 'members', '會友資訊', 5, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_group`
--

CREATE TABLE `circulate_group` (
  `id` int(11) NOT NULL COMMENT '群組流水號',
  `gid` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '群組代號',
  `gname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '群組名稱',
  `enable` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否啟用 (1.啟用 0.不啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='群組基本資料';

--
-- 傾印資料表的資料 `circulate_group`
--

INSERT INTO `circulate_group` (`id`, `gid`, `gname`, `enable`) VALUES
(1, '123', '12311', 0);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_groupfunction`
--

CREATE TABLE `circulate_groupfunction` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `gid` int(11) NOT NULL DEFAULT 0 COMMENT '群組流水號',
  `fid` int(11) NOT NULL DEFAULT 0 COMMENT '權限流水號',
  `lastupdate` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改日期',
  `sysadm` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改者(管理員登入帳號)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='記錄群組有那些功能(權限)';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_groupuser`
--

CREATE TABLE `circulate_groupuser` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `gid` int(11) NOT NULL DEFAULT 0 COMMENT '群組流水號',
  `aid` int(11) NOT NULL DEFAULT 0 COMMENT '管理員流水號',
  `lastupdate` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改日期',
  `sysadm` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改者(管理員登入帳號)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='記錄群組有那些管理員';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_ipmanage`
--

CREATE TABLE `circulate_ipmanage` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `storeid` int(11) NOT NULL DEFAULT 0 COMMENT '商店編號',
  `ipsec1` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class A',
  `ipsec2` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class B',
  `ipsec3` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class C',
  `ipsec4` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class D (開始)',
  `ipsec5` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class D (結束)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='service介面IP登入管理';

--
-- 傾印資料表的資料 `circulate_ipmanage`
--

INSERT INTO `circulate_ipmanage` (`id`, `storeid`, `ipsec1`, `ipsec2`, `ipsec3`, `ipsec4`, `ipsec5`) VALUES
(1, 1, 192, 168, 1, 1, 100);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_login_log`
--

CREATE TABLE `circulate_login_log` (
  `log_id` int(11) NOT NULL COMMENT '流水號',
  `log_loginname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '管理員登入帳號',
  `log_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '動作時間',
  `log_op` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '使用的後台管理功能代號',
  `log_op2` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '使用的後台管理功能細項代號 ',
  `log_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '使用的後台管理功能名稱',
  `log_ip` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入的位址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='管理員後台使用記錄';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_permit`
--

CREATE TABLE `circulate_permit` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `aid` int(11) NOT NULL DEFAULT 0 COMMENT '管理員流水號',
  `fid` int(11) NOT NULL DEFAULT 0 COMMENT '權限流水號',
  `lastupdate` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改日期',
  `sysadm` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改者登入帳號'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='記錄管理者有那些功能的使用權限';

--
-- 傾印資料表的資料 `circulate_permit`
--

INSERT INTO `circulate_permit` (`id`, `aid`, `fid`, `lastupdate`, `sysadm`) VALUES
(42, 1, 1, '2012-12-20 14:26:37', 'admin'),
(43, 1, 2, '2012-12-20 14:26:37', 'admin'),
(44, 1, 3, '2012-12-20 14:26:37', 'admin'),
(45, 1, 4, '2012-12-20 14:26:37', 'admin'),
(46, 1, 5, '2012-12-20 14:26:37', 'admin'),
(47, 1, 6, '2012-12-20 14:26:37', 'admin'),
(48, 1, 7, '2012-12-20 14:26:37', 'admin'),
(49, 1, 8, '2012-12-20 14:26:37', 'admin'),
(50, 1, 9, '2012-12-20 14:26:37', 'admin'),
(51, 1, 10, '2012-12-20 14:26:37', 'admin');

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_store`
--

CREATE TABLE `circulate_store` (
  `id` int(8) NOT NULL COMMENT '商店編號',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商店編號',
  `ipmanage` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否控管可登入IP位置(1. 控管 0. 不控管)',
  `enable` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '是否啟用 (1. 啟用 0. 不啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='活動單位部門資料';

--
-- 傾印資料表的資料 `circulate_store`
--

INSERT INTO `circulate_store` (`id`, `name`, `ipmanage`, `enable`) VALUES
(1, '第一網站1', 0, '1');

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_storeadmin`
--

CREATE TABLE `circulate_storeadmin` (
  `sid` int(11) NOT NULL COMMENT '商店Service介面登入流水號',
  `said` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入帳號',
  `sapw` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入密碼',
  `storeid` int(11) NOT NULL DEFAULT 0 COMMENT '商店/組織/單位編號',
  `enable` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否啟用(0. 不啟用 1.啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='商店Service介面登入帳號基本資料';

--
-- 傾印資料表的資料 `circulate_storeadmin`
--

INSERT INTO `circulate_storeadmin` (`sid`, `said`, `sapw`, `storeid`, `enable`) VALUES
(1, 'test', 'test', 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_storeadmin_login`
--

CREATE TABLE `circulate_storeadmin_login` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `loginname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入帳號',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '登入時間',
  `op` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '功能',
  `op2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'op2代號名稱',
  `function` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'OP功能名稱',
  `ip` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入IP位置'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='商店管理帳號使用管理功能記錄';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_storefunction`
--

CREATE TABLE `circulate_storefunction` (
  `id` int(11) NOT NULL COMMENT '商店Service介面管理功能流水號',
  `sfid` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理功能代碼',
  `sfname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理功能名稱',
  `enable` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否啟用(0. 不啟用 1.啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='商店Service介面管理功能基本資料';

--
-- 傾印資料表的資料 `circulate_storefunction`
--

INSERT INTO `circulate_storefunction` (`id`, `sfid`, `sfname`, `enable`) VALUES
(1, 'questionary', '問券管理', 1),
(2, 'eofferitem', '項目管理', 1),
(3, 'rule', '問卷調查管理', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_storepermit`
--

CREATE TABLE `circulate_storepermit` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `sid` int(11) NOT NULL DEFAULT 0 COMMENT '商店Service介面登入流水號',
  `fid` int(11) NOT NULL DEFAULT 0 COMMENT '商店Service介面管理功能流水號',
  `lastmod` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '最後修改時間',
  `admin` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改者'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='商店Service介面權限記錄';

--
-- 傾印資料表的資料 `circulate_storepermit`
--

INSERT INTO `circulate_storepermit` (`id`, `sid`, `fid`, `lastmod`, `admin`) VALUES
(6, 1, 3, '2012-12-20 15:04:24', 'admin');

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_useradmin`
--

CREATE TABLE `circulate_useradmin` (
  `sid` int(11) NOT NULL COMMENT '商店Service介面登入流水號',
  `said` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入帳號',
  `sapw` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入密碼',
  `storeid` int(11) NOT NULL DEFAULT 0 COMMENT '商店/組織/單位編號',
  `enable` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否啟用(0. 不啟用 1.啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Userlogin介面登入帳號基本資料';

--
-- 傾印資料表的資料 `circulate_useradmin`
--

INSERT INTO `circulate_useradmin` (`sid`, `said`, `sapw`, `storeid`, `enable`) VALUES
(1, 'usertest', 'usertest', 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_useradmin_login`
--

CREATE TABLE `circulate_useradmin_login` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `loginname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入帳號',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '登入時間',
  `op` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '功能',
  `ip` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登入IP位置'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Userlogin管理帳號使用管理功能記錄';

--
-- 傾印資料表的資料 `circulate_useradmin_login`
--

INSERT INTO `circulate_useradmin_login` (`id`, `loginname`, `time`, `op`, `ip`) VALUES
(1, 'usertest', '2009-12-07 12:28:52', '登出 / 離開', '192.168.1.61');

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_userfunction`
--

CREATE TABLE `circulate_userfunction` (
  `id` int(11) NOT NULL COMMENT '商店Service介面管理功能流水號',
  `sfid` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理功能代碼',
  `sfname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理功能名稱',
  `enable` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否啟用(0. 不啟用 1.啟用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='userlogin介面管理功能基本資料';

--
-- 傾印資料表的資料 `circulate_userfunction`
--

INSERT INTO `circulate_userfunction` (`id`, `sfid`, `sfname`, `enable`) VALUES
(1, 'questionary', '問券管理', 1),
(2, 'eofferitem', '項目管理', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_userpermit`
--

CREATE TABLE `circulate_userpermit` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `sid` int(11) NOT NULL DEFAULT 0 COMMENT '商店Service介面登入流水號',
  `fid` int(11) NOT NULL DEFAULT 0 COMMENT '商店Service介面管理功能流水號',
  `lastmod` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '最後修改時間',
  `admin` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '修改者'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='商店Service介面權限記錄';

-- --------------------------------------------------------

--
-- 資料表結構 `circulate_user_ipmanage`
--

CREATE TABLE `circulate_user_ipmanage` (
  `id` int(11) NOT NULL COMMENT '流水號',
  `userid` int(11) NOT NULL DEFAULT 0 COMMENT '商店編號',
  `ipsec1` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class A',
  `ipsec2` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class B',
  `ipsec3` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class C',
  `ipsec4` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class D (開始)',
  `ipsec5` int(11) NOT NULL DEFAULT 0 COMMENT 'IP位置 class D (結束)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='userlogin介面IP登入管理';

--
-- 傾印資料表的資料 `circulate_user_ipmanage`
--

INSERT INTO `circulate_user_ipmanage` (`id`, `userid`, `ipsec1`, `ipsec2`, `ipsec3`, `ipsec4`, `ipsec5`) VALUES
(1, 1, 192, 168, 1, 1, 100);

-- --------------------------------------------------------

--
-- 資料表結構 `XXX_circulate_user`
--

CREATE TABLE `XXX_circulate_user` (
  `id` int(11) NOT NULL COMMENT '會員編號',
  `username` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '會員姓名/公司名稱',
  `image` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '會員照片圖檔',
  `useridno` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '身份証號/護照號碼/統一編號',
  `sex` tinyint(1) NOT NULL COMMENT '性別 1:男  0:女',
  `useremail` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'email address',
  `birthday` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '生日',
  `address` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '地址',
  `homephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '家用電話',
  `officephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '公司電話',
  `cellphone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '行動電話',
  `fax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '傳真',
  `intro` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '會員簡介',
  `enable` tinyint(1) NOT NULL COMMENT '是否啟用 1:是 0:否',
  `ipmanage` tinyint(1) NOT NULL DEFAULT 0 COMMENT '登入IP是否控管 1:是 0:否'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='會員基本資料';

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `circulate_authors`
--
ALTER TABLE `circulate_authors`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_authors_ipmanage`
--
ALTER TABLE `circulate_authors_ipmanage`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_customer`
--
ALTER TABLE `circulate_customer`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_customer_group`
--
ALTER TABLE `circulate_customer_group`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_function`
--
ALTER TABLE `circulate_function`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_group`
--
ALTER TABLE `circulate_group`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_groupfunction`
--
ALTER TABLE `circulate_groupfunction`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_groupuser`
--
ALTER TABLE `circulate_groupuser`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_ipmanage`
--
ALTER TABLE `circulate_ipmanage`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_login_log`
--
ALTER TABLE `circulate_login_log`
  ADD PRIMARY KEY (`log_id`);

--
-- 資料表索引 `circulate_permit`
--
ALTER TABLE `circulate_permit`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_store`
--
ALTER TABLE `circulate_store`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_storeadmin`
--
ALTER TABLE `circulate_storeadmin`
  ADD PRIMARY KEY (`sid`);

--
-- 資料表索引 `circulate_storeadmin_login`
--
ALTER TABLE `circulate_storeadmin_login`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_storefunction`
--
ALTER TABLE `circulate_storefunction`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_storepermit`
--
ALTER TABLE `circulate_storepermit`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_useradmin`
--
ALTER TABLE `circulate_useradmin`
  ADD PRIMARY KEY (`sid`);

--
-- 資料表索引 `circulate_useradmin_login`
--
ALTER TABLE `circulate_useradmin_login`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_userfunction`
--
ALTER TABLE `circulate_userfunction`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_userpermit`
--
ALTER TABLE `circulate_userpermit`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `circulate_user_ipmanage`
--
ALTER TABLE `circulate_user_ipmanage`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `XXX_circulate_user`
--
ALTER TABLE `XXX_circulate_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `useremail` (`useremail`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_authors`
--
ALTER TABLE `circulate_authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理員編號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_authors_ipmanage`
--
ALTER TABLE `circulate_authors_ipmanage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_customer`
--
ALTER TABLE `circulate_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK,Auto';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_customer_group`
--
ALTER TABLE `circulate_customer_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK, Auto';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_function`
--
ALTER TABLE `circulate_function`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '權限流水號', AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_group`
--
ALTER TABLE `circulate_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '群組流水號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_groupfunction`
--
ALTER TABLE `circulate_groupfunction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_groupuser`
--
ALTER TABLE `circulate_groupuser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_ipmanage`
--
ALTER TABLE `circulate_ipmanage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_login_log`
--
ALTER TABLE `circulate_login_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_permit`
--
ALTER TABLE `circulate_permit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號', AUTO_INCREMENT=52;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_store`
--
ALTER TABLE `circulate_store`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '商店編號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_storeadmin`
--
ALTER TABLE `circulate_storeadmin`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT COMMENT '商店Service介面登入流水號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_storeadmin_login`
--
ALTER TABLE `circulate_storeadmin_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_storefunction`
--
ALTER TABLE `circulate_storefunction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商店Service介面管理功能流水號', AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_storepermit`
--
ALTER TABLE `circulate_storepermit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號', AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_useradmin`
--
ALTER TABLE `circulate_useradmin`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT COMMENT '商店Service介面登入流水號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_useradmin_login`
--
ALTER TABLE `circulate_useradmin_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_userfunction`
--
ALTER TABLE `circulate_userfunction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商店Service介面管理功能流水號', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_userpermit`
--
ALTER TABLE `circulate_userpermit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `circulate_user_ipmanage`
--
ALTER TABLE `circulate_user_ipmanage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '流水號', AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `XXX_circulate_user`
--
ALTER TABLE `XXX_circulate_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '會員編號';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;