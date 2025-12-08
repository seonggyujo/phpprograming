<?php
// 데이터베이스 연결
require_once 'db_config.php';

// 게시글 번호 받기
$boardNum = isset($_GET['boardNum']) ? (int)$_GET['boardNum'] : 0;

if ($boardNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board_list.php';</script>";
    exit;
}

// 조회수 증가
$updateSql = "UPDATE board SET viewCnt = viewCnt + 1 WHERE boardNum = $boardNum";
mysqli_query($conn, $updateSql);

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
$regDate = $row['regDate'];
$viewCnt = $row['viewCnt'];

// 내용의 줄바꿈을 <br>로 변환
$content = nl2br($content);

// 댓글 조회
$commentSql = "SELECT * FROM comment WHERE boardNum = $boardNum ORDER BY commentNum ASC";
$commentResult = mysqli_query($conn, $commentSql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판 - 상세보기</title>
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
        }
        td {
            padding: 10px;
        }
        .content-cell {
            min-height: 200px;
            vertical-align: top;
        }
        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }
        .button-group {
            margin-top: 10px;
        }
        .comment-table {
            margin-top: 20px;
        }
        .comment-form {
            margin-top: 10px;
        }
    </style>
    <script>
        function confirmDelete() {
            if (confirm("정말로 삭제하시겠습니까?")) {
                location.href = "board_delete.php?boardNum=<?php echo $boardNum; ?>";
            }
        }

        function confirmCommentDelete(commentNum) {
            if (confirm("댓글을 삭제하시겠습니까?")) {
                location.href = "comment_delete.php?commentNum=" + commentNum + "&boardNum=<?php echo $boardNum; ?>";
            }
        }

        function validateComment() {
            var writer = document.forms["commentForm"]["writer"].value.trim();
            var content = document.forms["commentForm"]["content"].value.trim();

            if (writer == "") {
                alert("작성자를 입력해주세요.");
                document.forms["commentForm"]["writer"].focus();
                return false;
            }

            if (content == "") {
                alert("댓글 내용을 입력해주세요.");
                document.forms["commentForm"]["content"].focus();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <h2>게시판 - 상세보기</h2>

    <table width="600">
        <tr>
            <th width="100">제목</th>
            <td><?php echo $title; ?></td>
        </tr>
        <tr>
            <th>작성자</th>
            <td><?php echo $writer; ?></td>
        </tr>
        <tr>
            <th>작성일</th>
            <td><?php echo $regDate; ?></td>
        </tr>
        <tr>
            <th>조회수</th>
            <td><?php echo $viewCnt; ?></td>
        </tr>
        <tr>
            <th>내용</th>
            <td class="content-cell"><?php echo $content; ?></td>
        </tr>
        <?php if ($fileName != null && $fileName != ''): ?>
        <tr>
            <th>첨부파일</th>
            <td>
                <a href="img/<?php echo $fileName; ?>" download><?php echo $fileName; ?></a>
                <br><br>
                <img src="img/<?php echo $fileName; ?>" style="max-width:400px;">
            </td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="button-group">
        <input type="button" value="수정" onclick="location.href='board_edit.php?boardNum=<?php echo $boardNum; ?>'" class="btn">
        <input type="button" value="삭제" onclick="confirmDelete()" class="btn">
        <input type="button" value="목록" onclick="location.href='board_list.php'" class="btn">
    </div>

    <!-- 댓글 목록 -->
    <h3>댓글</h3>
    <table class="comment-table" width="600">
        <tr>
            <th width="100">작성자</th>
            <th>내용</th>
            <th width="150">작성일</th>
            <th width="60">삭제</th>
        </tr>
        <?php
        if (mysqli_num_rows($commentResult) > 0) {
            while ($comment = mysqli_fetch_assoc($commentResult)) {
                echo "<tr>";
                echo "<td>" . $comment['writer'] . "</td>";
                echo "<td>" . nl2br($comment['content']) . "</td>";
                echo "<td>" . $comment['regDate'] . "</td>";
                echo "<td><input type='button' value='삭제' onclick='confirmCommentDelete(" . $comment['commentNum'] . ")' class='btn'></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>등록된 댓글이 없습니다.</td></tr>";
        }
        ?>
    </table>

    <!-- 댓글 작성 폼 -->
    <div class="comment-form">
        <form name="commentForm" method="post" action="comment_write.php" onsubmit="return validateComment()">
            <input type="hidden" name="boardNum" value="<?php echo $boardNum; ?>">
            <table width="600">
                <tr>
                    <th width="100">작성자</th>
                    <td><input type="text" name="writer"></td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td><textarea name="content" rows="3" cols="50"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="댓글등록" class="btn">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
