CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `password` char(60) NOT NULL,
  `place_study` varchar(200) DEFAULT NULL,
  `date_birth` date NOT NULL,
  -- Food
  `dietary` varchar(500) DEFAULT NULL,
  `dinner_choice` varchar(8) DEFAULT NULL,
  `lunch_choice` varchar(8) DEFAULT NULL,
  -- Shirt
  `shirt_size` varchar(8) DEFAULT NULL,
  `shirt_type` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
