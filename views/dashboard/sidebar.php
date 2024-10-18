<?php

/**
 * This is the main sidebar
 */

use yii\helpers\Html;

// Example permission names
$manageUsersPermission = 'manageUsers';


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
        <!-- check if the user has a permission to manage users -->
        <?php if (Yii::$app->user->can($manageUsersPermission)): ?>
        <li class="nav-item <?= strpos($currentUrl, '/applicant-details/create') !== false ? 'active' : '' ?>">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/user/manage']) ?>">
                <i class="bi bi-person-rolodex"></i>
                <span>Manage Users</span>
            </a>
        </li>
        <?php endif; ?>
        <!-- End  Details Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/form-data/index']) ?>">
                <i class="bi bi-book"></i>
                <span>pdf details</span>
            </a>
        </li><!-- End details Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/post/index']) ?>">
                <i class="bi bi-translate"></i></i>
                <span>Post</span>
            </a>
        </li><!-- End  Skills Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/role/index']) ?>">
                <i class="bi bi-buildings"></i>
                <span>Assign Roles</span>
            </a>
        </li><!-- End  Nav -->
        <li>
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['role/create-role']) ?>">
                <i class="bi bi-person-fill"></i>
                <span> Create Role</span>

            </a>
        </li>


        <!-- ASSIGN PERMISSIONS IMPLEMENTATION IN SIDEBAR -->
        <!-- Assign Permissions section -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseRoles"
                aria-expanded="true" aria-controls="collapseRoles">
                <i class="fas fa-user-shield"></i>
                <span>Manage Roles & Permissions</span>
            </a>
            <div id="collapseRoles" class="collapse" aria-labelledby="headingRoles" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Assign Permissions:</h6>
                    <ul class="list-group">
                        <?php
                            $roles = Yii::$app->authManager->getRoles(); // Fetch all roles
                            foreach ($roles as $role) : ?>
                        <li class="list-group-item">
                            <a
                                href="<?= Yii::$app->urlManager->createUrl(['role/assign-permission', 'roleName' => $role->name]) ?>">
                                <?= Html::encode($role->name) ?> - Assign Permissions
                            </a>
                        </li>

                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </li>

        <!-- END OF ASSIGN PERMISSION IMPLEMENTATION -->


        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= Yii::$app->urlManager->createUrl(['/applications/index']) ?>">
                <i class="bi bi-envelope-paper"></i>
                <span>View Applications</span>
            </a>
        </li>
    </ul>

    <!-- Admin options -->
    <!-- <? //php if ((new user)->checkRole(Yii::$app->user->id, user::ROLE_ADMIN)) : 
            ?>
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
</aside>
<!-- End Sidebar-->