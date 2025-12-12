<?php
session_start();
require_once 'db_config.php';

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// 입력값 받기
$userId = trim($_POST['userId'] ?? '');
$userPw = $_POST['userPw'] ?? '';

// 필수 항목 검증
if (empty($userId) || empty($userPw)) {
    header("Location: login.php?error=empty");
    exit;
}

// 사용자 조회
$sql = "SELECT memberNum, userId, userPw, userName FROM member WHERE userId = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // 비밀번호 검증
    if (password_verify($userPw, $row['userPw'])) {
        // 로그인 성공 - 세션 설정
        $_SESSION['memberNum'] = $row['memberNum'];
        $_SESSION['userId'] = $row['userId'];
        $_SESSION['userName'] = $row['userName'];

        // 마지막 로그인 시간 업데이트
        $updateSql = "UPDATE member SET lastLogin = NOW() WHERE memberNum = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "i", $row['memberNum']);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        // 게시판으로 이동
        header("Location: board_list.php");
        exit;
    }
}

// 로그인 실패
mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: login.php?error=invalid");
exit;
?>
