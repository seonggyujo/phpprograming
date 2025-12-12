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

// 게시글 조회
$sql = "SELECT * FROM board WHERE boardNum = $boardNum";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('게시글을 찾을 수 없습니다.'); location.href='board_list.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);

// 본인 글인지 확인
$postMemberNum = isset($row['memberNum']) ? $row['memberNum'] : null;
if ($_SESSION['memberNum'] != $postMemberNum) {
    echo "<script>alert('본인의 글만 수정할 수 있습니다.'); history.back();</script>";
    exit;
}

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
        table, th, td {
            border: 1px solid black;
        }
        th {
            background-color: #ddd;
            text-align: right;
            padding: 10px;
            width: 100px;
        }
        td {
            padding: 10px;
        }
        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }
        .current-file {
            color: #666;
            font-size: 14px;
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
    </script>
</head>
<body>
    <h2>게시판 - 수정</h2>

    <form name="editForm" method="post" action="board_edit_process.php" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="hidden" name="boardNum" value="<?php echo $boardNum; ?>">
        <input type="hidden" name="oldFileName" value="<?php echo $fileName; ?>">

        <table width="600">
            <tr>
                <th>제목</th>
                <td><input type="text" name="title" value="<?php echo $title; ?>" style="width:90%;"></td>
            </tr>
            <tr>
                <th>작성자</th>
                <td><input type="text" name="writer" value="<?php echo htmlspecialchars($writer); ?>" readonly style="background:#f0f0f0;"></td>
            </tr>
            <tr>
                <th>내용</th>
                <td><textarea name="content" rows="15" cols="60"><?php echo $content; ?></textarea></td>
            </tr>
            <tr>
                <th>첨부파일</th>
                <td>
                    <?php if ($fileName != null && $fileName != ''): ?>
                        <div class="current-file">
                            현재 파일: <strong><?php echo $fileName; ?></strong>
                            <br><br>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="upload">
                    <br><small style="color: #666;">새 파일을 선택하면 기존 파일이 교체됩니다.</small>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="수정완료" class="btn">
                    <input type="button" value="취소" onclick="history.back()" class="btn">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
<?php
mysqli_close($conn);
?>
