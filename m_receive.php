<?php
//接收，需要填入土地证和房产证信息
error_reporting(E_ALL || ~E_NOTICE);

$landName = $_POST['landName'];
$houseName = $_POST['houseName'];
$ownerName = $_POST['ownerName'];
$IdNumber = $_POST['IdNumber'];
$ManagerId = $_POST['ManagerId'];
$landEPC = $_POST['landEPC'];
$houseEPC = $_POST['houseEPC'];
$receiveTime = $_POST['receiveTime'];
// $landName = 'landName';
// $houseName ='houseName';
// $ownerName = '赵宪栋';
// $IdNumber = '130535199901012223';
// $ManagerId = 123;//客户经理ID可能输入错误或者没有注册过
// $landEPC = '143545435483957843753948';
// $houseEPC = '154357984378967497549493';
// $receiveTime = '2016-8-17';



require_once 'db_functions.php';
$db = new DB_functions();
$ret = $db->receive_2($landName,$houseName,$ownerName,$IdNumber,$ManagerId,$landEPC,$houseEPC,$receiveTime);
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