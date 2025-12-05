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

// 게시글 번호 받기
$boardNum = isset($_GET['boardNum']) ? (int)$_GET['boardNum'] : 0;

if ($boardNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board_list.php';</script>";
    exit;
}

// 삭제 전 파일명 조회
$sql = "SELECT fileName FROM board WHERE boardNum = $boardNum";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('게시글을 찾을 수 없습니다.'); location.href='board_list.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);
$fileName = $row['fileName'];

// 게시글 삭제
$deleteSql = "DELETE FROM board WHERE boardNum = $boardNum";

if (mysqli_query($conn, $deleteSql)) {
    // 첨부이미지가 있으면 파일도 삭제
    if ($fileName != null && $fileName != '') {
        $filePath = "../img/" . $fileName;
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
