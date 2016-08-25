
<?php
error_reporting(E_ALL || ~E_NOTICE);
header("Content-type:text/html;charset=utf-8"); 
//借出，根据EPC返回6条信息
$EPC = $_POST['EPC'];
// $EPC = "3000101000010000001D000034EF";

require_once 'db_functions.php';

$db = new DB_functions();

echo $db->getInfo($EPC);

?>