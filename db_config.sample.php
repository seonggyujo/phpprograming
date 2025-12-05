<?php
// 데이터베이스 연결 설정 샘플
// 사용 방법:
// 1. 이 파일을 db_config.php로 복사
// 2. 아래 설정값을 자신의 환경에 맞게 수정

$db_config = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => '여기에_비밀번호_입력',  // 자신의 MySQL 비밀번호로 변경
    'database' => 'sample01_db',
    'port' => 3307
);

// 데이터베이스 연결 함수
function getDbConnection() {
    global $db_config;

    $conn = mysqli_connect(
        $db_config['host'],
        $db_config['user'],
        $db_config['password'],
        $db_config['database'],
        $db_config['port']
    );

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}
?>
