<?php
// models/MpesaForm.php

namespace app\models;

use yii\base\Model;

class MpesaForm extends Model
{
    public $phoneNumber;
    public $amount;

    public function rules()
    {
        return [
            [['phoneNumber', 'amount'], 'required'],
            ['phoneNumber', 'match', 'pattern' => '/^[0-9]{10,12}$/'], // Assuming Kenyan phone numbers
            ['amount', 'number', 'min' => 1],
        ];
    }
}
