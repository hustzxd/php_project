
<?php
error_reporting(E_ALL || ~E_NOTICE);
$EPC = $_POST['EPC'];
// $EPC = "3000101000010000001D000034EF";


require_once 'db_functions.php';
$db = new DB_functions();

$ret = $db->loan($EPC);

switch($ret){
    case 0:
        $re['errorCode'] = 0;
        break;
    case -1:
        $re['errorCode'] = -1;
        break;
    case -2:
        $re['errorCode'] = -2;
        break;
    case -3:
        $re['errorCode'] = -3;
    default:
        break;
}

//解决json_encode 中文乱码问题
foreach ( $re as $key => $value ) {  
        $re[$key] = urlencode ( $value );  
} 

echo urldecode(json_encode($re));

?>