<?= $this->extend('_layout') ?>

<?= $this->section('content') ?>
<style>
    .main-content-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 75vh;
        /* Adjust height to fill most of the screen */
    }

    .watermark-logo {
        width: 50%;
        max-width: 500px;
        /* Adjust max width as needed */
        opacity: 0.25;
        /* Adjust opacity for watermark effect */
    }
</style>
<h1>首頁</h1>
<p>歡迎來到Land Stone的管理後台。</p>
<?= $this->endSection() ?>
