<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <title>Administer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <!-- STYLES -->
    <style type="text/css">
    </style>
</head>
<body>

<!-- HEADER: MENU + HEROE SECTION -->
<header>
</header>

<!-- CONTENT -->
<section>
    <h1>SIGN IN (로그인)</h1>

    <form name="form_login" method="post" action="" accept-charset="utf-8">
        <?= csrf_field() ?>
        <input type="email" name="email" placeholder="이메일주소" value="<?=esc($view['remember'] ?? 'promotion@project.co.kr')?>" required />
        <br />
        <input type="password" name="password" placeholder="패스워드" value="promotion1234" required />
        <br />
        <label><input type="checkbox" name="remember" value='Y' checked />Remember Me</label>
        <br />
        <button type="button" onclick="return doLogin();">SIGN IN</button>
    </form>
</section>


<!-- FOOTER: DEBUG INFO + COPYRIGHTS -->
<footer>
</footer>

<!-- SCRIPTS -->
<script type="text/javascript">

function doLogin () {
    let email_obj = $('form[name=form_login] input[name=email]');
    let pword_obj = $('form[name=form_login] input[name=password]');

    // 아이디를 입력하지 않았을 경우
    if (email_obj.val() == '') {
        alert('아이디를 입력해주세요.');
        email_obj.focus();
        return false;
    }
    // 입력 형식이 맞지 않는 경우
    if (!/^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/i.test(email_obj.val())) {
        alert('이메일 형식이 잘못되었습니다.');
        email_obj.focus();
        return false;
    }
    // 비밀번호를 입력하지 않은 경우
    if (email_obj.val() == '') {
        alert('비밀번호를 입력해주세요.');
        email_obj.focus();
        return false;
    }

    $.ajax({
        url: '/api/admin/manager/login',
        type: 'POST',
        dataType: 'json',
        data: $('form[name=form_login]').serialize(),
        timeout: 30000,
        beforeSubmit: function (arr, form, options) {},
        beforeSend: function (jqXHR, settings) {},
        uploadProgress: function (event, position, total, percentComplete) {},
        success: function (data, textStatus, jqXHR) {
            if (data.result) {
                location.href = data.data.redirect_url;
            } else {
                alert('존재하지 않는 아이디 또는 비밀번호가 잘못되었습니다.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
            alert('Ajax error has occurred. ' + errorThrown);
            return false;
        },
        complete: function (jqXHR, textStatus, form) {}
    });

    return false;
}

</script>

<!-- -->

</body>
</html>
