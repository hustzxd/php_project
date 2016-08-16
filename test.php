<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "myDB";

// 创建连接
$conn = new mysqli($servername, $username, $password,$dbname);

// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
echo "连接成功";

// 创建数据库
// $sql = "CREATE DATABASE myDB";
// if ($conn->query($sql) === TRUE) {
//     echo "数据库创建成功";
// } else {
//     echo "Error creating database: " . $conn->error;
// }

// $sql = "INSERT INTO MyGuests (firstname, lastname, email)
// VALUES ('John', 'Doe', 'john@example.com')";

// if ($conn->query($sql) === TRUE) {
//     echo "新记录插入成功";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }

$sql = "select * from myguests;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // 输出每行数据
    while($row = $result->fetch_assoc()) {
        // echo "<br> email: ". $row["email"]. " - Name: ". $row["firstname"]. " " . $row["lastname"];
        echo json_encode($row);
    }
} else {
    echo "0 个结果";
}


?>