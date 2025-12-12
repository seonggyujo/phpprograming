<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?require_login=1");
    exit;
}

// 데이터베이스 연결
require_once 'db_config.php';

// POST 데이터 받기
$boardNum = isset($_POST['boardNum']) ? (int)$_POST['boardNum'] : 0;
$writer = $_SESSION['userName']; // 세션에서 작성자 가져오기
$memberNum = $_SESSION['memberNum'];
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$ipAddr = $_SERVER['REMOTE_ADDR'];

// 유효성 검사
if ($boardNum == 0 || $writer == '' || $content == '') {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 댓글 저장
$stmt = mysqli_prepare($conn, "INSERT INTO comment (boardNum, writer, content, ipAddr) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isss", $boardNum, $writer, $content, $ipAddr);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('댓글이 등록되었습니다.'); location.href='board_view.php?boardNum=$boardNum';</script>";
} else {
    echo "<script>alert('댓글 등록 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
