<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Create Permission';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($permissionForm, 'permission')->textInput(['maxlength' => true]) ?>
<div class="form-group">
    <?= Html::submitButton('Create Permission', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>