CREATE TABLE `news` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `date` datetime DEFAULT NULL,
  `author` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
);