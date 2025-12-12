<?php
session_start();

// 이미 로그인된 경우 게시판으로 이동
if (isset($_SESSION['userId'])) {
    header("Location: board_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
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
            width: 120px;
        }
        td {
            padding: 10px;
        }
        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            padding: 5px;
            width: 200px;
        }
        .hint {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .link-group {
            margin-top: 15px;
        }
        .link-group a {
            margin-right: 10px;
        }
    </style>
    <script>
    function validateForm() {
        var userId = document.getElementById('userId').value;
        var userPw = document.getElementById('userPw').value;
        var userPwConfirm = document.getElementById('userPwConfirm').value;
        var userName = document.getElementById('userName').value;

        // 아이디 검증
        var userIdPattern = /^[a-zA-Z0-9]{4,20}$/;
        if (!userIdPattern.test(userId)) {
            alert('아이디는 영문, 숫자 4~20자로 입력해주세요.');
            document.getElementById('userId').focus();
            return false;
        }

        // 비밀번호 검증
        if (userPw.length < 4) {
            alert('비밀번호는 4자 이상 입력해주세요.');
            document.getElementById('userPw').focus();
            return false;
        }

        // 비밀번호 확인
        if (userPw !== userPwConfirm) {
            alert('비밀번호가 일치하지 않습니다.');
            document.getElementById('userPwConfirm').focus();
            return false;
        }

        // 이름 검증
        if (userName.trim() === '') {
            alert('이름을 입력해주세요.');
            document.getElementById('userName').focus();
            return false;
        }

        return true;
    }
    </script>
</head>
<body>
    <h2>회원가입</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="message error">
            <?php
            $error = $_GET['error'];
            if ($error == 'duplicate') echo '이미 사용 중인 아이디입니다.';
            else if ($error == 'password') echo '비밀번호가 일치하지 않습니다.';
            else if ($error == 'empty') echo '모든 필수 항목을 입력해주세요.';
            else echo '회원가입 중 오류가 발생했습니다.';
            ?>
        </div>
    <?php endif; ?>

    <form action="register_process.php" method="post" onsubmit="return validateForm()">
        <table>
            <tr>
                <th>아이디 *</th>
                <td>
                    <input type="text" id="userId" name="userId" required>
                    <div class="hint">영문, 숫자 4~20자</div>
                </td>
            </tr>
            <tr>
                <th>비밀번호 *</th>
                <td>
                    <input type="password" id="userPw" name="userPw" required>
                    <div class="hint">4자 이상</div>
                </td>
            </tr>
            <tr>
                <th>비밀번호 확인 *</th>
                <td>
                    <input type="password" id="userPwConfirm" name="userPwConfirm" required>
                </td>
            </tr>
            <tr>
                <th>이름 *</th>
                <td>
                    <input type="text" id="userName" name="userName" required>
                </td>
            </tr>
            <tr>
                <th>이메일</th>
                <td>
                    <input type="email" id="email" name="email">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="회원가입" class="btn">
                    <input type="reset" value="다시작성" class="btn">
                    <input type="button" value="취소" onclick="location.href='board_list.php'" class="btn">
                </td>
            </tr>
        </table>
    </form>

    <div class="link-group">
        이미 계정이 있으신가요? <a href="login.php">[로그인]</a>
    </div>
</body>
</html>
