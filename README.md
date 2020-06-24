# autocarz_kcar


## DB 구성

### 사용 버전

MySQL 5.x

### DATABASE 생성 및 계정 생성

``` sql
SHOW DATABASES;

CREATE DATABASE IF NOT EXISTS autocarz_kcar DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON autocarz_kcar.* TO 'autocarz'@'localhost' IDENTIFIED BY 'autocarz1234';
GRANT ALL PRIVILEGES ON autocarz_kcar.* TO 'autocarz'@'%' IDENTIFIED BY 'autocarz1234'; 
```

### TABLE 생성

1. 관리자 테이블

    ``` sql
    CREATE TABLE IF NOT EXISTS manager
    (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'id',
        email VARCHAR(100) NOT NULL COMMENT '관리자 email',
        password VARCHAR(255) NOT NULL COMMENT '관리자 패스워드',
        name VARCHAR(50) NOT NULL COMMENT '관리자 이름',
        status TINYINT NOT NULL DEFAULT 0 COMMENT '상태 (0:비활성 / 1:정상)', 
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
        
        UNIQUE KEY (email),
        INDEX (state)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='관리자';

    -- Insert default manager account (admin@autocarz.co.kr / autocarz1234)
    INSERT INTO manager (email, password, name, state) VALUES 
    ('admin@autocarz.co.kr', '87dd4e4ca8493300480ef1e419760ba4f0c7261c3baa623c133cafaac4562b6f', '관리자', '1');
    ```



