CREATE TABLE `iss_sync_list` (
  `sync_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `sync_name` varchar(45) DEFAULT NULL,
  `sync_parser_name` varchar(45) DEFAULT NULL,
  `sync_config` text,
  `sync_last_started` datetime DEFAULT NULL,
  PRIMARY KEY (`sync_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


