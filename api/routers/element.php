<?php


	/**
	 * @param string $method Метод запроса.
	 * @param array $urlData Параметры URL.
	 * @param array $formData Данные запроса.
	 */
	function route( $method, array $urlData, array $formData ) {

		if ( $method === 'OPTIONS' ) {
			return;
		}

		$db = new Database( getenv( "DB_HOST" ), getenv( "DB_USER" ), getenv( "DB_PASSWORD" ) );
		$db->select_db( getenv( "DB_NAME" ) );

		$treeview = new Treeview( $db );

		// Добавить элемент
		// POST /element
		if ( $method === 'POST' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$element_name = $formData["element_name"];
				$dir_id       = isset( $formData["dir_id"] ) ? $formData["dir_id"] : 0;
				$element_type = $formData["element_type"];

				if ( $element_name && (int) $dir_id >= 0 ) {
					var_dump($treeview->add_element( $dir_id, $element_name, $element_type ));
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Удалить элемент
		// DELETE /element
		if ( $method === 'DELETE' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$dir_id = $formData["dir_id"];

				$treeview->delete_directory( $dir_id );
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Возвращаем ошибку
		RequestSender::badRequest();
	}
