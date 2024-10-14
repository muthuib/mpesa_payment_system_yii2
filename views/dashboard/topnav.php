<?php

use yii\helpers\Html;

?>
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="<?= Yii::$app->homeUrl ?>" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">Payment System</span>
        </a>
        <?php if (!Yii::$app->user->isGuest) :
        ?>
        <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
        <?php endif;
        ?>
    </div>
    <!-- End Logo -->
    <nav class=" header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <ul class="d-flex align-items-center">
                <?php if (Yii::$app->user->isGuest) : ?>
                <li class="nav-item login-link">
                    <a class="nav-link" style=" font-size: 20px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style=" font-size: 20px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/site/signup']) ?>">Register</a>
                </li>
                <li class="nav-item login-link">
                    <!-- Add a CSS class -->
                    <a class="btn btn-danger" style="border-radius: 25px; font-size: 20px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">Login</a>
                </li>
                <?php else : ?>
                <li class="nav-item login-link">
                    <a class="nav-link" style=" font-size: 15px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Home</a>
                </li>
                <li class="nav-item login-link">
                    <!-- Add a CSS class -->
                    <a class="btn btn-danger" style="border-radius: 25px; font-size: 20px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/site/mpesa']) ?>">Deposit</a>
                </li>
                <li class="nav-item login-link">
                    <a class="nav-link" style=" font-size: 15px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/payments/user_payments']) ?>">Payment
                        history</a>
                </li>
                <li class="nav-item" style=" font-size: 15px;">
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                    <?= Html::submitButton('Logout (' . Yii::$app->user->identity->EMAIL . ')', ['class' => 'nav-link btn btn-link logout']) ?>
                    <?= Html::endForm() ?>
                </li>
                <li class="nav-item login-link">
                    <a class="nav-link" style=" font-size: 15px;"
                        href="<?= Yii::$app->urlManager->createUrl(['/site/change-password']) ?>">Change password</a>
                </li>
                <?php endif; ?>
            </ul>
        </ul>
    </nav><!-- End Icons Navigation -->
</header><!-- End Header -->
<style>
.login-link {
    margin-left: 40px;
    margin-right: 40px;
}
</style>