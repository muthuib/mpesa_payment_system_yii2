<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Assign Role';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'role')->dropDownList($availableRoles) ?>
<div class="form-group">
    <?= Html::submitButton('Assign Role', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>