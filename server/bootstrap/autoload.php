<?php

	function modelLoader($class_name) {
		$root = $_SERVER['DOCUMENT_ROOT'];
		@include_once($root.'/models/'.strtolower($class_name).'.class.php'); // @ stands for silence warnings
	}
	function helperLoader($class_name) {
		$root = $_SERVER['DOCUMENT_ROOT'];
		@include_once($root.'/helpers/'.strtolower($class_name).'.class.php');
	}

	spl_autoload_register('modelLoader');
	spl_autoload_register('helperLoader');

?>
