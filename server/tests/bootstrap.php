<?php
    define('BASE_PATH', realpath(dirname(__FILE__).'/..'));
    $_SERVER['DOCUMENT_ROOT'] = BASE_PATH;
    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/autoload.php');
    include($_SERVER['DOCUMENT_ROOT'].'/tests/test_config.php');
?>
