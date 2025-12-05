-- ========================================
-- 게시판 시스템 데이터베이스 생성 스크립트
-- 데이터베이스: sample01_db
-- ========================================

-- 1. 데이터베이스 사용 (이미 존재한다고 가정)
USE sample01_db;

-- 2. board 테이블 생성
-- 기존 테이블이 있으면 삭제 (주의: 데이터가 모두 삭제됩니다!)
DROP TABLE IF EXISTS board;

-- 게시판 테이블 생성
CREATE TABLE board (
    boardNum INT AUTO_INCREMENT PRIMARY KEY COMMENT '게시글 번호',
    title VARCHAR(200) NOT NULL COMMENT '제목',
    writer VARCHAR(50) NOT NULL COMMENT '작성자',
    content LONGTEXT NOT NULL COMMENT '내용',
    fileName VARCHAR(200) DEFAULT NULL COMMENT '첨부파일명',
    regDate DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '작성일시',
    viewCnt INT DEFAULT 0 COMMENT '조회수',
    ipAddr VARCHAR(50) DEFAULT NULL COMMENT '작성자 IP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='게시판 테이블';

-- 3. 샘플 데이터 삽입 (선택사항)
INSERT INTO board (title, writer, content, ipAddr) VALUES
('PHP 게시판 제작하기', '홍길동', 'PHP를 이용한 게시판 제작 예제입니다.\n다양한 기능을 구현해봅시다.', '127.0.0.1'),
('MySQL 데이터베이스 연동', '김철수', 'MySQL과 PHP를 연동하는 방법에 대해 알아봅니다.\nCRUD 작업을 수행할 수 있습니다.', '127.0.0.1'),
('파일 업로드 기능 구현', '이영희', '파일 업로드 기능을 추가하여\n첨부파일을 관리할 수 있습니다.', '127.0.0.1'),
('검색 기능 만들기', '박민수', '제목과 내용으로 검색하는 기능을 구현합니다.\n정규표현식을 활용할 수 있습니다.', '127.0.0.1'),
('페이지네이션 적용하기', '최지영', '게시글이 많을 때 페이지 단위로 나누어 표시합니다.\nLIMIT와 OFFSET을 사용합니다.', '127.0.0.1');

-- 4. 테이블 확인
SELECT * FROM board;

-- 5. 테이블 구조 확인
DESC board;

-- ========================================
-- 실행 방법 1: phpMyAdmin에서 실행
--   1) phpMyAdmin 접속 (http://localhost/phpmyadmin)
--   2) sample01_db 데이터베이스 선택
--   3) SQL 탭 클릭
--   4) 위 쿼리문 복사 후 붙여넣기
--   5) 실행 버튼 클릭
--
-- 실행 방법 2: MySQL 명령줄에서 실행
--   mysql -u root -p -P 3307 sample01_db < createTable.sql
--
-- 실행 방법 3: PHP 파일 실행
--   http://localhost/sg/board_project/createBoardTable.php
-- ========================================
