CREATE TABLE IF NOT EXISTS `notifylist` (
  `chat_id` varchar(30) NOT NULL,
  `pokemon_id` int(11) NOT NULL,
  PRIMARY KEY (`chat_id`,`pokemon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;