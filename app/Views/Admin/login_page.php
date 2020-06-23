<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <title>Administer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

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

    <form name="login_form" method="post" action="" accept-charset="utf-8">
        <?= csrf_field() ?>
        <input type="text" name="email" placeholder="이메일주소" value="<?=esc('admin@autocarz.co.kr')?>" />
        <br />
        <input type="password" name="password" placeholder="패스워드" value="autocarz1234" />
        <br />
        <label><input type="checkbox" name="keep_login" value='Y' checked />로그인 상태 유지</label>
        <br />
        <button type="submit">SIGN IN</button>
    </form>
</section>


<!-- FOOTER: DEBUG INFO + COPYRIGHTS -->
<footer>
</footer>

<!-- SCRIPTS -->
<script type="text/javascript">
</script>

<!-- -->

</body>
</html>
