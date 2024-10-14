<?php
// views/payment/user_payments.php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'My Payments';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'phone_number',
        'amount',
        'request_id',
        'response_code',
        'status',
        'created_at',
    ],
]); ?>