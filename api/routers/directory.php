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
					if ( $treeview->add_directory( $parent_id, $dir_name, $dir_description ) ) {
						RequestSender::success( $treeview->get_node( $parent_id ) );
					} else {
						throw new Exception( "Не удалось добавить директорию." );
					}
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Получить директорию
		// GET /directory
		if ( $method === 'GET' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$parent_id = isset( $formData["parent_id"] ) ? $formData["parent_id"] : 0;

				if ( (int) $parent_id >= 0 ) {
					RequestSender::success( $treeview->get_node( $parent_id ) );
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
				$ids       = $formData["ids"];
				$parent_id = $formData["parent_id"];

				$directories = isset( $ids["directory"] ) ? $ids["directory"] : [];
				$elements    = isset( $ids["element"] ) ? $ids["element"] : [];

				if ( $treeview->delete_directories( $directories ) === true && $treeview->delete_elements( $elements ) === true ) {
					RequestSender::success( $treeview->get_node( $parent_id ) );
				} else {
					throw new Exception( "Не удалось удалить директорию." );
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Обновить элемент
		// UPDATE /directory
		if ( $method === 'UPDATE' && empty( $urlData ) ) {
			if ( ! $formData ) {
				return;
			}

			try {
				$id        = $formData["id"];
				$parent_id = $formData["parent_id"];
				$name      = $formData["name"];
				$type      = $formData["type"];
				$target    = $formData["target"];

				if ( $treeview->update( $id, $name, $type, $target ) ) {
					RequestSender::success( $treeview->get_node( $parent_id ) );
				} else {
					throw new Exception( "Не удалось обновить элемент." );
				}
			} catch ( Exception $e ) {
				RequestSender::error( $e->getMessage() );
			}

			return;
		}

		// Возвращаем ошибку
		RequestSender::badRequest();
	}
