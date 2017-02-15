<?php
$SQL = array();
// contiene foreign key a _data con motor inno por lo que respeta restricción referencial: lo borramos primero
$SQL[] = 'DROP TABLE `'._DB_PREFIX_.'callme_please_comments`';
$SQL[] = 'DROP TABLE `'._DB_PREFIX_.'callme_please`';
$SQL[] = 'DROP TABLE `'._DB_PREFIX_.'callme_please_data`';

?>