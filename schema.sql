CREATE TABLE `block` (
  `block_id` decimal(14,0) NOT NULL,
  `block_hash` char(64) NOT NULL,
  `block_hashMerkleRoot` char(64) DEFAULT NULL,
  `block_type` char(64) NOT NULL,
  PRIMARY KEY (`block_id`),
  UNIQUE KEY `block_hash` (`block_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `block_tx` (
  `block_id` decimal(14,0) NOT NULL,
  `tx_id` decimal(26,0) NOT NULL,
  `tx_hash` char(64) NOT NULL,
  PRIMARY KEY (`block_id`,`tx_id`),
  UNIQUE KEY `tx_hash` (`tx_hash`),
  KEY `x_block_tx_tx` (`tx_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `last` (
  `value` varchar(255) NOT NULL,
  `last` int(10) DEFAULT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `txin` (
  `tx_in` int(11) NOT NULL AUTO_INCREMENT,
  `tx_id` decimal(26,0) NOT NULL,
  `tx_source_hash` varchar(255) NOT NULL,
  `txin_pubkey` text,
  `txin_address` varchar(255) DEFAULT NULL,
  `tx_value` float DEFAULT NULL,
  PRIMARY KEY (`tx_in`),
  UNIQUE KEY `tx_in` (`tx_in`)
) ENGINE=InnoDB AUTO_INCREMENT=106517 DEFAULT CHARSET=latin1;
CREATE TABLE `txout` (
  `tx_out` int(11) NOT NULL AUTO_INCREMENT,
  `tx_id` decimal(30,0) NOT NULL,
  `txin_hash` char(64) NOT NULL,
  `txout_value` float NOT NULL,
  `tx_n` decimal(10,0) NOT NULL,
  `address` text NOT NULL,
  PRIMARY KEY (`tx_out`),
  UNIQUE KEY `tx_out` (`tx_out`)
) ENGINE=InnoDB AUTO_INCREMENT=117552 DEFAULT CHARSET=latin1;

