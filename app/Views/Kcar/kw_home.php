<!-- Using Layout -->
<?= $this->extend('Layouts/Kw_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
<h5>K Car</h5>
<h4>K Car 보증만료</h4>
<h4>고객혜택</h4>

<h6>KW 보증보험 가입 고객분들께 혜택을 드려요!</h6>

<form name="form_customer" method="post" action="/Kcar/Kw/customer">
    <input type="text" name="cus_name" maxlength="6" placeholder="이름을 입력해주세요." value="홍길동" />
    <br />
    <input type="tel" name="cus_mobile" maxlength="11" placeholder="-없이 휴대폰번호를 입력해주세요." pattern="01[0-9]{1}[0-9]{3,4}[0-9]{4}" value="01088836414" />
    <button type="button" onclick="return checkCustomer();">조회</button>
    <br />

    <div>
        <p>- K Car Warranty 대상 고객정보를 입력해주세요.</p>
        <p>- 일치하는 정보가 없을 경우 조회되지 않습니다.</p>
        <p>- ㈜오토카지는 K Car의 프로모션을 위탁 받아 진행하는 자동차관리 전문 회사입니다.</p>
    </div>
</form>

<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
    // ajax - 고객 확인
    function checkCustomer () {
        $.ajax({
            url: '/API/Kcar/Kw/cuatomer',
            type: 'GET',
            dataType: 'json',
            data: $('form[name=form_customer]').serialize(),
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }

                if (data.data.is_customer) {
                    $('form[name=form_customer]').submit();
                } else {
                    alert('신청 대상 고객이 아닙니다.\n입력하신 이름, 휴대폰번호가 정확한지 다시 확인해주세요.\n(문의 02-555-0206 오토카지 고객센터)');
                }
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