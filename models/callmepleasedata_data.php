<?php
	class callmepleasedata_data extends ObjectModel{

	
/*
"CREATE TABLE `"._DB_PREFIX_."callme_please_data` (
  `id_data` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(40) CHARACTER SET utf8 NOT NULL,
  `ip` varchar(46) CHARACTER SET utf8 NOT NULL,
  `ban_method` varchar(1) CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

*/
		/** @var string Name */
		// public $ID_RATE; -> ya tenemos objectModel->id para estos menesteres, es más, es el que hay que usar para borrar.
		public $id_data;
		public $session_id;
		public $ip;
		public $ban_method;

		/**
		 * @see ObjectModel::$definition
		 */
		public static $definition = array(
			'table' => 'callme_please_data',
			'primary' => 'id_data',
			'multilang' => false,
			'fields' => array(
				// Lang fields
				'session_id' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 40),
				'ip' => 			array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 46),
				'ban_method' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 1)
			)
		);
        
	}
?>