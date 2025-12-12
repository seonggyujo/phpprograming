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
    <title>로그인</title>
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
        input[type="text"], input[type="password"] {
            padding: 5px;
            width: 200px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
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
</head>
<body>
    <h2>로그인</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="message success">회원가입이 완료되었습니다. 로그인해주세요.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="message error">
            <?php
            $error = $_GET['error'];
            if ($error == 'invalid') echo '아이디 또는 비밀번호가 올바르지 않습니다.';
            else if ($error == 'empty') echo '아이디와 비밀번호를 입력해주세요.';
            else echo '로그인 중 오류가 발생했습니다.';
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['require_login'])): ?>
        <div class="message error">로그인이 필요한 서비스입니다.</div>
    <?php endif; ?>

    <form action="login_process.php" method="post">
        <table>
            <tr>
                <th>아이디</th>
                <td><input type="text" name="userId" required autofocus></td>
            </tr>
            <tr>
                <th>비밀번호</th>
                <td><input type="password" name="userPw" required></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="로그인" class="btn">
                    <input type="button" value="회원가입" onclick="location.href='register.php'" class="btn">
                </td>
            </tr>
        </table>
    </form>

    <div class="link-group">
        <a href="board_list.php">[게시판으로 돌아가기]</a>
    </div>
</body>
</html>
