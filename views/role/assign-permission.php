<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Assign Permissions to Role: ' . $role->name;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?php
// Categorize permissions into 'CRUD' (Create, Read/View, Update, Delete) and 'Other'
$crudPermissions = [];
$otherPermissions = [];

foreach ($permissions as $permission) {
    $name = $permission->description ?: $permission->name;

    // Check if permission contains 'create', 'view', 'update', or 'delete'
    if (preg_match('/create|view|update|delete/i', $permission->name)) {
        $crudPermissions[$permission->name] = $name;
    } else {
        $otherPermissions[$permission->name] = $name;
    }
}
?>

<!-- Display Create, Update, Delete, View Permissions (CRUD) -->
<h3>CRUD Permissions (Create, Update, Delete, View)</h3>
<?= $form->field($model, 'permissions')->checkboxList($crudPermissions)->label(false) ?>

<!-- Display Other Permissions -->
<h3>Other Permissions</h3>
<?= $form->field($model, 'permissions')->checkboxList($otherPermissions)->label(false) ?>

<div class="form-group">
    <?= Html::submitButton('Assign Permissions', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>