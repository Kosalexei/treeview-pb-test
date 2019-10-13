<?php


	abstract class Rest {
		public static function getFormData( $method ) {
			// GET или POST: данные возвращаем как есть
			if ( $method === 'GET' ) {
				return $_GET;
			}

			if ( $method === 'POST' ) {
				$contents = json_decode( file_get_contents( 'php://input' ), true );

				return ! empty( $contents ) ? $contents : $_POST;
			}

			// PUT, PATCH или DELETE
			$data     = array();
			$exploded = explode( '&', file_get_contents( 'php://input' ) );

			foreach ( $exploded as $pair ) {
				$item = explode( '=', $pair );
				if ( count( $item ) == 2 ) {
					$data[ urldecode( $item[0] ) ] = urldecode( $item[1] );
				}
			}

			return $data;
		}

		public static function urls() {
			// Разбираем url
			$url  = ( isset( $_GET['q'] ) ) ? $_GET['q'] : '';
			$url  = rtrim( $url, '/' );
			$urls = explode( '/', $url );

			return $urls;
		}
	}
