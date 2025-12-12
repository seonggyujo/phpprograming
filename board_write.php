<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?require_login=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판 - 글쓰기</title>
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
    </style>
    <script>
        function validateForm() {
            var title = document.forms["writeForm"]["title"].value.trim();
            var writer = document.forms["writeForm"]["writer"].value.trim();
            var content = document.forms["writeForm"]["content"].value.trim();

            if (title == "") {
                alert("제목을 입력해주세요.");
                document.forms["writeForm"]["title"].focus();
                return false;
            }

            if (writer == "") {
                alert("작성자를 입력해주세요.");
                document.forms["writeForm"]["writer"].focus();
                return false;
            }

            if (content == "") {
                alert("내용을 입력해주세요.");
                document.forms["writeForm"]["content"].focus();
                return false;
            }

            if (content.length < 10) {
                alert("내용은 최소 10자 이상 입력해주세요.");
                document.forms["writeForm"]["content"].focus();
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
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h2>게시판 - 글쓰기</h2>

    <form name="writeForm" method="post" action="board_write_process.php" enctype="multipart/form-data" onsubmit="return validateForm()">
        <table width="600">
            <tr>
                <th>제목</th>
                <td><input type="text" name="title" style="width:90%;"></td>
            </tr>
            <tr>
                <th>작성자</th>
                <td>
                    <input type="text" name="writer" value="<?php echo htmlspecialchars($_SESSION['userName']); ?>" readonly style="background:#f0f0f0;">
                    <input type="hidden" name="memberNum" value="<?php echo $_SESSION['memberNum']; ?>">
                </td>
            </tr>
            <tr>
                <th>내용</th>
                <td><textarea name="content" rows="15" cols="60"></textarea></td>
            </tr>
            <tr>
                <th>첨부이미지</th>
                <td>
                    <input type="file" name="upload" accept="image/*" onchange="previewImage(this)">
                    <br>
                    <img id="imagePreview" src="" style="display:none; max-width:400px; margin-top:10px; border:1px solid #ccc;">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="등록" class="btn">
                    <input type="reset" value="다시작성" class="btn">
                    <input type="button" value="목록" onclick="location.href='board_list.php'" class="btn">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
