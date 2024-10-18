<?php

namespace app\models;

use yii\base\Model;

class AssignRoleForm extends Model
{
    public $role;

    public function rules()
    {
        return [
            ['role', 'required'],
        ];
    }
}