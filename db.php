<?php
function getDB() {
    
	$dbhost="ec2-50-19-95-47.compute-1.amazonaws.com";
    $dbuser="yvsdposbfkxkiq";
    $dbpass="a8b115a6c3c9d71ce30c3131e0f46d1f5a2ecc235e1de98ceb411085307a4b07";
    $dbname="dcofgbsdji5n1d";
    $dbConnection = new PDO("pgsql:host=$dbhost;port=5432;dbname=$dbname", $dbuser, $dbpass);	
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
}
?>
