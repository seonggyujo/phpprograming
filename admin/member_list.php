<?php
session_start();
require_once '../db_config.php';

// 관리자 권한 체크
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근할 수 있습니다.'); location.href='../board_list.php';</script>";
    exit;
}

// 검색 처리
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : '';
$searchKeyword = isset($_GET['searchKeyword']) ? trim($_GET['searchKeyword']) : '';

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$listPerPage = 15;
$offset = ($page - 1) * $listPerPage;

// 검색 조건에 따른 쿼리 작성
$whereClause = "";
$params = [];
$types = "";

if ($searchKeyword != '') {
    if ($searchType == 'userId') {
        $whereClause = "WHERE userId LIKE ?";
        $params[] = "%$searchKeyword%";
        $types = "s";
    } elseif ($searchType == 'userName') {
        $whereClause = "WHERE userName LIKE ?";
        $params[] = "%$searchKeyword%";
        $types = "s";
    } elseif ($searchType == 'email') {
        $whereClause = "WHERE email LIKE ?";
        $params[] = "%$searchKeyword%";
        $types = "s";
    }
}

// 전체 회원 수 조회
$countSql = "SELECT COUNT(*) as total FROM member $whereClause";
$countStmt = mysqli_prepare($conn, $countSql);
if (!empty($params)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
}
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $listPerPage);
mysqli_stmt_close($countStmt);

// 회원 목록 조회
$sql = "SELECT memberNum, userId, userName, email, role, regDate, lastLogin FROM member $whereClause ORDER BY memberNum DESC LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    $params[] = $offset;
    $params[] = $listPerPage;
    $types .= "ii";
    mysqli_stmt_bind_param($stmt, $types, ...$params);
} else {
    mysqli_stmt_bind_param($stmt, "ii", $offset, $listPerPage);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>관리자 - 회원 관리</title>
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
        .btn-danger {
            background-color: #f44336;
            color: white;
            border: none;
        }
        .role-admin {
            color: #1976d2;
            font-weight: bold;
        }
        .role-user {
            color: #666;
        }
    </style>
</head>
<body>
    <h2>회원 관리</h2>

    <!-- 로그인 상태 표시 -->
    <div style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
        <span><strong><?php echo htmlspecialchars($_SESSION['userName']); ?></strong>님 (관리자)</span>
        <a href="index.php" style="margin-left: 10px;">[대시보드]</a>
        <a href="../board_list.php" style="margin-left: 10px;">[게시판]</a>
        <a href="../mypage.php" style="margin-left: 10px;">[마이페이지]</a>
        <a href="../logout.php" style="margin-left: 10px;">[로그아웃]</a>
    </div>

    <div>
        <input type="button" value="대시보드" onclick="location.href='index.php'" class="btn">
        <span>전체 회원: <?php echo number_format($totalRecords); ?>명</span>
    </div>

    <!-- 검색 폼 -->
    <div>
        <form method="get" action="member_list.php">
            <select name="searchType">
                <option value="userId" <?php echo ($searchType == 'userId') ? 'selected' : ''; ?>>아이디</option>
                <option value="userName" <?php echo ($searchType == 'userName') ? 'selected' : ''; ?>>이름</option>
                <option value="email" <?php echo ($searchType == 'email') ? 'selected' : ''; ?>>이메일</option>
            </select>
            <input type="text" name="searchKeyword" value="<?php echo htmlspecialchars($searchKeyword); ?>">
            <input type="submit" value="검색" class="btn">
            <input type="button" value="전체목록" onclick="location.href='member_list.php'" class="btn">
        </form>
    </div>

    <!-- 회원 목록 테이블 -->
    <table>
        <tr>
            <th width="60">번호</th>
            <th width="120">아이디</th>
            <th width="100">이름</th>
            <th>이메일</th>
            <th width="80">역할</th>
            <th width="120">가입일</th>
            <th width="120">최근 로그인</th>
            <th width="120">관리</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['memberNum']; ?></td>
                <td><?php echo htmlspecialchars($row['userId']); ?></td>
                <td><?php echo htmlspecialchars($row['userName']); ?></td>
                <td class="title-cell"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                <td>
                    <span class="<?php echo ($row['role'] == 'admin') ? 'role-admin' : 'role-user'; ?>">
                        <?php echo ($row['role'] == 'admin') ? '관리자' : '일반'; ?>
                    </span>
                </td>
                <td><?php echo date('Y-m-d', strtotime($row['regDate'])); ?></td>
                <td><?php echo $row['lastLogin'] ? date('Y-m-d', strtotime($row['lastLogin'])) : '-'; ?></td>
                <td>
                    <input type="button" value="수정" onclick="location.href='member_edit.php?memberNum=<?php echo $row['memberNum']; ?>'" class="btn">
                    <?php if ($row['memberNum'] != $_SESSION['memberNum']): ?>
                    <input type="button" value="삭제" onclick="if(confirm('정말 삭제하시겠습니까?\n해당 회원의 게시글/댓글 작성자는 [탈퇴회원]으로 표시됩니다.')) location.href='member_delete.php?memberNum=<?php echo $row['memberNum']; ?>'" class="btn btn-danger">
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">회원이 없습니다.</td></tr>
        <?php endif; ?>
    </table>

    <!-- 페이지네이션 -->
    <?php if ($totalPages > 1): ?>
    <div>
        <?php
        $searchParams = '';
        if ($searchKeyword != '') {
            $searchParams = "&searchType=$searchType&searchKeyword=" . urlencode($searchKeyword);
        }
        
        if ($page > 1) {
            echo "<a href='member_list.php?page=" . ($page - 1) . $searchParams . "'>[이전]</a> ";
        }
        
        $startPage = max(1, $page - 5);
        $endPage = min($totalPages, $page + 5);
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $page) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='member_list.php?page=$i$searchParams'>$i</a> ";
            }
        }
        
        if ($page < $totalPages) {
            echo "<a href='member_list.php?page=" . ($page + 1) . $searchParams . "'>[다음]</a>";
        }
        ?>
    </div>
    <?php endif; ?>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
