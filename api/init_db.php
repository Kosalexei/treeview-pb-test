<?php

	function init_db() {
		$db = new Database( getenv( "DB_HOST" ), getenv( "DB_USER" ), getenv( "DB_PASSWORD" ) );
		$db->select_db( getenv( "DB_NAME" ) );

		if ( ! $db->table_exists( "Directories" ) ) {
			$sql = "CREATE TABLE `Directories` (
  			`ID` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  			`Name` VARCHAR(30) NOT NULL,
  			`Created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Description` VARCHAR(100) NULL,
  			`ParentID` INT(6) UNSIGNED NOT NULL,
 			PRIMARY KEY(`ID`)
			);";

			$db->query( $sql );
		}

		if ( ! $db->table_exists( "Elements" ) ) {
			$sql = "CREATE TABLE `Elements` (
  			`ID` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  			`DirectoryID` INT(6) UNSIGNED NOT NULL,
  			`Name` VARCHAR(30) NOT NULL,
  			`Created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Meta` JSON NULL,
			PRIMARY KEY(`ID`),
  			CONSTRAINT `FK_Elements2Directories` FOREIGN KEY `FK_Elements2Directories` (`DirectoryID`)
    			REFERENCES `directories` (`ID`)
    			ON DELETE RESTRICT
    			ON UPDATE RESTRICT
			);";

			$db->query( $sql );
		}
	}