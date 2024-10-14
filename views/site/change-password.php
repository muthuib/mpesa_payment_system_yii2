<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Change Password';
?>
<div class="card shadow p-3">
    <h1><?= $this->title ?> </h1>
    <div class="form-control-sm">
        <h1 class="card-title">Please input old password and new password to update.</h1>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row row-cols-lg-3 row-cols-md-2 row-cols-1">
            <div class="col">
                <?= $form->field($model, 'oldPassword')->passwordInput() ?>
            </div>
            <div class="col">
                <?= $form->field($model, 'newPassword')->passwordInput() ?>
            </div>
            <div class="col">
                <?= $form->field($model, 'confirmPassword')->passwordInput() ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Change Password', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>