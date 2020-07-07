<!-- Using Layout -->
<?= $this->extend('Layouts/Kw_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
<a href="/Kcar/Kw"> <- </a> <span>신청 완료</span>


<div>
    <p><?=$view['data']['type_kr']?> 서비스</p>
    <p><?=$view['data']['items']?></p>
</div>
<br />

<div>
    <p>주소</p>
    <p>우편번호: <?=$view['data']['cus_zip'] ?? '' ?></p>
    <p>주소: <?=$view['data']['cus_addr1'] ?? '' ?></p>
    <p>상세주소: <?=$view['data']['cus_addr2'] ?? '' ?></p>
</div>
<br />

<div>
    <p>- 상품관련 문의사항이 있으실 경우  대표 번호로 문의주시길 바랍니다. 대표전화 : 02-1800-0206</p>
    <p>- 선택 해 주신 일정에 세차 진행이 가능할   경우 세차 담당자가 직접 연락 드릴 수 있도록 하 겠습니다.</p>
    <p>단, 세차 일정 조율이 어려울 경우 상담원이 직접 고객님께 연락 드릴 예정입니다</p>
</div>
<br />

<div>
    <a href="/Kcar/Kw">확인</a>
</div>


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