<?php
/*LOGIN*/

$account = $_POST['username'];
$passw = $_POST['password'];

// $account = 'drc';
// $passw = 'pzy';


require_once 'db_functions.php';
$db = new DB_funciones();

	if($db->login($account,$passw)){

	}else{
	}

echo json_encode($resultado);

?>
