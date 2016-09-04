CREATE TABLE IF NOT EXISTS `chats` (
  `chat_id` varchar(50) NOT NULL,
  `admin` int(11) DEFAULT '0',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;