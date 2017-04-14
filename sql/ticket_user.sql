CREATE TABLE `ticket_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `code` char(16) NOT NULL,
  `emailed` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_ticket_user_user` (`user_id`),
  KEY `fk_ticket_user_ticket` (`ticket_id`),
  CONSTRAINT `fk_ticket_user_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`),
  CONSTRAINT `fk_ticket_user_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

