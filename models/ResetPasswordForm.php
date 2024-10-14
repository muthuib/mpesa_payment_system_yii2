<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;  // Add password_repeat field

    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],  // Both fields are required
            [['password'], 'string', 'min' => 6],  // Minimum password length
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match"],  // Ensure passwords match
        ];
    }

    public function resetPassword($user)
    {
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save();
    }
}