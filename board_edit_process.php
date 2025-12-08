<?php
// 데이터베이스 연결
require_once 'db_config.php';

// POST 데이터 받기
$boardNum = isset($_POST['boardNum']) ? (int)$_POST['boardNum'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$writer = isset($_POST['writer']) ? trim($_POST['writer']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$oldFileName = isset($_POST['oldFileName']) ? $_POST['oldFileName'] : '';

// 유효성 검사
if ($boardNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board_list.php';</script>";
    exit;
}

if ($title == '' || $writer == '' || $content == '') {
    echo "<script>alert('모든 필수 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 파일 업로드 처리
$fileName = $oldFileName; // 기본값은 기존 파일명

if (isset($_FILES['upload']) && $_FILES['upload']['error'] == 0) {
    $uploadDir = "img/";
    $originalName = $_FILES['upload']['name'];

    // 파일 확장자 추출
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // 허용할 확장자
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'hwp', 'doc', 'docx'];

    if (in_array($extension, $allowedExtensions)) {
        // 파일명 중복 방지를 위해 타임스탬프 추가
        $newFileName = pathinfo($originalName, PATHINFO_FILENAME) . "_" . time() . "." . $extension;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadPath)) {
            // 기존 파일 삭제
            if ($oldFileName != '' && file_exists($uploadDir . $oldFileName)) {
                unlink($uploadDir . $oldFileName);
            }
            $fileName = $newFileName;
        } else {
            echo "<script>alert('파일 업로드 중 오류가 발생했습니다.'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('허용되지 않는 파일 형식입니다.'); history.back();</script>";
        exit;
    }
}

// 데이터베이스 업데이트
$stmt = mysqli_prepare($conn, "UPDATE board SET title=?, writer=?, content=?, fileName=? WHERE boardNum=?");
mysqli_stmt_bind_param($stmt, "ssssi", $title, $writer, $content, $fileName, $boardNum);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('게시글이 수정되었습니다.'); location.href='board_view.php?boardNum=$boardNum';</script>";
} else {
    echo "<script>alert('게시글 수정 중 오류가 발생했습니다.'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
