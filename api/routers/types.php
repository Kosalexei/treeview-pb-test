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

		// Получить типы
		// GET /types
		if ( $method === 'GET' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$parent_id = isset( $formData["parent_id"] ) ? $formData["parent_id"] : 0;

				if ( (int) $parent_id >= 0 ) {
					RequestSender::success( [
						"items" => $treeview->get_types()
					] );
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Возвращаем ошибку
		RequestSender::badRequest();
	}
