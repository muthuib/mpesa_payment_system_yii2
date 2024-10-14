<?php
// models/ChangePasswordForm.php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $confirmPassword;


    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'confirmPassword'], 'required'],
            ['newPassword', 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Passwords do not match.'],
            ['oldPassword', 'validateOldPassword'],
        ];
    }

    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = Yii::$app->user->identity;
            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, 'Incorrect old password.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'oldPassword' => 'Old Password',
            'newPassword' => 'New Password',
            'confirmPassword' => 'Confirm Password',
        ];
    }
    public function setPassword($PASSWORD)
    {
        return $this->PASSWORD = Yii::$app->security->generatePasswordHash($PASSWORD);
    }

}