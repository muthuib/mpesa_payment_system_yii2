<?php

use app\models\FormData;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\FormDataSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Form Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('createPost')): ?>
        <!-- Show the Create button only if the user has permission to create posts -->
        <?= Html::a('Create Post', ['post/create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_name',
            'email:email',
            'message:ntext',
            'created_at',
            [
                // 'class' => ActionColumn::className(),
                // 'urlCreator' => function ($action, FormData $model, $key, $index, $column) {
                //     return Url::toRoute([$action, 'id' => $model->id]);
                // }
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        // Check if the current user has the 'updatePost' permission
                        return Yii::$app->user->can('updatePost');
                    },
                    'delete' => function ($model, $key, $index) {
                        // You can also control other buttons similarly
                        return Yii::$app->user->can('deletePost');
                    },
                    'view' => function ($model, $key, $index) {
                        // You can also control other buttons similarly
                        return Yii::$app->user->can('viewvPost');
                    },
                ],
            ],
        ],
    ]); ?>


</div>