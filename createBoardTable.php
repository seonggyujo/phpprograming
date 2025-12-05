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

// board 테이블 생성
$sql = "CREATE TABLE IF NOT EXISTS board (
    boardNum INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    writer VARCHAR(50) NOT NULL,
    content LONGTEXT NOT NULL,
    fileName VARCHAR(200),
    regDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    viewCnt INT DEFAULT 0,
    ipAddr VARCHAR(50)
)";

if (mysqli_query($conn, $sql)) {
    echo "Table 'board' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// 샘플 데이터 삽입
$sampleData = [
    ["PHP 게시판 제작하기", "홍길동", "PHP를 이용한 게시판 제작 예제입니다.\n다양한 기능을 구현해봅시다."],
    ["MySQL 데이터베이스 연동", "김철수", "MySQL과 PHP를 연동하는 방법에 대해 알아봅니다.\nCRUD 작업을 수행할 수 있습니다."],
    ["파일 업로드 기능 구현", "이영희", "파일 업로드 기능을 추가하여\n첨부파일을 관리할 수 있습니다."],
    ["검색 기능 만들기", "박민수", "제목과 내용으로 검색하는 기능을 구현합니다.\n정규표현식을 활용할 수 있습니다."],
    ["페이지네이션 적용하기", "최지영", "게시글이 많을 때 페이지 단위로 나누어 표시합니다.\nLIMIT와 OFFSET을 사용합니다."]
];

foreach ($sampleData as $data) {
    $stmt = mysqli_prepare($conn, "INSERT INTO board (title, writer, content, ipAddr) VALUES (?, ?, ?, ?)");
    $ip = "127.0.0.1";
    mysqli_stmt_bind_param($stmt, "ssss", $data[0], $data[1], $data[2], $ip);

    if (mysqli_stmt_execute($stmt)) {
        echo "Sample data inserted: " . $data[0] . "<br>";
    } else {
        echo "Error inserting data: " . mysqli_error($conn) . "<br>";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
echo "<br>Database setup completed!<br>";
echo "<a href='board_list.php'>게시판으로 이동</a>";
?>
