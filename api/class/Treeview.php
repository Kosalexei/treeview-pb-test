<?php


	class Treeview {
		private $db;
		public $directories_table_name = "directories";

		public function __construct( Database $db ) {
			$this->db = $db;
		}

		public function add_directory( $parent_id, $name, $description = null ) {
			$this->_check_db();
			$table_name = $this->directories_table_name;

			$_description = $description ? "'$description'" : "NULL";

			$sql = "INSERT INTO `$table_name` (`ID`, `Name`, `Created`, `Modified`, `Description`, `ParentID`) VALUES (NULL, '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, $_description, '$parent_id')";

			return $this->db->query( $sql );
		}

		public function delete_directory( $dir_id ) {
			$this->_check_db();
			$table_name = $this->directories_table_name;

			$sql = "DELETE FROM `$table_name` WHERE ID = '$dir_id'";

			return $this->db->query( $sql );
		}

		private function _check_db() {
			if ( ! $this->db ) {
				die( "Database does not set." );
			}
		}
	}