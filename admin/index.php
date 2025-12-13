<?php
session_start();
require_once '../db_config.php';

// 관리자 권한 체크
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근할 수 있습니다.'); location.href='../board_list.php';</script>";
    exit;
}

// 통계 데이터 조회
// 1. 전체 회원 수
$memberCountSql = "SELECT COUNT(*) as cnt FROM member";
$memberCountResult = mysqli_query($conn, $memberCountSql);
$memberCount = mysqli_fetch_assoc($memberCountResult)['cnt'];

// 2. 전체 게시글 수
$boardCountSql = "SELECT COUNT(*) as cnt FROM board";
$boardCountResult = mysqli_query($conn, $boardCountSql);
$boardCount = mysqli_fetch_assoc($boardCountResult)['cnt'];

// 3. 전체 댓글 수
$commentCountSql = "SELECT COUNT(*) as cnt FROM comment";
$commentCountResult = mysqli_query($conn, $commentCountSql);
$commentCount = mysqli_fetch_assoc($commentCountResult)['cnt'];

// 4. 오늘 가입한 회원 수
$todayMemberSql = "SELECT COUNT(*) as cnt FROM member WHERE DATE(regDate) = CURDATE()";
$todayMemberResult = mysqli_query($conn, $todayMemberSql);
$todayMemberCount = mysqli_fetch_assoc($todayMemberResult)['cnt'];

// 5. 오늘 작성된 게시글 수
$todayBoardSql = "SELECT COUNT(*) as cnt FROM board WHERE DATE(regDate) = CURDATE()";
$todayBoardResult = mysqli_query($conn, $todayBoardSql);
$todayBoardCount = mysqli_fetch_assoc($todayBoardResult)['cnt'];

// 6. 오늘 작성된 댓글 수
$todayCommentSql = "SELECT COUNT(*) as cnt FROM comment WHERE DATE(regDate) = CURDATE()";
$todayCommentResult = mysqli_query($conn, $todayCommentSql);
$todayCommentCount = mysqli_fetch_assoc($todayCommentResult)['cnt'];

// 7. 최근 가입 회원 5명
$recentMemberSql = "SELECT memberNum, userId, userName, regDate FROM member ORDER BY regDate DESC LIMIT 5";
$recentMemberResult = mysqli_query($conn, $recentMemberSql);

// 8. 최근 게시글 5개
$recentBoardSql = "SELECT boardNum, title, writer, regDate FROM board ORDER BY regDate DESC LIMIT 5";
$recentBoardResult = mysqli_query($conn, $recentBoardSql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>관리자 - 대시보드</title>
    <style>
        table {
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th {
            background-color: #ddd;
        }
        .title-cell {
            text-align: left;
        }
        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            border: 1px solid black;
            padding: 15px 25px;
            text-align: center;
            background: #f9f9f9;
        }
        .stat-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .stat-box .number {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .stat-box .today {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }
        .content-grid {
            display: flex;
            gap: 20px;
        }
        .content-grid > div {
            flex: 1;
        }
    </style>
</head>
<body>
    <h2>관리자 대시보드</h2>

    <!-- 로그인 상태 표시 -->
    <div style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
        <span><strong><?php echo htmlspecialchars($_SESSION['userName']); ?></strong>님 (관리자)</span>
        <a href="member_list.php" style="margin-left: 10px;">[회원관리]</a>
        <a href="../board_list.php" style="margin-left: 10px;">[게시판]</a>
        <a href="../mypage.php" style="margin-left: 10px;">[마이페이지]</a>
        <a href="../logout.php" style="margin-left: 10px;">[로그아웃]</a>
    </div>

    <!-- 통계 카드 -->
    <div class="stats-container">
        <div class="stat-box">
            <h3>전체 회원</h3>
            <div class="number"><?php echo number_format($memberCount); ?></div>
            <div class="today">오늘 +<?php echo $todayMemberCount; ?></div>
        </div>
        <div class="stat-box">
            <h3>전체 게시글</h3>
            <div class="number"><?php echo number_format($boardCount); ?></div>
            <div class="today">오늘 +<?php echo $todayBoardCount; ?></div>
        </div>
        <div class="stat-box">
            <h3>전체 댓글</h3>
            <div class="number"><?php echo number_format($commentCount); ?></div>
            <div class="today">오늘 +<?php echo $todayCommentCount; ?></div>
        </div>
    </div>

    <!-- 빠른 메뉴 -->
    <div>
        <input type="button" value="회원 관리" onclick="location.href='member_list.php'" class="btn">
        <input type="button" value="게시판" onclick="location.href='../board_list.php'" class="btn">
        <input type="button" value="메인으로" onclick="location.href='../board_list.php'" class="btn">
    </div>

    <!-- 최근 데이터 -->
    <div class="content-grid">
        <!-- 최근 가입 회원 -->
        <div>
            <h3 class="section-title">최근 가입 회원</h3>
            <table width="100%">
                <tr>
                    <th>아이디</th>
                    <th>이름</th>
                    <th width="120">가입일</th>
                </tr>
                <?php if (mysqli_num_rows($recentMemberResult) > 0): ?>
                    <?php while ($member = mysqli_fetch_assoc($recentMemberResult)): ?>
                    <tr>
                        <td class="title-cell"><a href="member_edit.php?memberNum=<?php echo $member['memberNum']; ?>"><?php echo htmlspecialchars($member['userId']); ?></a></td>
                        <td><?php echo htmlspecialchars($member['userName']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($member['regDate'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center;">회원이 없습니다.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- 최근 게시글 -->
        <div>
            <h3 class="section-title">최근 게시글</h3>
            <table width="100%">
                <tr>
                    <th>제목</th>
                    <th width="100">작성자</th>
                    <th width="120">작성일</th>
                </tr>
                <?php if (mysqli_num_rows($recentBoardResult) > 0): ?>
                    <?php while ($board = mysqli_fetch_assoc($recentBoardResult)): ?>
                    <tr>
                        <td class="title-cell"><a href="../board_view.php?boardNum=<?php echo $board['boardNum']; ?>"><?php echo htmlspecialchars(mb_substr($board['title'], 0, 20)); ?><?php echo mb_strlen($board['title']) > 20 ? '...' : ''; ?></a></td>
                        <td><?php echo htmlspecialchars($board['writer']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($board['regDate'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center;">게시글이 없습니다.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
