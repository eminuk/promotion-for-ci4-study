<!-- Using Layout -->
<?= $this->extend('Layouts/Kw_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
<a href="/Kcar/Kw"> <- </a> <span>상품 선택</span>

<h6>출장세차 서비스, 세차용품, 자동차용품 중 한 가지를 선택해주세요.</h6>

<form name="form_product" method="post" action="/Kcar/Kw/customer">
    <input type="hidden" name="cus_name" value="<?=$view['cus_name'] ?? ''?>" />
    <input type="hidden" name="cus_mobile" value="<?=$view['cus_mobile'] ?? ''?>" />

    <div>
        <?php if (!empty($view['wash_service'])) :?>
            <label>
                <p>출장세차 서비스 - 서비스 항목</p>
                <p><?=$view['wash_service']['items']?></p>
                <input type="radio" name='type' value="1" />
            </label>
        <?php endif ?>
        <?php if (!empty($view['wash_goods'])) :?>
            <label>
                <p>세차 용품 - 상품 구성</p>
                <p><?=$view['wash_goods']['items']?></p>
                <input type="radio" name='type' value="2" />
            </label>
        <?php endif ?>
        <?php if (!empty($view['car_goods'])) :?>
            <label>
                <p>자동차 용품 - 상품 구성</p>
                <p><?=$view['car_goods']['items']?></p>
                <input type="radio" name='type' value="3" />
            </label>
        <?php endif ?>
    </div>
    <br />

    <div>
        <p>배송을 위해 정확한 주소를 입력해주세요.</p>
        <p>※ 출장세차 신청 시 해당 주소 기준으로 신청됩니다.</p>
        <input type="text" name="cus_zip" placeholder="우편번호" value="" />
        <br />
        <input type="text" name="cus_addr1" placeholder="" value="" />
        <br />
        <input type="text" name="cus_addr2" placeholder="상세 주소를 입력해주세요." value="" />
    </div>
    <br />

    <div>
        <p>유의 사항안내</p>
        <div>유의해 주세요.</div>
    </div>
    <br />

    <div>
        <p>개인정보 수집 및 이용 동의</p>
        <div>수집해서 사용할께요.</div>
        <label>
            개인정보 수집 및 이용에 동의합니다.
            <input type="checkbox" name="private_agree" />
        </label>
    </div>
    <br />

    <div>
        <button type="button" onclick="return doApply();">신청 하기</button>
    </div>
</form>

<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
    // ajax - 상품 선택
    function doApply() {
        $.ajax({
            url: '/API/Kcar/Kw/product',
            type: 'PUT',
            dataType: 'json',
            data: $('form[name=form_product]').serialize(),
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }

                $('form[name=form_product]').submit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                alert("Ajax error has occurred. \n" + errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus, form) {}
        });
        return false;
    }
</script>
<?= $this->endSection() ?>