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

// 게시글 조회
$sql = "SELECT * FROM board WHERE boardNum = $boardNum";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('게시글을 찾을 수 없습니다.'); location.href='board_list.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);
$title = $row['title'];
$writer = $row['writer'];
$content = $row['content'];
$fileName = $row['fileName'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판 - 수정</title>
    <style>
        table {
            border-collapse: collapse;
        }
        table td {
            border: 1px solid black;
        }
        td.ttl {
            text-align: right;
            background-color: #ccc;
        }
    </style>
    <script>
        function validateForm() {
            var title = document.forms["editForm"]["title"].value.trim();
            var writer = document.forms["editForm"]["writer"].value.trim();
            var content = document.forms["editForm"]["content"].value.trim();

            if (title == "") {
                alert("제목을 입력해주세요.");
                document.forms["editForm"]["title"].focus();
                return false;
            }

            if (writer == "") {
                alert("작성자를 입력해주세요.");
                document.forms["editForm"]["writer"].focus();
                return false;
            }

            if (content == "") {
                alert("내용을 입력해주세요.");
                document.forms["editForm"]["content"].focus();
                return false;
            }

            if (content.length < 10) {
                alert("내용은 최소 10자 이상 입력해주세요.");
                document.forms["editForm"]["content"].focus();
                return false;
            }

            return true;
        }

        // 이미지 미리보기 함수
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</head>
<body>
    <h2>게시판 - 수정</h2>

    <table>
    <form name="editForm" method="post" action="board_edit_process.php" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="hidden" name="boardNum" value="<?php echo $boardNum; ?>">
        <input type="hidden" name="oldFileName" value="<?php echo $fileName; ?>">
        <tr>
            <td class="ttl">제목:</td>
            <td><input type="text" name="title" value="<?php echo $title; ?>"></td>
        </tr>
        <tr>
            <td class="ttl">작성자:</td>
            <td><input type="text" name="writer" value="<?php echo $writer; ?>"></td>
        </tr>
        <tr>
            <td class="ttl">내용:</td>
            <td><textarea name="content" rows="15" cols="60"><?php echo $content; ?></textarea></td>
        </tr>
        <tr>
            <td class="ttl">첨부이미지:</td>
            <td>
                <?php if ($fileName != null && $fileName != ''): ?>
                    <img id="imagePreview" src="../img/<?php echo $fileName; ?>" style="max-width:400px; border:1px solid #ccc;"><br>
                    현재 파일: <strong><?php echo $fileName; ?></strong><br>
                <?php else: ?>
                    <img id="imagePreview" src="" style="display:none; max-width:400px; border:1px solid #ccc;"><br>
                <?php endif; ?>
                <input type="file" name="upload" accept="image/*" onchange="previewImage(this)">
                <br>새 이미지 선택 시 미리보기가 변경됩니다.
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="수정완료">
                <input type="reset" value="다시작성">
                <input type="button" value="취소" onclick="history.back()">
            </td>
        </tr>
    </form>
    </table>
</body>
</html>
<?php
mysqli_close($conn);
?>
