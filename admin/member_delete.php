<?php
session_start();
require_once '../db_config.php';

// 관리자 권한 체크
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근할 수 있습니다.'); location.href='../board_list.php';</script>";
    exit;
}

// 회원 번호 받기
$memberNum = isset($_GET['memberNum']) ? (int)$_GET['memberNum'] : 0;

if ($memberNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='member_list.php';</script>";
    exit;
}

// 자기 자신은 삭제 불가
if ($memberNum == $_SESSION['memberNum']) {
    echo "<script>alert('자신의 계정은 삭제할 수 없습니다.'); location.href='member_list.php';</script>";
    exit;
}

// 삭제할 회원 정보 조회
$sql = "SELECT memberNum, userId, userName FROM member WHERE memberNum = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $memberNum);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('회원을 찾을 수 없습니다.'); location.href='member_list.php';</script>";
    exit;
}

$member = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// 트랜잭션 시작
mysqli_begin_transaction($conn);

try {
    // 1. 해당 회원의 게시글 작성자를 "탈퇴회원"으로 변경, memberNum을 NULL로
    $updateBoardSql = "UPDATE board SET writer = '탈퇴회원', memberNum = NULL WHERE memberNum = ?";
    $updateBoardStmt = mysqli_prepare($conn, $updateBoardSql);
    mysqli_stmt_bind_param($updateBoardStmt, "i", $memberNum);
    mysqli_stmt_execute($updateBoardStmt);
    mysqli_stmt_close($updateBoardStmt);
    
    // 2. 해당 회원의 댓글 작성자를 "탈퇴회원"으로 변경
    $updateCommentSql = "UPDATE comment SET writer = '탈퇴회원' WHERE writer = ?";
    $updateCommentStmt = mysqli_prepare($conn, $updateCommentSql);
    mysqli_stmt_bind_param($updateCommentStmt, "s", $member['userName']);
    mysqli_stmt_execute($updateCommentStmt);
    mysqli_stmt_close($updateCommentStmt);
    
    // 3. 회원 삭제
    $deleteSql = "DELETE FROM member WHERE memberNum = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "i", $memberNum);
    mysqli_stmt_execute($deleteStmt);
    mysqli_stmt_close($deleteStmt);
    
    // 커밋
    mysqli_commit($conn);
    
    echo "<script>alert('회원이 삭제되었습니다.\\n해당 회원의 게시글/댓글 작성자는 [탈퇴회원]으로 표시됩니다.'); location.href='member_list.php';</script>";
    
} catch (Exception $e) {
    // 롤백
    mysqli_rollback($conn);
    echo "<script>alert('회원 삭제 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_close($conn);
?>
