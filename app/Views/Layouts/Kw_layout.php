<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <title>Kcar KW</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <!-- STYLES -->
    <style type="text/css">
    </style>

    <?= $this->renderSection('head_add_area') ?>
</head>
<body>

<!-- HEADER: MENU + HEROE SECTION -->
<header>
    <h1>This is header</h1>
    <a href="/Kcar/Kw">K Car</a>
    <hr />
</header>

<!-- CONTENT -->
<section>
<?= $this->renderSection('content_area') ?>
</section>


<!-- FOOTER: DEBUG INFO + COPYRIGHTS -->
<footer>
    <hr />
    <h1>This is footer</h1>
    <div>
        <p>㈜오토카지 대표이사 문창훈, 이앙</p>
        <p>사업자등록번호 109-88-00998</p>
        <p>서울특별시 강남구 언주로 430 (윤익빌딩10층)</p>
        <p>대표전화 : 02-1800-0206</p>
        <p>서비스 관련 문의 : support@autocarz.co.kr</p>
    </div>
</footer>

<!-- SCRIPTS -->
<?= $this->renderSection('footer_script_area') ?>
<!-- -->



</body>
</html>
