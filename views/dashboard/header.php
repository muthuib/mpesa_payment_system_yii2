<?php

/** @var yii\web\View $this */
?>
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="<?= Yii::$app->homeUrl ?>" class="logo d-flex align-items-center">
            <img src="<?php echo Yii::getAlias('@web/img/logo.png') ?>" alt="Logo">
            <span class="d-none d-lg-block" style="font-size: 20px; color: blue;">
                <?php echo Yii::$app->name ?>
            </span>
        </a>
    </div><!-- End Logo -->
    <div class="d-flex justify-content-end" style="width: 100%;">
        <?= $this->render('/dashboard/topnav') ?>
    </div>
</header><!-- End Header -->