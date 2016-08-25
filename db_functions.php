<?php
 
class DB_functions {
 
    private $db;
 
    // constructor

    function __construct() {
        require_once 'db_connect.php';
        // connecting to database

        $this->db = new DB_Connect();
        $this->db->connect();
        mysql_query("set names utf8;"); //**设置字符集***
        date_default_timezone_set(PRC);
    }
 
    // destructor
    function __destruct() {
 
    }

    /**
     * agregar nuevo usuario
     */
    public function adduser($username, $password) {

    $result = mysql_query("INSERT INTO usuarios(username,passw) VALUES('$username', '$password')");
        // check for successful store

        if ($result) {

            return true;

        } else {

            return false;
        }

    }
 
 
     /**
     * Verificar si el usuario ya existe por el username
     */

    public function isuserexist($username) {

        $result = mysql_query("SELECT username from usuarios WHERE username = '$username'");

        $num_rows = mysql_num_rows($result); //numero de filas retornadas

        if ($num_rows > 0) {

            // el usuario existe 

            return true;
        } else {
            // no existe
            return false;
        }
    }
 
   /*
    *登录功能
    */
    public function login($account,$passw){
    $sql = "SELECT COUNT(*) FROM account WHERE Account='$account' AND password='$passw';";
    $result=mysql_query($sql);
    $count = mysql_fetch_row($result);

    /*como el usuario debe ser unico cuenta el numero de ocurrencias con esos datos*/


        if ($count[0]==0){

        return true;

        }else{

        return false;

        }
    }

    /*
    *   接收功能，土地证和房产证
        需要修改
        archive
        arcivetrace
        possessor
    */
    public function receive_2($landName,$houseName,$ownerName,$IdNumber,$ManagerId,$landEPC,$houseEPC,$receiveTime){

        $possessorID = 0;
        //判断持有人是否已在记录表中
        $sql = "select count(*) from possessor where IDcard = '$IdNumber';";
        $result = mysql_query($sql);
        $count = mysql_fetch_row($result);
        if($count[0]==0){//没有在记录中
            $sql = "insert into possessor value(null,'$ownerName','$IdNumber');";
            // $this->db->log($sql);
            mysql_query($sql);
            $this->db->log("该持有人没有在possessor记录中，插入新持有人信息");
        }
        else{//在记录中
            $this->db->log("该持有人在记录中，不需要插入持有人信息记录");
        }

        $sql = "select ID from possessor where IDcard = '$IdNumber';";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);
        $possessorID = $row['ID'];
        $this->db->log("持有人ID:".$possessorID);

        //查看客户经理ID是否注册过
        $sql = "select * from account where ID = $ManagerId;";
        $result = mysql_query($sql);
        $count = mysql_fetch_row($result);
        if($count[0]==0){//ID输入错误，或者该用户经理没有注册过
            $this->db->log("ID输入错误，或者该客户经理没有注册过");
            return -1;
        }
        $this->db->log("客户经理ID:$ManagerId 正确");
        //开始一个事务
        mysql_query("BEGIN"); //或者mysql_query("START TRANSACTION");
        //插入权证信息（土地证）
        $sql = "insert into archives value('20160817001','$landName','$landEPC',0,0,null,'$receiveTime',$possessorID,$ManagerId,'20160817002');";
        // $this->db->log($sql);
        //插入权证信息（房产证）
        $sql2 = "insert into archives value('20160817002','$houseName','$houseEPC',1,0,null,'$receiveTime',$possessorID,$ManagerId,'20160817001');";
        // $this->db->log($sql2);
        $sql3 = "insert into archivetrace value(null,'20160817001',0,'$receiveTime',$ManagerId);";
        $sql4 = "insert into archivetrace value(null,'20160817002',0,'$receiveTime',$ManagerId);";

        $res = mysql_query($sql);
        $res1 = mysql_query($sql2); 
        $res2 = mysql_query($sql3);
        $res3 = mysql_query($sql4);
        if($res && $res1 && $res2 && $res3){
            mysql_query("COMMIT");
            $this->db->log("接收成功提交成功，插入archive表中两条记录,插入archivetrace中两条记录");
        }
        else{
        mysql_query("ROLLBACK");
        //失败，原因可能是权证编号不能重复，编号由函数生成，理论上不会重复
        //另一个原因就是，EPC编号不能重复，有可能标签已经被用过，没有被及时销毁，或者扫描到其他的标签，所以要提示扫描时避免其他干扰标签
        $this->db->log('数据回滚，EPC编号不能重复');
        return -2;
        }
    }

    /**
    *   合一证接收函数
    */
    public function receive_1($Name,$ownerName,$IdNumber,$ManagerId,$EPC,$receiveTime){
         $possessorID = 0;
        //判断持有人是否已在记录表中
        $sql = "select count(*) from possessor where IDcard = '$IdNumber';";
        $result = mysql_query($sql);
        $count = mysql_fetch_row($result);
        if($count[0]==0){//没有在记录中
            $sql = "insert into possessor value(null,'$ownerName','$IdNumber');";
            // $this->db->log($sql);
            mysql_query($sql);
            $this->db->log("该持有人没有在possessor记录中，插入新持有人信息");
        }
        else{//在记录中
            $this->db->log("该持有人在记录中，不需要插入持有人信息记录");
        }

        $sql = "select ID from possessor where IDcard = '$IdNumber';";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);
        $possessorID = $row['ID'];
        $this->db->log("持有人ID:".$possessorID);

        //查看客户经理ID是否注册过
        $sql = "select * from account where ID = $ManagerId;";
        $result = mysql_query($sql);
        $count = mysql_fetch_row($result);
        if($count[0]==0){//ID输入错误，或者该用户经理没有注册过
            $this->db->log("ID输入错误，或者该客户经理没有注册过");
            return -1;
        }
        $this->db->log("客户经理ID:$ManagerId 正确");

        //开始一个事务
        mysql_query("BEGIN"); //或者mysql_query("START TRANSACTION");
        //插入权证信息（合一证）
        $sql = "insert into archives value('20160818001','$Name','$EPC',2,0,null,'$receiveTime',$possessorID,$ManagerId,null);";
        // $this->db->log($sql);

        $sql2 = "insert into archivetrace value(null,'20160818001',0,'$receiveTime',$ManagerId);";
        // $this->db->log($sql2);
        $res = mysql_query($sql);
        $res2 = mysql_query($sql2);
        if($res && $res2){
            mysql_query("COMMIT");
            $this->db->log("接收成功提交成功，插入archive表中一条记录");
        }
        else{
        mysql_query("ROLLBACK");
        //失败，原因可能是权证编号不能重复，编号由函数生成，理论上不会重复
        //另一个原因就是，EPC编号不能重复，有可能标签已经被用过，没有被及时销毁，或者扫描到其他的标签，所以要提示扫描时避免其他干扰标签
        $this->db->log('数据回滚，EPC编号不能重复');
        return -2;
        }
    }
    //权证借出处理函数
    public function loan($EPC){
        //同事修改两张表
        $time=date('Y-m-d');

        $sql = "select * from archives where TagNum = '$EPC'";
        // $this->db->log($sql);
        $raw = mysql_fetch_array(mysql_query($sql));
        if(!$raw){
            $this->db->log("EPC error");
            return -1;//EPC error
        }
        $status = $raw['StatusCode'];
        if($status == 1){
            $this->db->log("该权证已经借出");
            return -2;//该权证已经借出
        }
        $archiveID = $raw['ID'];
        $CustomerManagerID = $raw['CustomerManagerID'];
        mysql_query("BEGIN");

        $sql = "insert into archivetrace value(null,'$archiveID',1,'$time',$CustomerManagerID)";
        $sql2 = "update archives set statusCode = 1 where TagNum = '$EPC'";

        $this->db->log($sql.$sql2);
        $res1 = mysql_query($sql);
        $res2 = mysql_query($sql2);

        if($res1 && $res2){
            mysql_query("COMMIT");
            $this->db->log("借出成功");
        }else{
            mysql_query("ROLLBACK");
            $this->db->log("数据回滚，借出失败");
            return -3;
        }
        return 0;
    }
    //权证销毁处理函数
    public function back($EPC){
        
        
        $time=date('Y-m-d');

        $sql = "select * from archives where TagNum = '$EPC'";
        // $this->db->log($sql);
        $raw = mysql_fetch_array(mysql_query($sql));
        if(!$raw){
            $this->db->log("EPC error");
            return -1;//EPC error
        }
        $status = $raw['StatusCode'];
        if($status == 0){
            $this->db->log("该权证在库，无需归还");
            return -2;
        }else if($status == 2){
            $this->db->log("该权证已被销毁，不能归还");
            return -3;
        }
        $archiveID = $raw['ID'];
        $name = $raw['Name'];
        $typeCode = $raw['typeCode'];
        $CustomerManagerID = $raw['CustomerManagerID'];
        $possessorID = $raw['possessorID'];

        mysql_query("BEGIN");

        $sql = "insert into archivetrace value(null,'$archiveID',2,'$time',$CustomerManagerID)";
        $sql2 = "update archives set statusCode = 0 where TagNum = '$EPC'";

        $this->db->log($sql.$sql2);
        $res1 = mysql_query($sql);
        $res2 = mysql_query($sql2);

        if($res1 && $res2){
            mysql_query("COMMIT");
            $this->db->log("归还成功");
        }else{
            mysql_query("ROLLBACK");
            $this->db->log("数据回滚，归还失败");
            return -3;
        }
        return 0;
    }

    public function destroy($EPC){
        $info = json_decode($this->getInfo($EPC));
        //删除archives
        //插入archiveshistory archivetrace
        if($info->anotherArchiveID == ''){
            $anotherArchiveID = 'null';
        }
        mysql_query("BEGIN");
        $sql = "insert into archiveshistory value('$info->ID','$info->name',$info->TypeCode,'$info->possessorName',$info->managerID,$anotherArchiveID);";
        $this->db->log($sql);
        $time=date('Y-m-d');
        $sql2 = "insert into archivetrace value(null,'$info->ID',3,'$time',$info->managerID);";
        $this->db->log($sql2);
        $sql3 = "delete from archives where ID = '$info->ID'";
        $this->db->log($sql3);
        $ret = mysql_query($sql);
        $ret2 = mysql_query($sql2);
        $ret3 = mysql_query($sql3);
        if($ret && $ret2 && $ret3){
            mysql_query("COMMIT");
            return 0;
        }else{
            mysql_query("ROLLBACK");
            return -1;
        }


    }

    public function getInfo($EPC){

        $sql = "select * from archives where TagNum = '$EPC';";
        // $db->log($sql);
        $result = mysql_query($sql);

        $raw = mysql_fetch_array($result);
        if(!$raw){
            $re['errorCode'] = -1;
            $re['errorInfo'] = "EPC error";
        }else{
            $Name = $raw['Name'];
            $re['name'] = $Name;
            $re['ID'] = $raw['ID'];
            $re['TypeCode'] = $raw['TypeCode'];
            $re['anotherArchiveID'] = $raw['AnotherArchiveID'];
            $statusCode = $raw['StatusCode'];
            //查表获取权证状态
            $sql = "select * from statusdict where Code = $statusCode;";
            $result = mysql_query($sql);
            $statusRaw = mysql_fetch_array($result);

            $re['status'] = $statusRaw['Type'];

            $managerID = $raw['CustomerManagerID'];
            $re['managerID'] = $managerID;
            //查表获取客户经理名字
            $sql = "select * from account where ID = $managerID;";
            $accountResult = mysql_query($sql);
            $accountRaw = mysql_fetch_array($accountResult);

            $re['managerName'] = $accountRaw['Name'];


            //获取所有人姓名
            $possessorID = $raw['PossessorID'];
            $sql = "select * from possessor where ID = $possessorID";
            $possessorResult = mysql_query($sql);
            $possessorRaw = mysql_fetch_array($possessorResult);
            $re['possessorName'] = $possessorRaw['Name'];
            $re['possessorIDCard'] = $possessorRaw['IDCard'];

            $re['createdTime'] = $raw['CreatedTime'];

            $re['errorCode'] = 0;
        }

        //解决json_encode 中文乱码问题
        foreach ( $re as $key => $value ) {  
                $re[$key] = urlencode ( $value );  
        } 

        return urldecode(json_encode($re));
                return json_encode($re);
        }
}
?>
