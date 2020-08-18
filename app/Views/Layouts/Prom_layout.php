<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <title>Promotion</title>
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
    <a href="/front/prom">HOME</a>
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
        <p>Footer messages.</p>
        <p>Footer messages.</p>
    </div>
</footer>

<!-- SCRIPTS -->
<?= $this->renderSection('footer_script_area') ?>
<!-- -->



</body>
</html>
