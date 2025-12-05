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

// POST 데이터 받기
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$writer = isset($_POST['writer']) ? trim($_POST['writer']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$ipAddr = $_SERVER['REMOTE_ADDR'];

// 유효성 검사
if ($title == '' || $writer == '' || $content == '') {
    echo "<script>alert('모든 필수 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 이미지 파일 업로드 처리
$fileName = '';
if (isset($_FILES['upload']) && $_FILES['upload']['error'] == 0) {
    $uploadDir = "../img/";

    // 디렉토리가 없으면 생성
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $originalName = $_FILES['upload']['name'];

    // 파일 확장자 추출
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // 이미지 파일만 허용
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extension, $allowedExtensions)) {
        // 파일명 중복 방지를 위해 타임스탬프 추가
        $newFileName = pathinfo($originalName, PATHINFO_FILENAME) . "_" . time() . "." . $extension;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadPath)) {
            $fileName = $newFileName;
        } else {
            echo "<script>alert('이미지 업로드 중 오류가 발생했습니다.'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('이미지 파일만 업로드 가능합니다. (jpg, jpeg, png, gif)'); history.back();</script>";
        exit;
    }
}

// 데이터베이스에 저장
$stmt = mysqli_prepare($conn, "INSERT INTO board (title, writer, content, fileName, ipAddr) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssss", $title, $writer, $content, $fileName, $ipAddr);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('게시글이 등록되었습니다.'); location.href='board_list.php';</script>";
} else {
    echo "<script>alert('게시글 등록 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
