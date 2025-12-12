<?php
session_start();
// 데이터베이스 연결
require_once 'db_config.php';

// 검색 처리
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : '';
$searchKeyword = isset($_GET['searchKeyword']) ? $_GET['searchKeyword'] : '';

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$listPerPage = 10; // 페이지당 게시글 수
$offset = ($page - 1) * $listPerPage;

// 검색 조건에 따른 쿼리 작성
if ($searchKeyword != '') {
    if ($searchType == 'title') {
        $sql = "SELECT * FROM board WHERE title LIKE '%$searchKeyword%' ORDER BY boardNum DESC LIMIT $offset, $listPerPage";
        $countSql = "SELECT COUNT(*) as total FROM board WHERE title LIKE '%$searchKeyword%'";
    } elseif ($searchType == 'writer') {
        $sql = "SELECT * FROM board WHERE writer LIKE '%$searchKeyword%' ORDER BY boardNum DESC LIMIT $offset, $listPerPage";
        $countSql = "SELECT COUNT(*) as total FROM board WHERE writer LIKE '%$searchKeyword%'";
    } else { // content
        $sql = "SELECT * FROM board WHERE content LIKE '%$searchKeyword%' ORDER BY boardNum DESC LIMIT $offset, $listPerPage";
        $countSql = "SELECT COUNT(*) as total FROM board WHERE content LIKE '%$searchKeyword%'";
    }
} else {
    $sql = "SELECT * FROM board ORDER BY boardNum DESC LIMIT $offset, $listPerPage";
    $countSql = "SELECT COUNT(*) as total FROM board";
}

$result = mysqli_query($conn, $sql);
$countResult = mysqli_query($conn, $countSql);
$countRow = mysqli_fetch_assoc($countResult);
$totalRecords = $countRow['total'];
$totalPages = ceil($totalRecords / $listPerPage);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>게시판 - 목록</title>
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
    </style>
</head>
<body>
    <h2>게시판</h2>

    <!-- 로그인 상태 표시 -->
    <div style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
        <?php if (isset($_SESSION['userId'])): ?>
            <span><strong><?php echo htmlspecialchars($_SESSION['userName']); ?></strong>님 환영합니다!</span>
            <a href="mypage.php" style="margin-left: 10px;">[마이페이지]</a>
            <a href="logout.php" style="margin-left: 10px;">[로그아웃]</a>
        <?php else: ?>
            <a href="login.php">[로그인]</a>
            <a href="register.php" style="margin-left: 10px;">[회원가입]</a>
        <?php endif; ?>
    </div>

    <div>
        <?php if (isset($_SESSION['userId'])): ?>
            <input type="button" value="글쓰기" onclick="location.href='board_write.php'" class="btn">
        <?php else: ?>
            <input type="button" value="글쓰기" onclick="alert('로그인이 필요합니다.'); location.href='login.php';" class="btn">
        <?php endif; ?>
        <span>전체 게시글: <?php echo $totalRecords; ?>개</span>
    </div>

    <!-- 검색 폼 -->
    <div>
        <form method="get" action="board_list.php">
            <select name="searchType">
                <option value="title" <?php echo ($searchType == 'title') ? 'selected' : ''; ?>>제목</option>
                <option value="writer" <?php echo ($searchType == 'writer') ? 'selected' : ''; ?>>작성자</option>
                <option value="content" <?php echo ($searchType == 'content') ? 'selected' : ''; ?>>내용</option>
            </select>
            <input type="text" name="searchKeyword" value="<?php echo $searchKeyword; ?>">
            <input type="submit" value="검색" class="btn">
            <input type="button" value="전체목록" onclick="location.href='board_list.php'" class="btn">
        </form>
    </div>

    <!-- 게시글 목록 테이블 -->
    <table>
        <tr>
            <th width="80">번호</th>
            <th>제목</th>
            <th width="120">작성자</th>
            <th width="100">조회수</th>
            <th width="180">작성일</th>
        </tr>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $boardNum = $row['boardNum'];
                $title = $row['title'];
                $writer = $row['writer'];
                $viewCnt = $row['viewCnt'];
                $regDate = $row['regDate'];

                // 댓글 수 조회
                $commentCountSql = "SELECT COUNT(*) as cnt FROM comment WHERE boardNum = $boardNum";
                $commentCountResult = mysqli_query($conn, $commentCountSql);
                $commentCountRow = mysqli_fetch_assoc($commentCountResult);
                $commentCount = $commentCountRow['cnt'];

                // 이미지 첨부 표시
                if ($row['fileName'] != null && $row['fileName'] != '') {
                    $title .= " 🖼️";
                }

                // 댓글 수 표시
                if ($commentCount > 0) {
                    $title .= " [$commentCount]";
                }

                echo "<tr>";
                echo "<td>$boardNum</td>";
                echo "<td class='title-cell'><a href='board_view.php?boardNum=$boardNum'>$title</a></td>";
                echo "<td>$writer</td>";
                echo "<td>$viewCnt</td>";
                echo "<td>$regDate</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>등록된 게시글이 없습니다.</td></tr>";
        }
        ?>
    </table>

    <!-- 페이지네이션 -->
    <?php if ($totalPages > 1): ?>
    <div>
        <?php
        // 이전 페이지
        if ($page > 1) {
            $prevPage = $page - 1;
            $searchParams = '';
            if ($searchKeyword != '') {
                $searchParams = "&searchType=$searchType&searchKeyword=$searchKeyword";
            }
            echo "<a href='board_list.php?page=$prevPage$searchParams'>[이전]</a> ";
        }

        // 페이지 번호 표시 (현재 페이지 기준 앞뒤 5개씩)
        $startPage = max(1, $page - 5);
        $endPage = min($totalPages, $page + 5);

        for ($i = $startPage; $i <= $endPage; $i++) {
            $searchParams = '';
            if ($searchKeyword != '') {
                $searchParams = "&searchType=$searchType&searchKeyword=$searchKeyword";
            }
            if ($i == $page) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='board_list.php?page=$i$searchParams'>$i</a> ";
            }
        }

        // 다음 페이지
        if ($page < $totalPages) {
            $nextPage = $page + 1;
            $searchParams = '';
            if ($searchKeyword != '') {
                $searchParams = "&searchType=$searchType&searchKeyword=$searchKeyword";
            }
            echo "<a href='board_list.php?page=$nextPage$searchParams'>[다음]</a>";
        }
        ?>
    </div>
    <?php endif; ?>
</body>
</html>
<?php
mysqli_close($conn);
?>
