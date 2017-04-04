CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `password` char(60) NOT NULL,
  `place_study` varchar(200) DEFAULT NULL,
  `dietary` varchar(500) DEFAULT NULL,
  `shirt_size` varchar(25) DEFAULT NULL,
  `date_birth` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;