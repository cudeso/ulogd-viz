-- Create script for additional tables in the ulogd database
--
--  Koen Van Impe
--

CREATE TABLE IF NOT EXISTS `shortcut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortcut` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `params` varchar(250) NOT NULL,
  `insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
