<?php

	class Database {
		private $mysqli;
		public $dbname = "";
		private static $instance = null;

		public static function getInstance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __clone() {
		}

		private function __construct() {
		}

		public function init( $host, $user, $password ) {
			$this->mysqli = new mysqli( $host, $user, $password );
			$this->mysqli->set_charset( "utf8" );

			if ( $this->mysqli->connect_error ) {
				die( "Connection failed: " . $this->mysqli->connect_error );
			}
		}

		public function select_db( $dbname ) {
			if ( $this->mysqli->select_db( $dbname ) === false ) {
				$sql = "CREATE DATABASE $dbname";

				if ( $this->mysqli->query( $sql ) === true ) {
					$this->dbname = $dbname;

					return true;
				} else {
					return false;
				}
			} else {
				$this->dbname = $dbname;

				return true;
			}
		}

		public function query( $query ) {
			$this->_check_db_selected();

			return $this->mysqli->query( $query );
		}

		public function multi_query( $query ) {
			$this->_check_db_selected();

			return $this->mysqli->multi_query( $query );
		}

		public function table_exists( $table_name ) {
			return $this->query( "SHOW TABLES LIKE $table_name" );
		}

		public function next_result() {
			$this->mysqli->next_result();
		}

		public function get_columns( $table_name ) {
			$this->_check_db_selected();

			$sql = "SHOW COLUMNS FROM `$table_name`";

			$result = $this->query( $sql );

			$columns = $result->fetch_all( MYSQLI_ASSOC );

			if ( $columns ) {
				return array_map( function ( $column ) {
					return $column["Field"];
				}, $columns );
			} else {
				return [];
			}
		}

		private function _check_db_selected() {
			if ( ! $this->dbname ) {
				die( "Database not selected." );
			}
		}
	}