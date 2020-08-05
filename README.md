# Promotion project (Codeignitr 4 study)

## PHP 구성

- PHP 7.4 기준으로 작업
- CodeIgniter 4 요구사항 만족 필요
    - https://codeigniter.com/user_guide/intro/requirements.html
    - intl extension, mbstring extension 
    - php-json, php-mysqlnd, php-xml
    - libcurl
- box/spout 요구사항 만족 필요
    - php-zip

## DB 구성

### 사용 버전

MySQL 5.x

### DATABASE 생성 및 계정 생성

``` sql
SHOW DATABASES;

CREATE DATABASE IF NOT EXISTS promotion_prj DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON promotion_prj.* TO 'promotion_prj'@'localhost' IDENTIFIED BY 'promotion_prj1234';
GRANT ALL PRIVILEGES ON promotion_prj.* TO 'promotion_prj'@'%' IDENTIFIED BY 'promotion_prj1234'; 
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
        INDEX (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='관리자';

    -- Insert default manager account (promotion@project.co.kr / promotion1234)
    INSERT INTO manager (email, password, name, status) VALUES 
    ('promotion@project.co.kr', 'fcbd0318c8e142a07e2670d10b5cd270ef4b545243b21f7c4662d778c15c1208', '관리자', '1');
    ```


1. 프로모션 관리 테이블
    1. 기초 데이터 테이블
        ``` sql
        CREATE TABLE IF NOT EXISTS promotion
        (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
            pm_number VARCHAR(25) NOT NULL COMMENT '대상 고유번호',
            pm_code VARCHAR(10) NOT NULL COMMENT '대상 등급코드',
            cus_name VARCHAR(25) NOT NULL COMMENT '고객 이름',
            cus_mobile VARCHAR(13) NOT NULL COMMENT '고객 연락처',
            bnft_price INT NOT NULL COMMENT '상품권 금액',

            status TINYINT NOT NULL DEFAULT '0' COMMENT '상태 (0:등록중 / 1:정상 / 2:삭제)',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',

            PRIMARY KEY (id),
            UNIQUE KEY (pm_number),
            INDEX (pm_code),
            INDEX (bnft_price),
            INDEX (cus_name, cus_mobile)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='프로모션 기초 데이터';
        ```
    1. 혜택 코드 데이터
        ``` sql
        CREATE TABLE IF NOT EXISTS promotion_benefit
        (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
            bnft_code VARCHAR(10) NOT NULL COMMENT '혜택 코드',
            bnft_price INT NOT NULL DEFAULT 0 COMMENT '혜택 금액  promotion.bnft_price',

            status TINYINT(4) NOT NULL DEFAULT '1' COMMENT '상태 (0:비활성 / 1:정상)',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',

            PRIMARY KEY (id),
            UNIQUE KEY (bnft_code),
            UNIQUE KEY (bnft_price)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='프로모션 혜택 데이터';

        -- Insert default codes
        INSERT INTO promotion_benefit (bnft_code, bnft_price) VALUES 
        ('G20', 20000), ('G25', 25000), ('G30', 30000), ('G35', 35000), ('G45', 45000), ('G60', 60000), 
        ('G50', 50000), ('G75', 75000), ('G70', 70000), ('G95', 95000)
        ;
        ```
    1. 고객 데이터
        ``` sql
        CREATE TABLE IF NOT EXISTS promotion_customer
        (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
            pm_id INT UNSIGNED NOT NULL COMMENT '프로모션 ID  promotion.id',
            bnft_code VARCHAR(10) NOT NULL COMMENT '혜택 코드  promotion_benefit.bnft_code',
            product_id INT UNSIGNED NULL COMMENT '선택 상품 ID  promotion_product.id',
            select_at DATETIME NULL COMMENT '선택일시',
            cus_zip VARCHAR(6) NOT NULL DEFAULT '' COMMENT '고객 우편번호',
            cus_addr1 VARCHAR(100) NOT NULL DEFAULT '' COMMENT '고객 주소',
            cus_addr2 VARCHAR(100) NOT NULL DEFAULT '' COMMENT '고객 상세주소',
            hope_1 DATETIME NULL COMMENT '수령 희망일 1',
            hope_2 DATETIME NULL COMMENT '수령 희망일 2',
            hope_3 DATETIME NULL COMMENT '수령 희망일 3',
            send_sms TINYINT NOT NULL DEFAULT 0 COMMENT 'SMS 발송여부 (0:미발송 / 1:발송완료 / 2:발송실패)',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',

            PRIMARY KEY (id),
            UNIQUE KEY (pm_id),
            INDEX (bnft_code),
            INDEX (product_id), 
            INDEX (send_sms)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='프로모션 고객 데이터';
        ```
    1. 상품 데이터
        ``` sql
        CREATE TABLE IF NOT EXISTS promotion_product
        (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
            pm_code VARCHAR(10) NOT NULL COMMENT '등급코드 promotion.pm_code',
            bnft_price INT NOT NULL DEFAULT 0 COMMENT '혜택 금액  promotion.bnft_price',
            type TINYINT NOT NULL DEFAULT 0 COMMENT '상품 타입 (1:상품그룹1 / 2:상품그룹2 / 3:상품그룹3)',
            items TEXT NULL COMMENT '상품 구성품',
            status TINYINT(4) NOT NULL DEFAULT '1' COMMENT '상태 (0:비활성 / 1:정상)',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',

            PRIMARY KEY (id),
            UNIQUE KEY (pm_code, bnft_price, type),
            INDEX (type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='프로모션 상품 데이터';

        -- Insert default products
        INSERT INTO `promotion_product` (`pm_code`, `bnft_price`, `type`, `items`) VALUES
        ('6', '20000', '1', '실외'),
        ('6', '20000', '2', '카샴푸\n광택용극세타월'),
        ('6', '20000', '3', '랏츠 트윈스마트 충전기\n햇빛 가리게(3p)'),
        ('12', '30000', '1', '실외\n실내'),
        ('12', '30000', '2', '카샴푸\n유리광택세정제\n광택용극세타월'),
        ('12', '30000', '3', '랏츠 트윈스마트 충전기\n엑스트라 3in1 USB 고속 충전 cable\n시그니처 스윙 주차 알림판'),
        ('6', '25000', '1', '실외'),
        ('6', '25000', '2', '카샴푸\n유리광택세정제'),
        ('6', '25000', '3', '랏츠 트윈스마트 충전기\n아이팝 블루라인 마그네틱 케이블 5핀.8핀C타입\n햇빛 가리게 (3p)'),
        ('12', '35000', '1', '실외\n실내'),
        ('12', '35000', '2', 'MBL 모먼트 아쿠아 스프레이 2개'),
        ('12', '35000', '3', '랏츠 트윈스마트 충전기\n엑스트라 3in1 USB 고속 충전 cable\n햇빛 가리게 (3p)\n시그니처 스윙 주차 알림판\n'),
        ('6', '35000', '1', '실외'),
        ('6', '35000', '2', '카샴푸\n유리광택세정제\n휠크리너'),
        ('6', '35000', '3', '랏츠 트윈스마트 충전기\n아이팝 블루라인 마그네틱 케이블 5핀.8핀C타입\n햇빛 가리게 (3p)\n풀메탈 QC3.0 충전기'),
        ('12', '45000', '1', '실외\n실내'),
        ('12', '45000', '2', '카샴푸\n유리광택세정제\n휠크리너\n광택용극세타월\n소낙스 멀티스펀지'),
        ('12', '45000', '3', '랏츠 트윈스마트 충전기\n엑스트라 3in1 USB 고속 충전 cable\n햇빛 가리게 (3p)\n시그니처 스윙 주차 알림판\n멀티 트렁크 정리함'),
        ('6', '45000', '1', '실외'),
        ('6', '45000', '2', '카샴푸\n유리광택세정제\n휠크리너\n광택용극세타월\n소낙스 멀티스펀지'),
        ('12', '60000', '1', '실외\n실내\n스팀살균'),
        ('12', '60000', '2', '카샴푸\n유리광택세정제\n휠크리너\n고속코팅왁스\n스티커타르제거'),
        ('6', '50000', '1', '실외\n실내\n스팀살균'),
        ('6', '50000', '2', '카샴푸\n유리광택세정제\n휠크리너\n고속코팅왁스\n광택용극세타월'),
        ('12', '75000', '1', '실외\n실내\n스팀살균\n엔진룸'),
        ('12', '75000', '2', '카샴푸\n유리광택세정제\n휠크리너\n고속코팅왁스\n스티커타르제거\n컴파운드\n광택용극세타월\n유리극세타월\n드라잉타월\n소낙스왁싱스펀지'),
        ('6', '70000', '1', '실외\n실내\n스팀살균'),
        ('6', '70000', '2', '카샴푸\n유리광택세정제\n휠크리너\n고속코팅왁스\n스티커타르제거\n컴파운드'),
        ('12', '95000', '1', '실외\n실내\n스팀살균\n엔진룸'),
        ('12', '95000', '2', '카샴푸\n유리광택세정제\n휠크리너\n고속코팅왁스\n스티커타르제거\n컴파운드\n타이어광택제\n유리극세타월\n드라잉타월\n소낙스왁싱스펀지\n트렁크정리함')
        ;
        ```



## .env 파일 생성

``` shell
cp env .env
```

### CI_ENVIRONMENT 설정

#### CI_ENVIRONMENT 값 변경:
- `production` 또는 `development`

### DATABASE 설정

#### APP_ENV 값 변경:
- `prd` 또는 기타 값
    (`prd` 이외 값은 정의되지 않음)
- `prd` 일경우 production 용으로 정의된 DB에 연결
- 이외 다른 값은 default로 정의한 DB에 연결
    - __.env__ 파일에 default DB 정의 필요

### app.baseURL
- app.baseURL 값 설정


## 서버 설정 (어플리케이션 구동)

- CodeIgniter4 manual 참고
- https://codeigniter.com/user_guide/installation/running.html#hosting-with-apache
