<?php

use yii\helpers\Html;

$this->title = 'Manage Roles';
?>
<h1><?= Html::encode($this->title) ?></h1>
<h1>Role and Permission Management</h1>

<h2>Roles</h2>
<ul>
    <?php foreach ($roles as $role): ?>
    <li>
        <?= Html::encode($role->name) ?>
        <?= Html::a('Update', ['role/update-role', 'name' => $role->name]) ?>
        <?= Html::a('Delete', ['role/delete-role', 'name' => $role->name], ['data-method' => 'post']) ?>
    </li>
    <?php endforeach; ?>
</ul>

<p><?= Html::a('Create New Role', ['role/create-role'], ['class' => 'btn btn-success']) ?></p>

<h2>Permissions</h2>
<ul>
    <?php foreach ($permissions as $permission): ?>
    <li><?= Html::encode($permission->name) ?></li>
    <?php endforeach; ?>
</ul>