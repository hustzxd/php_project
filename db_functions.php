<?php
 
class DB_funciones {
 
    private $db;
 
    // constructor

    function __construct() {
        require_once 'db_connect.php';
        // connecting to database

        $this->db = new DB_Connect();
        $this->db->connect();

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
  
}
 
?>
