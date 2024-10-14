<?php
// views/site/mpesa.php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'M-Pesa Payment';
?>
<div class="site-mpesa">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to initiate an M-Pesa payment:</p>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'phoneNumber')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'amount')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
