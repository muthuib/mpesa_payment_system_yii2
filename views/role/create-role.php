<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Create Role';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'description')->textarea() ?>
<div class="form-group">
    <?= Html::submitButton('Create Role', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>