<?php
	require_once dirname( __FILE__ ) . "/vendor/autoload.php";
	require_once dirname( __FILE__ ) . "/class/Database.php";
	require_once dirname( __FILE__ ) . "/class/Treeview.php";
	require_once dirname( __FILE__ ) . "/class/Rest.php";
	require_once dirname( __FILE__ ) . "/class/RequestSender.php";
	require_once dirname( __FILE__ ) . "/init_db.php";

	$dotenv = Dotenv\Dotenv::create( __DIR__ );
	$dotenv->load();

	// Определяем метод запроса
	$method = $_SERVER["REQUEST_METHOD"];

	// Получаем данные из тела запроса
	$formData = Rest::getFormData( $method );

	$urls = Rest::urls();

	// Определяем роутер и url data
	$router  = $urls[0];
	$urlData = array_slice( $urls, 1 );

	$routerPath = dirname( __FILE__ ) . "/routers/" . $router . ".php";

	if ( file_exists( $routerPath ) ) {
		init_db();

		// Подключаем файл-роутер и запускаем главную функцию
		include_once dirname( __FILE__ ) . "/routers/" . $router . ".php";

		route( $method, $urlData, $formData );
	} else {
		RequestSender::badRequest();
	}
