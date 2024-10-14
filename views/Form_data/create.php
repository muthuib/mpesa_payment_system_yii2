<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\FormData $model */

$this->title = 'Create Form Data';
$this->params['breadcrumbs'][] = ['label' => 'Form Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
