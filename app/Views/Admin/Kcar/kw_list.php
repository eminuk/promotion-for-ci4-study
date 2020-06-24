<!-- Using Layout -->
<?= $this->extend('Layouts/Admin_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
    <h1>Hello World!</h1>
    
<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
</script>
<?= $this->endSection() ?>