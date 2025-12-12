<?php
// 회원 테이블 생성 스크립트
require_once 'db_config.php';

// member 테이블 생성
$sql = "CREATE TABLE IF NOT EXISTS member (
    memberNum INT AUTO_INCREMENT PRIMARY KEY COMMENT '회원번호',
    userId VARCHAR(50) NOT NULL UNIQUE COMMENT '아이디',
    userPw VARCHAR(255) NOT NULL COMMENT '비밀번호(암호화)',
    userName VARCHAR(50) NOT NULL COMMENT '이름',
    email VARCHAR(100) COMMENT '이메일',
    regDate DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '가입일시',
    lastLogin DATETIME COMMENT '마지막 로그인'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회원 테이블'";

if (mysqli_query($conn, $sql)) {
    echo "<h2>member 테이블 생성 완료!</h2>";
    echo "<p>회원 테이블이 성공적으로 생성되었습니다.</p>";
} else {
    echo "테이블 생성 실패: " . mysqli_error($conn);
}

// board 테이블에 memberNum 컬럼 추가 (기존 테이블 호환성)
$alterSql = "ALTER TABLE board ADD COLUMN memberNum INT DEFAULT NULL COMMENT '작성자 회원번호'";
if (mysqli_query($conn, $alterSql)) {
    echo "<p>board 테이블에 memberNum 컬럼이 추가되었습니다.</p>";
} else {
    // 이미 존재하면 무시
    if (strpos(mysqli_error($conn), 'Duplicate') !== false) {
        echo "<p>board 테이블에 memberNum 컬럼이 이미 존재합니다.</p>";
    }
}

// 테이블 구조 확인
echo "<h3>테이블 구조:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

$result = mysqli_query($conn, "DESC member");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><a href='register.php'>회원가입 페이지로 이동</a>";
echo " | <a href='login.php'>로그인 페이지로 이동</a>";
echo " | <a href='board_list.php'>게시판으로 이동</a>";

mysqli_close($conn);
?>
