<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Update Role: ' . $model->name;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name')->textInput(['readonly' => true]) ?>
<?= $form->field($model, 'description')->textarea() ?>

<div class="form-group">
    <?= Html::submitButton('Update Role', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>