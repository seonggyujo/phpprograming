<?php
// 데이터베이스 연결
$host = "127.0.0.1";
$user = "root";
$pw = "SgTest123!";
$dbName = "sample01_db";
$port = 3307;

$conn = mysqli_connect($host, $user, $pw, $dbName, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully<br>";

// comment 테이블 생성
$sql = "CREATE TABLE IF NOT EXISTS comment (
    commentNum INT AUTO_INCREMENT PRIMARY KEY,
    boardNum INT NOT NULL,
    writer VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    regDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    ipAddr VARCHAR(50),
    FOREIGN KEY (boardNum) REFERENCES board(boardNum) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "Table 'comment' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
echo "<br>Comment table setup completed!<br>";
echo "<a href='board_list.php'>게시판으로 이동</a>";
?>
