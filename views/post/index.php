<?php

use app\models\Post;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('createPost')): ?>
        <!-- Show the Create button only if the user has permission to create posts -->
        <?= Html::a('Create Post', ['post/create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'title',
        'content:ntext',
        'author_id',
        'created_at',
        //'updated_at',
        [
            'class' => ActionColumn::className(),
            'urlCreator' => function ($action, Post $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
            }
        ],
    ],
]); ?>


</div>