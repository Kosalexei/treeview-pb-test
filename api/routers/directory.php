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

		// Добавить директорию
		// POST /directory
		if ( $method === 'POST' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$dir_name        = $formData["dir_name"];
				$parent_id       = isset( $formData["parent_id"] ) ? $formData["parent_id"] : 0;
				$dir_description = $formData["dir_description"];

				if ( $dir_name && (int) $parent_id >= 0 ) {
					RequestSender::success( [
						"dirId" => $parent_id,
						"items" => $treeview->add_directory( $parent_id, $dir_name, $dir_description )
					] );
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Добавить директорию
		// GET /directory
		if ( $method === 'GET' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$parent_id = isset( $formData["parent_id"] ) ? $formData["parent_id"] : 0;

				if ( (int) $parent_id >= 0 ) {
					RequestSender::success( [
						"dirId" => $parent_id,
						"items" => $treeview->getNode( $parent_id )
					] );
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Удалить директорию
		// DELETE /directory
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
