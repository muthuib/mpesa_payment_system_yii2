<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\FormData $model */

$this->title = 'Update Form Data: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Form Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="form-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('updatePost')): ?>
    <!-- Show the Update button only if the user has permission to update posts -->
    <?= Html::a('Update Post', ['post/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    <?php endif; ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>