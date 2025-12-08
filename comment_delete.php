<?php
// 데이터베이스 연결
require_once 'db_config.php';

// 댓글 번호와 게시글 번호 받기
$commentNum = isset($_GET['commentNum']) ? (int)$_GET['commentNum'] : 0;
$boardNum = isset($_GET['boardNum']) ? (int)$_GET['boardNum'] : 0;

if ($commentNum == 0 || $boardNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board_list.php';</script>";
    exit;
}

// 댓글 삭제
$deleteSql = "DELETE FROM comment WHERE commentNum = $commentNum";

if (mysqli_query($conn, $deleteSql)) {
    echo "<script>alert('댓글이 삭제되었습니다.'); location.href='board_view.php?boardNum=$boardNum';</script>";
} else {
    echo "<script>alert('댓글 삭제 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_close($conn);
?>
