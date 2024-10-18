<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;


/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\data\ActiveDataProvider $dataProvider */


$this->title = 'User Management';


?>


<h1><?= Html::encode($this->title) ?></h1>


<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataProvider->models as $user): ?>
        <tr>
            <td><?= Html::encode($user->ID) ?></td>
            <td><?= Html::encode($user->EMAIL) ?></td>
            <td>
                <?php
                    // Retrieve user's assigned roles
                    $assignedRoles = Yii::$app->authManager->getRolesByUser($user->ID);
                    echo implode(', ', array_keys($assignedRoles));
                    ?>
            </td>
            <td>
                <?= Html::beginForm(['user/assign-role'], 'post') ?>
                <?= Html::hiddenInput('userId', $user->ID) ?>
                <?= Html::dropDownList('roleName', null, [
                        'admin' => 'Admin',
                        'editor' => 'Editor',
                        'viewer' => 'Viewer',
                    ], ['prompt' => 'Select Role']) ?>
                <?= Html::submitButton('Assign Role', ['class' => 'btn btn-primary']) ?>
                <?= Html::endForm() ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>