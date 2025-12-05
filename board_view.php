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
$commentCount = mysqli_num_rows($commentResult);
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
        table td {
            border: 1px solid black;
            padding: 10px;
        }
        td.ttl {
            text-align: right;
            background-color: #ccc;
            width: 120px;
        }
    </style>
    <script>
        function confirmDelete() {
            if (confirm("정말로 삭제하시겠습니까?")) {
                location.href = "board_delete.php?boardNum=<?php echo $boardNum; ?>";
            }
        }

        function deleteComment(commentNum) {
            if (confirm("댓글을 삭제하시겠습니까?")) {
                location.href = "comment_delete.php?commentNum=" + commentNum + "&boardNum=<?php echo $boardNum; ?>";
            }
        }
    </script>
</head>
<body>
    <h2>게시판 - 상세보기</h2>

    <table>
        <tr>
            <td class="ttl">제목:</td>
            <td><?php echo $title; ?></td>
        </tr>
        <tr>
            <td class="ttl">작성자:</td>
            <td><?php echo $writer; ?></td>
        </tr>
        <tr>
            <td class="ttl">작성일:</td>
            <td><?php echo $regDate; ?></td>
        </tr>
        <tr>
            <td class="ttl">조회수:</td>
            <td><?php echo $viewCnt; ?></td>
        </tr>
        <tr>
            <td class="ttl">내용:</td>
            <td><?php echo $content; ?></td>
        </tr>
        <?php if ($fileName != null && $fileName != ''): ?>
        <tr>
            <td class="ttl">첨부이미지:</td>
            <td>
                <img src="../img/<?php echo $fileName; ?>" style="max-width:600px; border:1px solid #ccc;">
                <br>
                <a href="../img/<?php echo $fileName; ?>" download><?php echo $fileName; ?></a>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="2" align="center">
                <input type="button" value="수정" onclick="location.href='board_edit.php?boardNum=<?php echo $boardNum; ?>'">
                <input type="button" value="삭제" onclick="confirmDelete()">
                <input type="button" value="목록" onclick="location.href='board_list.php'">
            </td>
        </tr>
    </table>

    <h3>댓글 (<?php echo $commentCount; ?>개)</h3>

    <!-- 댓글 목록 -->
    <table>
        <?php
        if ($commentCount > 0) {
            while ($comment = mysqli_fetch_assoc($commentResult)) {
                $commentNum = $comment['commentNum'];
                $commentWriter = $comment['writer'];
                $commentContent = nl2br($comment['content']);
                $commentDate = $comment['regDate'];

                echo "<tr>";
                echo "<td style='background-color:#f9f9f9; padding:10px;'>";
                echo "<strong>$commentWriter</strong> <small>($commentDate)</small><br>";
                echo "$commentContent";
                echo "</td>";
                echo "<td style='width:80px; text-align:center;'>";
                echo "<input type='button' value='삭제' onclick='deleteComment($commentNum)'>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>댓글이 없습니다.</td></tr>";
        }
        ?>
    </table>

    <!-- 댓글 작성 폼 -->
    <h3>댓글 작성</h3>
    <table>
    <form method="post" action="comment_write.php">
        <input type="hidden" name="boardNum" value="<?php echo $boardNum; ?>">
        <tr>
            <td class="ttl">작성자:</td>
            <td><input type="text" name="writer" required></td>
        </tr>
        <tr>
            <td class="ttl">내용:</td>
            <td><textarea name="content" rows="3" cols="60" required></textarea></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="댓글 등록">
            </td>
        </tr>
    </form>
    </table>
</body>
</html>
<?php
mysqli_close($conn);
?>
