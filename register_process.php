<?php
session_start();
require_once 'db_config.php';

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

// 입력값 받기
$userId = trim($_POST['userId'] ?? '');
$userPw = $_POST['userPw'] ?? '';
$userPwConfirm = $_POST['userPwConfirm'] ?? '';
$userName = trim($_POST['userName'] ?? '');
$email = trim($_POST['email'] ?? '');

// 필수 항목 검증
if (empty($userId) || empty($userPw) || empty($userName)) {
    header("Location: register.php?error=empty");
    exit;
}

// 비밀번호 확인
if ($userPw !== $userPwConfirm) {
    header("Location: register.php?error=password");
    exit;
}

// 아이디 중복 확인
$checkSql = "SELECT memberNum FROM member WHERE userId = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "s", $userId);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    mysqli_stmt_close($checkStmt);
    header("Location: register.php?error=duplicate");
    exit;
}
mysqli_stmt_close($checkStmt);

// 비밀번호 암호화
$hashedPw = password_hash($userPw, PASSWORD_DEFAULT);

// 회원 등록
$sql = "INSERT INTO member (userId, userPw, userName, email) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $userId, $hashedPw, $userName, $email);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // 회원가입 성공 - 로그인 페이지로 이동
    header("Location: login.php?registered=1");
    exit;
} else {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: register.php?error=unknown");
    exit;
}
?>
