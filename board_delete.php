<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?require_login=1");
    exit;
}

// 데이터베이스 연결
require_once 'db_config.php';

// 게시글 번호 받기
$boardNum = isset($_GET['boardNum']) ? (int)$_GET['boardNum'] : 0;

if ($boardNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board_list.php';</script>";
    exit;
}

// 삭제 전 게시글 정보 조회
$sql = "SELECT fileName, memberNum FROM board WHERE boardNum = $boardNum";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('게시글을 찾을 수 없습니다.'); location.href='board_list.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);
$fileName = $row['fileName'];

// 본인 글인지 확인 (관리자는 모든 글 삭제 가능)
$postMemberNum = isset($row['memberNum']) ? $row['memberNum'] : null;
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (!$isAdmin && $_SESSION['memberNum'] != $postMemberNum) {
    echo "<script>alert('본인의 글만 삭제할 수 있습니다.'); history.back();</script>";
    exit;
}

// 게시글 삭제
$deleteSql = "DELETE FROM board WHERE boardNum = $boardNum";

if (mysqli_query($conn, $deleteSql)) {
    // 첨부파일이 있으면 파일도 삭제
    if ($fileName != null && $fileName != '') {
        $filePath = "img/" . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    echo "<script>alert('게시글이 삭제되었습니다.'); location.href='board_list.php';</script>";
} else {
    echo "<script>alert('게시글 삭제 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_close($conn);
?>
