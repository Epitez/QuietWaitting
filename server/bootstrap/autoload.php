<?php

	function modelLoader($class_name) {
		$root = $_SERVER['DOCUMENT_ROOT'];
		require_once($root.'/model/'.strtolower($class_name).'.class.php');
	}

	spl_autoload_register('modelLoader');

?>