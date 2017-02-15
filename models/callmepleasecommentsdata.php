<?php
	class callmepleasecommentsdata extends ObjectModel{

	
/*
  $SQL[] =
  "CREATE TABLE `"._DB_PREFIX_."callme_please_comments` ("
  	."`comment_ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,"
  	."`customer_phone` varchar(20) CHARACTER SET utf8,"
  	."`session_id` varchar(40) CHARACTER SET utf8,"
  	."`ip` varchar(46) CHARACTER SET utf8,"
  	."`comment` varchar(4096) CHARACTER SET utf8 NOT NULL"
  .") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			
			
*/
		/** @var string Name */
		// public $ID_RATE; -> ya tenemos objectModel->id para estos menesteres, es más, es el que hay que usar para borrar.
		public $comment_ID;
		public $customer_phone;
		public $session_id;
		public $ip;
		public $comment;

		/**
		 * @see ObjectModel::$definition
		 */
		public static $definition = array(
			'table' => 'callme_please_comments',
			'primary' => 'comment_ID',
			'multilang' => false,
			'fields' => array(
				// Lang fields
				'customer_phone' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 20),	
				'session_id' => 		array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 40),
				'ip' => 				array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 46),
				'comment' => 			array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 4096),		
			)
		);
        
	}
?>