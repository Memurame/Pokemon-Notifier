CREATE TABLE IF NOT EXISTS `chats` (
  `chat_id` varchar(50) NOT NULL,
  `admin` int(11) DEFAULT '0',
  `place` varchar(45) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `alias` varchar(45) DEFAULT NULL,
  `active` int(1) DEFAULT '1',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `notify_iv` (
  `chat_id` varchar(50) NOT NULL,
  `iv_val` int(11) DEFAULT NULL,
  `pokemon_id` int(11) NOT NULL,
  PRIMARY KEY (`chat_id`,`pokemon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `notify_pokemon` (
  `chat_id` varchar(30) CHARACTER SET utf8 NOT NULL,
  `pokemon_id` int(11) NOT NULL,
  PRIMARY KEY (`chat_id`,`pokemon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE  IF NOT EXISTS `pokemonhistory` (
  `encounter_id` varchar(200) NOT NULL,
  `pokemon_id` int(4) NOT NULL,
  `spawnpoint_id` varchar(50) NOT NULL,
  `disappear_time` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1
