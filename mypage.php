<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?require_login=1");
    exit;
}

// 데이터베이스 연결
require_once 'db_config.php';

$memberNum = $_SESSION['memberNum'];

// 회원 정보 조회
$memberSql = "SELECT * FROM member WHERE memberNum = ?";
$memberStmt = mysqli_prepare($conn, $memberSql);
mysqli_stmt_bind_param($memberStmt, "i", $memberNum);
mysqli_stmt_execute($memberStmt);
$memberResult = mysqli_stmt_get_result($memberStmt);
$member = mysqli_fetch_assoc($memberResult);
mysqli_stmt_close($memberStmt);

// 내가 쓴 게시글 조회
$boardSql = "SELECT boardNum, title, regDate, viewCnt FROM board WHERE memberNum = ? ORDER BY boardNum DESC LIMIT 10";
$boardStmt = mysqli_prepare($conn, $boardSql);
mysqli_stmt_bind_param($boardStmt, "i", $memberNum);
mysqli_stmt_execute($boardStmt);
$boardResult = mysqli_stmt_get_result($boardStmt);

// 내가 쓴 게시글 수
$boardCountSql = "SELECT COUNT(*) as cnt FROM board WHERE memberNum = ?";
$boardCountStmt = mysqli_prepare($conn, $boardCountSql);
mysqli_stmt_bind_param($boardCountStmt, "i", $memberNum);
mysqli_stmt_execute($boardCountStmt);
$boardCountResult = mysqli_stmt_get_result($boardCountStmt);
$boardCount = mysqli_fetch_assoc($boardCountResult)['cnt'];
mysqli_stmt_close($boardCountStmt);

// 내가 쓴 댓글 수
$commentCountSql = "SELECT COUNT(*) as cnt FROM comment WHERE writer = ?";
$commentCountStmt = mysqli_prepare($conn, $commentCountSql);
$userName = $_SESSION['userName'];
mysqli_stmt_bind_param($commentCountStmt, "s", $userName);
mysqli_stmt_execute($commentCountStmt);
$commentCountResult = mysqli_stmt_get_result($commentCountStmt);
$commentCount = mysqli_fetch_assoc($commentCountResult)['cnt'];
mysqli_stmt_close($commentCountStmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>마이페이지</title>
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
        .title-cell {
            text-align: left;
        }
        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }
        .stats-box {
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .stats-box span {
            margin-right: 30px;
            font-size: 16px;
        }
        .stats-box strong {
            color: #333;
            font-size: 18px;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }
    </style>
</head>
<body>
    <h2>마이페이지</h2>

    <!-- 통계 -->
    <div class="stats-box">
        <span>작성한 게시글: <strong><?php echo $boardCount; ?></strong>개</span>
        <span>작성한 댓글: <strong><?php echo $commentCount; ?></strong>개</span>
    </div>

    <!-- 회원 정보 -->
    <h3 class="section-title">회원 정보</h3>
    <table>
        <tr>
            <th>아이디</th>
            <td><?php echo htmlspecialchars($member['userId']); ?></td>
        </tr>
        <tr>
            <th>이름</th>
            <td><?php echo htmlspecialchars($member['userName']); ?></td>
        </tr>
        <tr>
            <th>이메일</th>
            <td><?php echo htmlspecialchars($member['email'] ?? '-'); ?></td>
        </tr>
        <tr>
            <th>가입일</th>
            <td><?php echo $member['regDate']; ?></td>
        </tr>
        <tr>
            <th>마지막 로그인</th>
            <td><?php echo $member['lastLogin'] ?? '-'; ?></td>
        </tr>
    </table>

    <!-- 내가 쓴 게시글 -->
    <h3 class="section-title">내가 쓴 게시글 (최근 10개)</h3>
    <table>
        <tr>
            <th width="60" style="text-align:center;">번호</th>
            <th style="text-align:center;">제목</th>
            <th width="80" style="text-align:center;">조회수</th>
            <th width="150" style="text-align:center;">작성일</th>
        </tr>
        <?php if (mysqli_num_rows($boardResult) > 0): ?>
            <?php while ($board = mysqli_fetch_assoc($boardResult)): ?>
            <tr>
                <td style="text-align:center;"><?php echo $board['boardNum']; ?></td>
                <td class="title-cell"><a href="board_view.php?boardNum=<?php echo $board['boardNum']; ?>"><?php echo htmlspecialchars($board['title']); ?></a></td>
                <td style="text-align:center;"><?php echo $board['viewCnt']; ?></td>
                <td style="text-align:center;"><?php echo $board['regDate']; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">작성한 게시글이 없습니다.</td>
            </tr>
        <?php endif; ?>
    </table>

    <div style="margin-top: 20px;">
        <input type="button" value="게시판으로" onclick="location.href='board_list.php'" class="btn">
        <input type="button" value="로그아웃" onclick="location.href='logout.php'" class="btn">
    </div>
</body>
</html>
<?php
mysqli_stmt_close($boardStmt);
mysqli_close($conn);
?>
