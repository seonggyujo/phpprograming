# 이미지 게시판

## 개발 언어
PHP, MySQL, HTML, CSS, JavaScript

## 프로젝트 설명
이미지 업로드가 가능한 게시판 시스템

## 구현 기능
- 게시글 CRUD
- 이미지 업로드
- 댓글 기능
- 검색
- 페이지네이션

## 설치 방법

### 1. 프로젝트 다운로드
```bash
git clone https://github.com/사용자명/board_project.git
```

### 2. 데이터베이스 설정

#### 2-1. MySQL에서 데이터베이스 생성
```sql
CREATE DATABASE sample01_db;
```

#### 2-2. 테이블 생성
`createTable.sql` 파일을 실행하거나, 브라우저에서 다음 파일들을 실행:
- `createBoardTable.php` - 게시판 테이블 생성
- `createCommentTable.php` - 댓글 테이블 생성

#### 2-3. DB 연결 설정
1. `db_config.sample.php` 파일을 복사하여 `db_config.php`로 저장
2. `db_config.php` 파일을 열어 자신의 환경에 맞게 수정

```php
$host = "127.0.0.1";        // 데이터베이스 호스트
$user = "root";             // 데이터베이스 사용자명
$pw = "비밀번호";            // 본인의 MySQL 비밀번호 입력
$dbName = "sample01_db";    // 데이터베이스 이름
$port = 3306;               // MySQL 포트 (XAMPP 기본: 3306)
```

### 3. 웹 서버 실행
XAMPP, WAMP 등의 웹 서버에서 Apache와 MySQL을 실행한 후 브라우저에서 접속:
```
http://localhost/board_project/board_list.php
```

## 파일 구조
```
board_project/
├── img/                    # 업로드된 이미지 저장 폴더
├── board_list.php          # 목록
├── board_write.php         # 작성 폼
├── board_write_process.php # 작성 처리
├── board_view.php          # 상세보기
├── board_edit.php          # 수정 폼
├── board_edit_process.php  # 수정 처리
├── board_delete.php        # 삭제 처리
├── comment_write.php       # 댓글 작성
├── comment_delete.php      # 댓글 삭제
├── db_config.php           # DB 연결 설정 (Git 제외)
├── db_config.sample.php    # DB 연결 설정 샘플
├── createBoardTable.php    # 게시판 테이블 생성
├── createCommentTable.php  # 댓글 테이블 생성
└── createTable.sql         # SQL 테이블 생성 스크립트
```

## 스크린샷
| 목록 | 상세보기 | 글쓰기 |
|------|----------|--------|
| ![목록](screenshots/list.png) | ![상세보기](screenshots/view.png) | ![글쓰기](screenshots/write.png) |
