<!-- Using Layout -->
<?= $this->extend('Layouts/Admin_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
    <form name="search_form" method="get" action="" accept-charset="utf-8">
        <?= csrf_field() ?>
        신청여부: 
        <select name="is_select">
            <option value="N">미신청</option>
            <option value="Y">신청완료</option>
        </select>
        <br />

        조회기간: 
        <input type="date" name="sdate" value="" /> ~
        <input type="date" name="edate" value="" />
        <br />

        <select name="search_key">
            <option value="kw_code">상품</option>
            <option value="car_number">차량번호</option>
            <option value="car_model">모델</option>
            <option value="cus_name">고객</option>
            <option value="cus_mobile">연락처</option>
            <option value="cus_post">우편번호</option>
            <option value="cus_addr1">주소</option>
            <option value="cus_addr2">상세주소</option>
            <option value="bnft_price">상품권금액</option>
            <option value="bnft_code">상품코드</option>
            <option value="product_type">신청상품</option>
        </select>
        <input type="text" name="search_value" placeholder="Search" value="" />
        <button type="button" >검색</button>
    </form>
    <br />

    <div>
        여기 리스트
    </div>
    <br />

    <div>
        여기 버튼
    </div>
<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
</script>
<?= $this->endSection() ?>