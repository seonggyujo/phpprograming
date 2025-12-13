<?php
session_start();
// 데이터베이스 연결
require_once 'db_config.php';

// 로그인 체크
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?require_login=1");
    exit;
}

// 댓글 번호와 게시글 번호 받기
$commentNum = isset($_GET['commentNum']) ? (int)$_GET['commentNum'] : 0;
$boardNum = isset($_GET['boardNum']) ? (int)$_GET['boardNum'] : 0;

if ($commentNum == 0 || $boardNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board_list.php';</script>";
    exit;
}

// 댓글 작성자 확인
$checkSql = "SELECT writer FROM comment WHERE commentNum = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "i", $commentNum);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$checkRow = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

if (!$checkRow) {
    echo "<script>alert('댓글을 찾을 수 없습니다.'); location.href='board_view.php?boardNum=$boardNum';</script>";
    exit;
}

// 본인 댓글인지 확인 (관리자는 모든 댓글 삭제 가능)
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isOwner = $checkRow['writer'] === $_SESSION['userName'];

if (!$isAdmin && !$isOwner) {
    echo "<script>alert('본인의 댓글만 삭제할 수 있습니다.'); history.back();</script>";
    exit;
}

// 댓글 삭제
$deleteSql = "DELETE FROM comment WHERE commentNum = ?";
$deleteStmt = mysqli_prepare($conn, $deleteSql);
mysqli_stmt_bind_param($deleteStmt, "i", $commentNum);

if (mysqli_stmt_execute($deleteStmt)) {
    echo "<script>alert('댓글이 삭제되었습니다.'); location.href='board_view.php?boardNum=$boardNum';</script>";
} else {
    echo "<script>alert('댓글 삭제 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_stmt_close($deleteStmt);
mysqli_close($conn);
?>
