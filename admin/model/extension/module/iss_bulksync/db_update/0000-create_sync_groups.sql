CREATE TABLE `iss_sync_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `sync_id` int(11) DEFAULT NULL,
  `category_path` varchar(300) DEFAULT NULL,
  `category_lvl1` varchar(100) DEFAULT NULL,
  `category_lvl2` varchar(100) DEFAULT NULL,
  `category_lvl3` varchar(100) DEFAULT NULL,
  `total_products` int(11) DEFAULT NULL,
  `comission` float DEFAULT NULL,
  `retail_comission` float DEFAULT NULL,
  `destination_categories` varchar(445) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `category_path_UNIQUE` (`category_path`,`sync_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;