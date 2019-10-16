<?php

	function init_db() {
		$db = Database::getInstance();
		$db->init( getenv( "DB_HOST" ), getenv( "DB_USER" ), getenv( "DB_PASSWORD" ) );
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

		if ( ! $db->table_exists( "Types" ) ) {
			$sql = "CREATE TABLE `Types` (
  			`ID` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  			`Name` VARCHAR(30) NOT NULL,
  			`Created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY(`ID`)
			);";

			$db->query( $sql );

			$sql = "INSERT INTO Types (`ID`, `Name`, `Created`, `Modified`) VALUES
  					(1, 'Новость', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  					(2, 'Статья', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  					(3, 'Отзыв', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  					(4, 'Комментарий', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
					;";

			$db->query( $sql );

		}

		if ( ! $db->table_exists( "Elements" ) ) {
			$sql = "CREATE TABLE `Elements` (
  			`ID` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  			`DirectoryID` INT(6) UNSIGNED NOT NULL,
  			`Name` VARCHAR(30) NOT NULL,
  			`Created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`Type` INT(6) UNSIGNED NOT NULL,
			`Meta` VARCHAR(255) NULL,
			PRIMARY KEY(`ID`),
  			CONSTRAINT `FK_Elements2Directories` FOREIGN KEY `FK_Elements2Directories` (`DirectoryID`)
    			REFERENCES `Directories` (`ID`)
    			ON DELETE CASCADE
    			ON UPDATE RESTRICT,
            CONSTRAINT `FK_Elements2Types` FOREIGN KEY `FK_Elements2Types` (`Type`)
    			REFERENCES `Types` (`ID`)
    			ON DELETE RESTRICT
    			ON UPDATE RESTRICT
			);";

			$db->query( $sql );
		}
	}