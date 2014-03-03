CREATE TABLE `mt2base_serverstatus` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `port` int(14) DEFAULT NULL,
  `last` int(1) DEFAULT NULL,
  `lastcheck` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);