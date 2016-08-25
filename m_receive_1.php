<?php
//接收，需要填入合一证信息
error_reporting(E_ALL || ~E_NOTICE);

$Name = $_POST['Name'];
$ownerName = $_POST['ownerName'];
$IdNumber = $_POST['IdNumber'];
$ManagerId = $_POST['ManagerId'];
$EPC = $_POST['EPC'];
$receiveTime = $_POST['receiveTime'];
// $Name = 'Name';
// $ownerName = '赵宪栋';
// $IdNumber = '130535199901012223';
// $ManagerId = 123;//客户经理ID可能输入错误或者没有注册过
// $EPC = '154357984378967497549413';
// $receiveTime = '2016-8-18';



require_once 'db_functions.php';
$db = new DB_functions();
$ret = $db->receive_1($Name,$ownerName,$IdNumber,$ManagerId,$EPC,$receiveTime);
switch ($ret) {
	case 0:
		$re['errorCode'] = 0;
		break;
	case -1:
		$re['errorCode'] = -1;
		$re['errorInfo'] = "Maybe input the error Manager ID!";
		break;
	case -2:
		$re['errorCode']= -2;
		$re['errorInfo'] = "EPC error";
		break;
	default:
		# code...
		break;
}

echo json_encode($re);

?>