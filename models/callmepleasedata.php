<?php
	class callmepleasedata extends ObjectModel{

	
/*
"CREATE TABLE `"._DB_PREFIX_."callme_please` (
  `inquiry_ID` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `customer_country` varchar(25) CHARACTER SET utf8 NOT NULL,
  `origin_url` varchar(50) CHARACTER SET utf8 NOT NULL,
  `inquiry_status` varchar(1) CHARACTER SET utf8 NOT NULL,
  `attended_by` int(11), // employee who is attending or will attend this call inquiry
  `id_data` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			
			
*/
		/** @var string Name */
		// public $ID_RATE; -> ya tenemos objectModel->id para estos menesteres, es más, es el que hay que usar para borrar.
		public $inquiry_ID;
		public $customer_name;
		public $customer_phone;
		public $customer_country;
		public $origin_url;
		public $inquiry_status;
		public $id_data;
		public $attended_by;

		/**
		 * @see ObjectModel::$definition
		 */
		public static $definition = array(
			'table' => 'callme_please',
			'primary' => 'inquiry_ID',
			'multilang' => false,
			'fields' => array(
				// Lang fields
				'customer_name' => 		array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 50),
				'customer_phone' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 20),
				'customer_country' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 25),
				'origin_url' => 		array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isUrl', 'required' => true, 'size' => 50),
				'inquiry_status' => 	array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 1),
				'id_data' => 			array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isUnsignedInt', 'required' => true, 'size' => 11),
				'attended_by' =>		array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 50),
			)
		);
        
	}
?>