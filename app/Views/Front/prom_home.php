<!-- Using Layout -->
<?= $this->extend('Layouts/Prom_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
<h4>Promotion</h4>
<h5>Please select a promotion benefit.</h5>

<form name="form_customer" method="post" action="/front/prom/customer">
    <input type="text" name="cus_name" maxlength="6" placeholder="Enter your name." value="" />
    <br />
    <input type="tel" name="cus_mobile" maxlength="11" placeholder="Enter your mobile number without -" pattern="01[0-9]{1}[0-9]{3,4}[0-9]{4}" value="" />
    <button type="button" onclick="return checkCustomer();">sign in</button>
    <br />
</form>

<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
    // ajax - sign in
    function checkCustomer () {
        $.ajax({
            url: '/api/front/prom/cuatomer',
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
                    alert('No matching information.');
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