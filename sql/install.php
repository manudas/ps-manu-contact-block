<?php


$SQL = array();

/* max size of ipv6: 39 chars
 * ban_method:  'n' -> no ban;
				's' -> session id from a session; 
				'i' -> ip; 
 * max session id size: 40 chars
 */
$SQL[] =
"CREATE TABLE `"._DB_PREFIX_."callme_please_data` ("
  ."`id_data` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,"
  ."`session_id` varchar(40) CHARACTER SET utf8 NOT NULL,"
  ."`ip` varchar(46) CHARACTER SET utf8 NOT NULL,"
  ."`ban_method` varchar(1) CHARACTER SET utf8 NOT NULL DEFAULT 'n'"
.") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

/* sessio e ip siempre son únicas */

$SQL[] =
"ALTER TABLE `"._DB_PREFIX_."callme_please_data`
ADD INDEX `session_data_index` (`session_id`),
ADD INDEX `ip_data_index` (`ip`),
ADD INDEX `ban_method_data_index` (`ban_method`)";
/*
  ADD INDEX `combinated_index` (`session_id`,`ip`),
  ADD INDEX `combinated_index1` (`session_id`,`ban_method`),
  ADD INDEX `combinated_index2` (`ban_method`,`ip`),
  ADD INDEX `combinated_index3` (`session_id`,`ip`, `ban_method`),
  ADD INDEX `session_index` (`session_id`),
  ADD INDEX `ip_index` (`ip`);
  ADD INDEX `ban_method_index` (`ban_method`)";
*/

/* inquiry_status:
				u -> unattended; 
				p -> in progress (being attended right now); 
				a -> already attended; 
 * max session id size: 40 chars
 */
$SQL[] =
"CREATE TABLE `"._DB_PREFIX_."callme_please` ("
  ."`inquiry_ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,"
  ."`customer_name` varchar(50) CHARACTER SET utf8 NOT NULL,"
  ."`customer_phone` varchar(20) CHARACTER SET utf8 NOT NULL,"
  ."`customer_country` varchar(25) CHARACTER SET utf8 NOT NULL,"
  ."`origin_url` varchar(50) CHARACTER SET utf8 NOT NULL,"
  ."`inquiry_status` varchar(1) CHARACTER SET utf8 NOT NULL,"
  ."`attended_by` varchar(50) CHARACTER SET utf8," //  employee ID who is attending or will attend this inquiry call
  ."`id_data` int(11) NOT NULL"
.") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$SQL[] =
"ALTER TABLE `"._DB_PREFIX_."callme_please`"
  ."ADD FOREIGN KEY (`id_data`) REFERENCES `"._DB_PREFIX_."callme_please_data`(`id_data`),

  ADD INDEX `customer_name_index` (`customer_name`),
  ADD INDEX `customer_country_index` (`customer_country`),
  ADD INDEX `customer_phone_index` (`customer_phone`),
  ADD INDEX `origin_url_index` (`origin_url`),
  ADD INDEX `inquiry_status_index` (`inquiry_status`);";
/*
  ADD INDEX `combinated_index1` (`customer_country`,`customer_phone`),
  ADD INDEX `combinated_index2` (`customer_country`,`customer_name`),
  ADD INDEX `combinated_index3` (`customer_phone`,`customer_name`),
   
  ADD INDEX `combinated_index4` (`customer_country`,`origin_url`),
  ADD INDEX `combinated_index5` (`origin_url`,`customer_name`),
  ADD INDEX `combinated_index6` (`customer_phone`,`origin_url`),
  
  ADD INDEX `combinated_index1` (`customer_country`,`inquiry_status`),
  ADD INDEX `combinated_index2` (`inquiry_status`,`customer_name`),
  ADD INDEX `combinated_index3` (`customer_phone`,`inquiry_status`),
   
  ADD INDEX `combinated_index4` (`inquiry_status`,`origin_url`),

  
  ADD INDEX `combinated_index7` (`customer_country`,`customer_phone`,`customer_name`),
  ADD INDEX `combinated_index8` (`customer_country`,`customer_phone`,`origin_url`),
  ADD INDEX `combinated_index9` (`customer_country`,`origin_url`,`customer_name`),
  ADD INDEX `combinated_index10` (`origin_url`,`customer_phone`,`customer_name`),
  
  
  ADD INDEX `combinated_index7` (`inquiry_status`,`customer_phone`,`customer_name`),
  ADD INDEX `combinated_index8` (`inquiry_status`,`customer_phone`,`origin_url`),
  ADD INDEX `combinated_index9` (`inquiry_status`,`origin_url`,`customer_name`),
  
  ADD INDEX `combinated_index7` (`customer_country`,`inquiry_status`,`customer_name`),
  ADD INDEX `combinated_index8` (`customer_country`,`customer_phone`,`inquiry_status`),
  ADD INDEX `combinated_index9` (`customer_country`,`origin_url`,`inquiry_status`),
  

  ADD INDEX `combinated_index10` (`origin_url`,`customer_phone`,`inquiry_status`),
  

  ADD INDEX `combinated_index11` (`customer_country`,`customer_phone`,`customer_name`,`origin_url`)
  ADD INDEX `combinated_index11` (`customer_country`,`customer_phone`,`customer_name`,`inquiry_status`)
  ADD INDEX `combinated_index11` (`customer_country`,`customer_phone`,`inquiry_status`,`origin_url`)
  ADD INDEX `combinated_index11` (`customer_country`,`inquiry_status`,`customer_name`,`origin_url`)
  ADD INDEX `combinated_index11` (`inquiry_status`,`customer_phone`,`customer_name`,`origin_url`)
	
	
  ADD INDEX `combinated_index11` (`customer_country`,`customer_phone`,`customer_name`,`origin_url`,`inquiry_status`);";
*/

  
  
  $SQL[] =
  "CREATE TABLE `"._DB_PREFIX_."callme_please_comments` ("
  	."`comment_ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,"
  	."`customer_phone` varchar(20) CHARACTER SET utf8,"
  	."`session_id` varchar(40) CHARACTER SET utf8,"
  	."`ip` varchar(46) CHARACTER SET utf8,"
  	."`comment` varchar(4096) CHARACTER SET utf8 NOT NULL"
  .") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  
  $SQL[] =
  "ALTER TABLE `"._DB_PREFIX_."callme_please_comments`
  		ADD FOREIGN KEY (`customer_phone`) REFERENCES `"._DB_PREFIX_."callme_please`(`customer_phone`),
	  	ADD FOREIGN KEY (`session_id`) REFERENCES `"._DB_PREFIX_."callme_please_data`(`session_id`),
	  	ADD FOREIGN KEY (`ip`) REFERENCES `"._DB_PREFIX_."callme_please_data`(`ip`)";
				
	  	//ADD INDEX `comment_index` (`comment`)";
?>