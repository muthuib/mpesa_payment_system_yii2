<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * classSignupForm
 * 
 * @author Benjamin Muthui <benmuthui98@gmail.com>
 * @package app\models
 */
class SignupForm extends Model
{
    public $email;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['email', 'password', 'password_repeat'], 'required'],
            [['email', 'password', 'password_repeat'], 'string', 'min' => 4, 'max' => 100],
            ['password_repeat', 'compare', 'compareAttribute' => 'password']
        ];
    }
    // check if the email is registered
    public function validateEmail($attribute, $params)
    {
        if (User::find()->where(['email' => $this->email])->exists()) {
            $this->addError($attribute, 'This email address is already registered.');
        }
    }
    public function signup()
    {
        $user = new User();
        $user->EMAIL = $this->email;
        $user->PASSWORD = yii::$app->security->generatePasswordHash($this->password);
        $user->ACCESS_TOKEN = yii::$app->security->generateRandomString();
        $user->AUTH_KEY = yii::$app->security->generateRandomString();
        //generate confirmation token during sign up
        $user->generateEmailVerificationToken();
        $user->status = 9;  // User initially inactive
        $user->save();

        if ($user->save()) {
            $user->generateEmailVerificationToken();
            $user->save();
            $user->sendConfirmationEmail($user);

            Yii::$app->session->setFlash('success', 'Please check your email to confirm your account.');
            return Yii::$app->response->redirect(['site/index']);
        }
    }
}