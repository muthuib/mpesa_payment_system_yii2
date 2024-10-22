<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Assign Permissions to Role: ' . $role->name;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'permissions')->checkboxList(
    array_map(function ($permission) {
        return $permission->description ?: $permission->name;
    }, $permissions)
) ?>

<div class="form-group">
    <?= Html::submitButton('Assign Permissions', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>