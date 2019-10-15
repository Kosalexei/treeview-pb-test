<?php


	class Treeview {
		private $db;
		public $directories_table_name = "directories";
		public $elements_table_name = "elements";
		public $types_table_name = "types";

		public function __construct( Database $db ) {
			$this->db = $db;
		}

		public function add_directory( $parent_id, $name, $description = null ) {
			$this->_check_db();
			$table_name = $this->directories_table_name;

			$_description = $description ? "'$description'" : "NULL";

			$sql = "INSERT INTO `$table_name` (`ID`, `Name`, `Created`, `Modified`, `Description`, `ParentID`) VALUES (NULL, '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, $_description, '$parent_id')";

			return $this->db->query( $sql );

			if ( $this->db->query( $sql ) === true ) {
				return $this->getNode( $parent_id );
			} else {
				return new Error( "Не удалось добавить директорию." );
			}
		}

		public function get_directories( $id ) {
			$table_name = $this->directories_table_name;

			$sql = "SELECT * from `$table_name` WHERE `ParentID` = $id";

			$result = $this->db->query( $sql );

			return $result->fetch_all(MYSQLI_ASSOC);
		}

		public function get_elements( $id ) {
			$table_name = $this->elements_table_name;

			$sql = "SELECT * from `$table_name` WHERE `DirectoryID` = $id";

			$result = $this->db->query( $sql );

			return $result->fetch_all(MYSQLI_ASSOC);
		}

		public function delete_directories( $ids ) {
			if ( count($ids) === 0 ) {
				return true;
			}

			$this->_check_db();
			$table_name = $this->directories_table_name;

			$ids = $this->_prepare_ids( $ids );

			$sql = "DELETE FROM `$table_name` WHERE (ID) IN ($ids) OR (ParentID) IN ($ids)";

			return $this->db->query( $sql );
		}

		public function delete_elements( $ids ) {
			if ( count($ids) === 0 ) {
				return true;
			}

			$this->_check_db();
			$table_name = $this->elements_table_name;

			$ids = $this->_prepare_ids( $ids );

			$sql = "DELETE FROM `$table_name` WHERE (ID) IN ($ids)";

			return $this->db->query( $sql );
		}

		public function add_element( $dir_id, $name, $type ) {
			$this->_check_db();
			$table_name = $this->elements_table_name;

			$sql = "INSERT INTO `$table_name` (`ID`, `DirectoryID`, `Name`, `Created`, `Modified`, `Type`, `Meta`) VALUES (NULL, '$dir_id', '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$type', NULL)";

			return $this->db->query( $sql );
			if ( $this->db->query( $sql ) === true ) {
				return $this->get_directories( $parent_id );
			} else {
				return new Error( "Не удалось добавить элемент." );
			}
		}

		public function get_types() {
			$this->_check_db();
			$table_name = $this->types_table_name;

			$sql = "SELECT * from `$table_name`";

			$result = $this->db->query( $sql );

			return $result->fetch_all(MYSQLI_ASSOC);
		}

		public function get_node($id) {
			return [
				"dirId" => $id,
				"directories" => $this->get_directories( $id ),
				"elements" => $this->get_elements( $id )
			];
		}

		private function _check_db() {
			if ( ! $this->db ) {
				die( "Database does not set." );
			}
		}

		private function _prepare_ids( $ids ) {
			$ids = array_map(function( $id ) {
				return "(" . $id . ")";
			}, $ids);

			return join(",", $ids);
		}
	}