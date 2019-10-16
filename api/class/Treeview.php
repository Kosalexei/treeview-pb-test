<?php

	class Treeview {
		/**
		 * @var Database Экземпляр класса базы данных.
		 */
		private $db;

		/**
		 * @var string Имя таблицы с дерикториями.
		 */
		public $directories_table_name = "directories";

		/**
		 * @var string Имя таблицы с элементами.
		 */
		public $elements_table_name = "elements";

		/**
		 * @var string Имя таблицы с типами записей.
		 */
		public $types_table_name = "types";

		/**
		 * Treeview constructor.
		 *
		 * @param Database $db Экземпляр класса базы данных.
		 */
		public function __construct( Database $db ) {
			$this->db = $db;
		}

		/**
		 * Добавить директорию.
		 *
		 * @param string|number $parent_id Идентификатор родительской директории.
		 * @param string $name Имя директории.
		 * @param string|null $description Описание директории.
		 *
		 * @return boolean
		 */
		public function add_directory( $parent_id, $name, $description = null ) {
			$this->_check_db();
			$table_name = $this->directories_table_name;

			$_description = $description ? "'$description'" : "NULL";

			$sql = "INSERT INTO `$table_name` (`ID`, `Name`, `Created`, `Modified`, `Description`, `ParentID`) VALUES (NULL, '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, $_description, '$parent_id');";

			return $this->db->query( $sql );
		}

		/**
		 * Получить директорию.
		 *
		 * @param string|number $id Идентификатор директории.
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public function get_directories( $id ) {
			try {
				$table_name = $this->directories_table_name;

				$sql = "SELECT * from `$table_name` WHERE `ParentID` = $id;";

				$result = $this->db->query( $sql );

				return $result->fetch_all( MYSQLI_ASSOC );
			} catch ( Exception $e ) {
				throw new Exception( $e );
			}
		}

		/**
		 * Получить элементы.
		 *
		 * @param string|number $id Идентификатор директории.
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public function get_elements( $id ) {
			try {
				$table_name = $this->elements_table_name;

				$sql = "SELECT * from `$table_name` WHERE `DirectoryID` = $id;";

				$result = $this->db->query( $sql );

				return $result->fetch_all( MYSQLI_ASSOC );
			} catch ( Exception $e ) {
				throw new Exception( $e );
			}
		}

		/**
		 * Удалить директории.
		 *
		 * @param array $ids Массив идентификаторов директорий.
		 *
		 * @return bool
		 */
		public function delete_directories( array $ids ) {
			if ( count( $ids ) === 0 ) {
				return true;
			}

			$this->_check_db();
			$table_name = $this->directories_table_name;

			$ids = $this->_prepare_ids( $ids );

			$sql = "DELETE FROM `$table_name` WHERE (ID) IN ($ids) OR (ParentID) IN ($ids);";

			return $this->db->query( $sql );
		}

		/**
		 * Удалить элементы.
		 *
		 * @param array $ids Массив идентификаторов элементов.
		 *
		 * @return bool
		 */
		public function delete_elements( array $ids ) {
			if ( count( $ids ) === 0 ) {
				return true;
			}

			$this->_check_db();
			$table_name = $this->elements_table_name;

			$ids = $this->_prepare_ids( $ids );

			$sql = "DELETE FROM `$table_name` WHERE (ID) IN ($ids);";

			return $this->db->query( $sql );
		}

		/**
		 * Добавить элемент.
		 *
		 * @param string|number $dir_id Идентификатор директории.
		 * @param string $name Имя элемента.
		 * @param string|number $type Тип элемента (указывается номер).
		 *
		 * @return mixed
		 */
		public function add_element( $dir_id, $name, $type ) {
			$this->_check_db();
			$table_name = $this->elements_table_name;

			$sql = "INSERT INTO `$table_name` (`ID`, `DirectoryID`, `Name`, `Created`, `Modified`, `Type`, `Meta`) VALUES (NULL, '$dir_id', '$name', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$type', NULL);";

			return $this->db->query( $sql );
		}

		/**
		 * Получить типы записей.
		 *
		 * @return mixed
		 */
		public function get_types() {
			$this->_check_db();
			$table_name = $this->types_table_name;

			$sql = "SELECT * from `$table_name`;";

			$result = $this->db->query( $sql );

			return $result->fetch_all( MYSQLI_ASSOC );
		}

		/**
		 * Получить ветку (директорию).
		 *
		 * @param string|number $id Идентификатор директории.
		 *
		 * @return array
		 * @throws Exception
		 */
		public function get_node( $id ) {
			return [
				"dirId"       => $id,
				"directories" => $this->get_directories( $id ),
				"elements"    => $this->get_elements( $id )
			];
		}

		/**
		 * Обновить объекты (директории или элементы).
		 *
		 * @param array $ids Массив идентификаторов объектов.
		 * @param array $fields Массив полей, которые нужно обновить.
		 * @param array $data Массив данных.
		 * @param bool $modified Обновлять ли дату модификации записи в базе данных.
		 * @param string $target Что обновлем - directory или element.
		 *
		 * @return mixed
		 */
		public function update( array $ids, array $fields, array $data, $modified = true, $target = "directory" ) {
			$this->_check_db();
			$table_name = $target === "directory" ? $this->directories_table_name : $this->elements_table_name;

			$sql_fields_array = [];

			// Получаем колконки таблицы.
			$columns = $this->db->get_columns( $table_name );

			// Обходим массив полей, которые необходимо заполнить.
			foreach ( $fields as $field ) {
				// Обрабатываем те поля, которые имеются в массиве данных и, если поле есть в таблице.
				if ( isset( $data[ $field ] ) && in_array( $field, $columns ) ) {
					array_push( $sql_fields_array, "`$field`='$data[$field]'" );
				}
			}

			if ( $modified ) {
				array_push( $sql_fields_array, "`Modified`=CURRENT_TIMESTAMP" );
			}

			$sql_fields = join( ", ", $sql_fields_array );

			$sql = "";

			// Формируем запрос.
			foreach ( $ids as $id ) {
				$sql .= "UPDATE `$table_name` SET $sql_fields WHERE `ID` = $id;";
			}

			$result = $this->db->multi_query( $sql );

			// Дожидаемся выполнения запроса
			while ( $this->db->next_result() ) {
				;
			}

			return $result;
		}

		/**
		 * Проверка, имеется ли экземпляр объекта базы данных.
		 */
		private function _check_db() {
			if ( ! $this->db ) {
				die( "Database does not set." );
			}
		}

		/**
		 * Подготовка массива идентификаторов для sql запроса.
		 *
		 * @param array $ids Массив идентификатор.
		 *
		 * @return string
		 */
		private function _prepare_ids( array $ids ) {
			$ids = array_map( function ( $id ) {
				return "(" . $id . ")";
			}, $ids );

			return join( ",", $ids );
		}
	}