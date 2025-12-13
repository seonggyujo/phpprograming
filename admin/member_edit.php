<?php
session_start();
require_once '../db_config.php';

// 관리자 권한 체크
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근할 수 있습니다.'); location.href='../board_list.php';</script>";
    exit;
}

// 회원 번호 받기
$memberNum = isset($_GET['memberNum']) ? (int)$_GET['memberNum'] : 0;

if ($memberNum == 0) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='member_list.php';</script>";
    exit;
}

// 자기 자신인지 확인
$isSelf = ($memberNum == $_SESSION['memberNum']);

// POST 처리 (수정 처리)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['userName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    
    // 유효성 검사
    if ($userName == '') {
        echo "<script>alert('이름을 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    // 자기 자신의 역할은 변경 불가
    if ($isSelf && $role != 'admin') {
        echo "<script>alert('자신의 역할은 변경할 수 없습니다.'); history.back();</script>";
        exit;
    }
    
    // 역할 값 검증
    if ($role != 'admin' && $role != 'user') {
        $role = 'user';
    }
    
    // 회원 정보 업데이트
    $updateSql = "UPDATE member SET userName = ?, email = ?, role = ? WHERE memberNum = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "sssi", $userName, $email, $role, $memberNum);
    
    if (mysqli_stmt_execute($updateStmt)) {
        echo "<script>alert('회원 정보가 수정되었습니다.'); location.href='member_list.php';</script>";
    } else {
        echo "<script>alert('회원 정보 수정 중 오류가 발생했습니다.'); history.back();</script>";
    }
    mysqli_stmt_close($updateStmt);
    exit;
}

// 회원 정보 조회
$sql = "SELECT * FROM member WHERE memberNum = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $memberNum);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('회원을 찾을 수 없습니다.'); location.href='member_list.php';</script>";
    exit;
}

$member = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// 해당 회원의 통계
$boardCountSql = "SELECT COUNT(*) as cnt FROM board WHERE memberNum = ?";
$boardCountStmt = mysqli_prepare($conn, $boardCountSql);
mysqli_stmt_bind_param($boardCountStmt, "i", $memberNum);
mysqli_stmt_execute($boardCountStmt);
$boardCount = mysqli_fetch_assoc(mysqli_stmt_get_result($boardCountStmt))['cnt'];
mysqli_stmt_close($boardCountStmt);

$commentCountSql = "SELECT COUNT(*) as cnt FROM comment WHERE writer = ?";
$commentCountStmt = mysqli_prepare($conn, $commentCountSql);
mysqli_stmt_bind_param($commentCountStmt, "s", $member['userName']);
mysqli_stmt_execute($commentCountStmt);
$commentCount = mysqli_fetch_assoc(mysqli_stmt_get_result($commentCountStmt))['cnt'];
mysqli_stmt_close($commentCountStmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>관리자 - 회원 수정</title>
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
        .btn-danger {
            background-color: #f44336;
            color: white;
            border: none;
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
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            color: #856404;
        }
        .help-text {
            margin-top: 5px;
            font-size: 12px;
            color: #999;
        }
        .warning-text {
            margin-top: 5px;
            font-size: 12px;
            color: #f44336;
        }
        input[type="text"], input[type="email"], select {
            padding: 5px;
            width: 300px;
        }
        input[readonly] {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>회원 수정</h2>

    <!-- 로그인 상태 표시 -->
    <div style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
        <span><strong><?php echo htmlspecialchars($_SESSION['userName']); ?></strong>님 (관리자)</span>
        <a href="index.php" style="margin-left: 10px;">[대시보드]</a>
        <a href="member_list.php" style="margin-left: 10px;">[회원관리]</a>
        <a href="../board_list.php" style="margin-left: 10px;">[게시판]</a>
        <a href="../logout.php" style="margin-left: 10px;">[로그아웃]</a>
    </div>

    <?php if ($isSelf): ?>
    <div class="warning-box">
        <strong>주의:</strong> 자신의 계정을 수정 중입니다. 역할(관리자/일반) 변경은 불가능합니다.
    </div>
    <?php endif; ?>

    <!-- 회원 통계 -->
    <div class="stats-box">
        <span>작성한 게시글: <strong><?php echo number_format($boardCount); ?></strong>개</span>
        <span>작성한 댓글: <strong><?php echo number_format($commentCount); ?></strong>개</span>
    </div>

    <!-- 회원 정보 수정 폼 -->
    <form method="post">
        <table width="500">
            <tr>
                <th>회원번호</th>
                <td><input type="text" value="<?php echo $member['memberNum']; ?>" readonly></td>
            </tr>
            <tr>
                <th>아이디</th>
                <td>
                    <input type="text" value="<?php echo htmlspecialchars($member['userId']); ?>" readonly>
                    <div class="help-text">아이디는 변경할 수 없습니다.</div>
                </td>
            </tr>
            <tr>
                <th>이름 *</th>
                <td><input type="text" name="userName" value="<?php echo htmlspecialchars($member['userName']); ?>" required></td>
            </tr>
            <tr>
                <th>이메일</th>
                <td><input type="email" name="email" value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>"></td>
            </tr>
            <tr>
                <th>역할</th>
                <td>
                    <select name="role" <?php echo $isSelf ? 'disabled' : ''; ?>>
                        <option value="user" <?php echo ($member['role'] == 'user') ? 'selected' : ''; ?>>일반 회원</option>
                        <option value="admin" <?php echo ($member['role'] == 'admin') ? 'selected' : ''; ?>>관리자</option>
                    </select>
                    <?php if ($isSelf): ?>
                    <input type="hidden" name="role" value="admin">
                    <div class="warning-text">자신의 역할은 변경할 수 없습니다.</div>
                    <?php else: ?>
                    <div class="help-text">관리자는 모든 게시글/댓글 수정/삭제 권한을 갖습니다.</div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>가입일</th>
                <td><input type="text" value="<?php echo $member['regDate']; ?>" readonly></td>
            </tr>
            <tr>
                <th>마지막 로그인</th>
                <td><input type="text" value="<?php echo $member['lastLogin'] ?? '-'; ?>" readonly></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="수정완료" class="btn">
                    <input type="button" value="목록으로" onclick="location.href='member_list.php'" class="btn">
                    <?php if (!$isSelf): ?>
                    <input type="button" value="회원삭제" onclick="if(confirm('정말 삭제하시겠습니까?\n해당 회원의 게시글/댓글 작성자는 [탈퇴회원]으로 표시됩니다.')) location.href='member_delete.php?memberNum=<?php echo $memberNum; ?>'" class="btn btn-danger">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
<?php
mysqli_close($conn);
?>
