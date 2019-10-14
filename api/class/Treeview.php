<?php


	class Treeview {
		private $db;
		public $directories_table_name = "directories";
		public $elements_table_name = "elements";

		public function __construct( Database $db ) {
			$this->db = $db;
		}

		public function add_directory( $parent_id, $name, $description = null ) {
			$this->_check_db();
			$table_name = $this->directories_table_name;

			$_description = $description ? "'$description'" : "NULL";

			$sql = "INSERT INTO `$table_name` (`ID`, `Name`, `Created`, `Modified`, `Description`, `ParentID`) VALUES (NULL, '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, $_description, '$parent_id')";

			if ( $this->db->query( $sql ) === true ) {
				return $this->getNode( $parent_id );
			} else {
				return new Error( "Не удалось добавить директорию." );
			}
		}

		public function getNode( $id ) {
			$table_name = $this->directories_table_name;

			$sql = "SELECT * from `$table_name` WHERE `ParentID` = $id";

			$result = $this->db->query( $sql );
//var_dump($result);
			return $result->fetch_all(MYSQLI_ASSOC);
		}

		public function delete_directory( $dir_id ) {
			$this->_check_db();
			$table_name = $this->directories_table_name;

			$sql = "DELETE FROM `$table_name` WHERE ID = '$dir_id'";

			return $this->db->query( $sql );
		}

		public function add_element( $dir_id, $name, $type ) {
			$this->_check_db();
			$table_name = $this->elements_table_name;

			$sql = "INSERT INTO `$table_name` (`ID`, `DirectoryID`, `Name`, `Created`, `Modified`, `Type`, `Meta`) VALUES (NULL, '$dir_id', '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0', NULL)";

			return $this->db->query( $sql );
		}

		private function _check_db() {
			if ( ! $this->db ) {
				die( "Database does not set." );
			}
		}
	}