<?php

	abstract class Rest {
		public static function getFormData( $method ) {
			// GET: данные возвращаем как есть
			if ( $method === 'GET' ) {
				return $_GET;
			}

			if ( $method === 'POST' || $method === 'DELETE' || $method === 'UPDATE' ) {
				$contents = json_decode( file_get_contents( 'php://input' ), true );

				return ! empty( $contents ) ? $contents : $_POST;
			}

			// PUT, PATCH
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
