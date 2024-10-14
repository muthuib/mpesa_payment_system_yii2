<?php
// controllers/PaymentController.php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Payment;
use yii\data\ActiveDataProvider;

class PaymentController extends Controller
{
    // ... other actions ...

    public function actionUserPayments()
    {
        $userPhoneNumber = Yii::$app->user->identity->phone_number;

        $dataProvider = new ActiveDataProvider([
    'query' => Payment::find()->where(['phone_number' => $userPhoneNumber]),
        ]);

        return $this->render('user_payments', [
            'dataProvider' => $dataProvider,
        ]);
    }
}