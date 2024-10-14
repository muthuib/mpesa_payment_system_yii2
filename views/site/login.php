<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';

?>
<div class="site-login">
    <h1 class="animate__animated animate__fadeInDown"><?= Html::encode($this->title) ?></h1>

    <p class="animate__animated animate__fadeIn">Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <div class="card animate__animated animate__zoomIn">
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
                            'inputOptions' => ['class' => 'form-control animate__animated animate__fadeInUp'],
                            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"custom-control custom-checkbox animate__animated animate__fadeInUp\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    ]) ?>

                    <div class="form-group">
                        <div>
                            <?= Html::submitButton('Login', [
                                'class' => 'btn btn-primary login-btn animate__animated animate__pulse',
                                'name' => 'login-button',
                                'style' => 'width: 380px;',
                            ]) ?>
                        </div>
                        <p class="animate__animated animate__fadeInUp">
                            <?= Html::a('Forgot password?', ['site/request-password-reset'], ['class' => 'forgot-password-link']) ?>
                        </p>
                    </div>

                    <div class="animate__animated animate__fadeInUp">
                        <p>Don’t have an account? <?= Html::a('Sign up', ['site/signup']) ?></p>
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
    .login-btn:hover {
        background-color: #0056b3;
        transition: background-color 0.3s ease;
    }
    .forgot-password-link {
        color: #0056b3;
        text-decoration: none;
    }
    .forgot-password-link:hover {
        text-decoration: underline;
    }
');
?>
<!-- remember to include the following in main.php -->
<!-- <//?php
        // Inside <head> tag of your layout (main.php)
        $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
        ?>
 -->