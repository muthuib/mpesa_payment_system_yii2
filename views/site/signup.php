<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Signup';

?>
<div class="site-signup">
    <h1 class="animate__animated animate__fadeInDown"><?= Html::encode($this->title) ?></h1>

    <p class="animate__animated animate__fadeIn">Please fill out the following fields to Register:</p>

    <div class="row">
        <div class="col-lg-5">
            <div class="card animate__animated animate__zoomIn">
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'signup-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3 animate__animated animate__fadeInUp'],
                            'inputOptions' => ['class' => 'form-control animate__animated animate__fadeInUp'],
                            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                    <div class="form-group">
                        <div>
                            <?= Html::submitButton('Register', [
                                'class' => 'btn btn-primary signup-btn animate__animated animate__pulse',
                                'name' => 'signup-button',
                                'style' => 'width: 380px;',
                            ]) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Register custom CSS for button hover animation
$this->registerCss('
    .signup-btn:hover {
        background-color: #0056b3;
        transition: background-color 0.3s ease;
    }
');
?>