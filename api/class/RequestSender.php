<?php


	abstract class RequestSender {

		public static function success( array $data = [] ) {
			echo json_encode( array(
				'data'   => $data,
				'status' => 'success',
				'code'   => 200
			), JSON_UNESCAPED_UNICODE );
		}

		public static function badRequest( $message = 'Bad Request' ) {
			header( 'HTTP/1.0 400 Bad Request' );

			self::error( $message, 400 );
		}

		public static function error( $message, $code = 0 ) {
			echo json_encode( array(
				'message' => $message,
				'status'  => 'error',
				'code'    => $code
			), JSON_UNESCAPED_UNICODE );
		}
	}
