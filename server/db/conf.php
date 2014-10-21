
<?php
/* Connexion à une base ODBC avec l'invocation de pilote */
$dsn = 'mysql:dbname=q;host=10.16.160.241';
$user = 'q_acces';
$password = 'xxxkevin';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

?>
