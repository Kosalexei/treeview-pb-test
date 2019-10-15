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

		$db = Database::getInstance();

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
					if($treeview->add_element( $dir_id, $element_name, $element_type )) {
						RequestSender::success( $treeview->get_node($dir_id) );
					} else {
						return new Error( "Не удалось добавить элемент." );
					}
					
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Возвращаем ошибку
		RequestSender::badRequest();
	}
