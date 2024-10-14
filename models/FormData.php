<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "form_data".
 *
 * @property int $id
 * @property string $user_name
 * @property string $email
 * @property string $message
 * @property string $created_at
 */
class FormData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'form_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_name', 'email', 'message'], 'required'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
            [['user_name', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'email' => 'Email',
            'message' => 'Message',
            'created_at' => 'Created At',
        ];
    }
}
