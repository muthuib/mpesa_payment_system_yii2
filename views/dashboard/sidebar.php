<?php

/**
 * This is the main sidebar
 */

use app\models\JobInterview;
use app\models\user;

?>
<?php
$currentUrl = Yii::$app->request->url;
?>
<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link " href="<?= Yii::$app->urlManager->createUrl(['/dashboard/index']) ?>">
                <i class="bi bi-grid"></i>
                <span>Home</span>
            </a>
        </li><!-- End Dashboard Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>My system</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li class="nav-item <?= strpos($currentUrl, '/site/info') !== false ? 'active' : '' ?>">
                    <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/site/info']) ?>">
                        <i class="bi bi-info-circle" style="font-size: medium;"></i>
                        <span>system Information</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currentUrl, '/site/about') !== false ? 'active' : '' ?>">
                    <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/site/apply-info']) ?>">
                        <i class="bi bi-check2-circle" style="font-size: medium;"></i>
                        <span>system </span>
                    </a>
                </li>
            </ul>
        </li><!-- End  Nav -->

        <li class="nav-item <?= strpos($currentUrl, '/applicant-details/create') !== false ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/applicant-details/create']) ?>">
                <i class="bi bi-person-rolodex"></i>
                <span>read</span>
            </a>
        </li><!-- End  Details Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/form-data/create']) ?>">
                <i class="bi bi-book"></i>
                <span>pdf details</span>
            </a>
        </li><!-- End details Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/language/index']) ?>">
                <i class="bi bi-translate"></i></i>
                <span>Skills</span>
            </a>
        </li><!-- End  Skills Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/employment-history/index']) ?>">
                <i class="bi bi-buildings"></i>
                <span>History</span>
            </a>
        </li><!-- End History Nav -->
        </li>
        <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/user/profile']) ?>">
            <i class="bi bi-person-fill"></i>
            <span> View Profile </span>

        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/applications/index']) ?>">
                <i class="bi bi-envelope-paper"></i>
                <span>View Applications</span>
            </a>
        </li>
    </ul>

    <!-- Admin options -->
    <!-- <?//php if ((new user)->checkRole(Yii::$app->user->id, user::ROLE_ADMIN)) : ?>
    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#pages-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-menu-button-wide"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="pages-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
            <li class="nav-item">
                <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/jobs/create']) ?>">
                    <i class="bi bi-door-open"></i>
                    <span>Add Jobs</span>
                </a>
            </li>
        </ul>
    </li>
    <//?php endif; ?> -->

</aside><!-- End Sidebar-->