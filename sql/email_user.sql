CREATE TABLE `email_user` (
  `email_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  KEY `fk_email_user_email` (`email_id`),
  KEY `fk_email_user_user` (`user_id`),
  CONSTRAINT `fk_email_user_email` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`),
  CONSTRAINT `fk_email_user_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
