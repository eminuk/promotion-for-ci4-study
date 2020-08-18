<!-- Using Layout -->
<?= $this->extend('Layouts/Prom_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
<h4>Select complete.</h4>

<div>
    <p>Selected info:<p>
    <p>Option: <?=$view['data']['type_kr']?></p>
    <p>Detail: <?=$view['data']['items']?></p>
</div>
<br />

<div>
    <p>Address info</p>
    <p>Zip code: <?=$view['data']['customer_zip'] ?? '' ?></p>
    <p>Address: <?=$view['data']['customer_addr1'] ?? '' ?></p>
    <p>Address detail: <?=$view['data']['customer_addr2'] ?? '' ?></p>
</div>
<br />

<div>
    <p>Delivery desired date</p>
    <p>Date 1: <?=$view['data']['hope_1'] ?? '' ?></p>
    <p>Date 2: <?=$view['data']['hope_2'] ?? '' ?></p>
    <p>Date 3: <?=$view['data']['hope_3'] ?? '' ?></p>
</div>
<br />

<div>
    <p>- Promotion notice.</p>
    <p>- Promotion notice.</p>
</div>
<br />

<div>
    <a href="/front/prom">BACK</a>
</div>


<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
</script>
<?= $this->endSection() ?>