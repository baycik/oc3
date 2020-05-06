
CREATE TABLE `oc_iss_sync_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `sync_id` int(11) DEFAULT NULL,
  `category_path` varchar(602) DEFAULT NULL,
  `category_lvl1` varchar(200) DEFAULT NULL,
  `category_lvl2` varchar(200) DEFAULT NULL,
  `category_lvl3` varchar(200) DEFAULT NULL,
  `total_products` int(11) DEFAULT NULL,
  `comission` int(11) DEFAULT NULL,
  `retail_comission` int(11) DEFAULT NULL,
  `destination_categories` varchar(445) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `category_path_UNIQUE` (`category_path`,`sync_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;