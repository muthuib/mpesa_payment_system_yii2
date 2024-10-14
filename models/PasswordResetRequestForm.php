<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'email'],
        ];
    }

    public function sendEmail()
    {
        $user = User::findOne(['EMAIL' => $this->email]);
        if (!$user) {
            return false;
        }
        // If no user is found
        if (!$user) {
            Yii::$app->session->setFlash('error', 'No user with this email address found.');
            return false;
        }

        // Check if the user's account is active (adjust based on your user model)
        if ($user->STATUS !== User::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('error', 'This email is not activated.');
            return false;
        }
        $user->generatePasswordResetToken();
        if ($user->save()) {
            return Yii::$app->mailer->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($this->email)
                ->setSubject('Password reset for ' . Yii::$app->name)
                ->setTextBody('Please click the following link to reset your password: ' .
                    Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->PASSWORD_RESET_TOKEN]))
                ->send();
        }
        return false;
    }
}