<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $ID
 * @property string $PASSWORD
 * @property string|null $EMAIL
 * @property string|null $AUTH_KEY
 * @property string|null $ACCESS_TOKEN
 * @property string|null $PASSWORD_RESET_TOKEN
 * @property int|null $STATUS
 * @property int|null $CREATED_AT
 * @property int|null $UPDATED_AT
 * @property string|null $VERIFICATION_TOKEN
 * @property int $USER_ROLE
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PASSWORD', 'USER_ROLE'], 'required'],
            [['STATUS', 'CREATED_AT', 'UPDATED_AT', 'USER_ROLE'], 'integer'],
            [['PASSWORD'], 'string', 'max' => 255],
            [['EMAIL', 'AUTH_KEY', 'ACCESS_TOKEN', 'PASSWORD_RESET_TOKEN', 'VERIFICATION_TOKEN'], 'string', 'max' => 100],
            [['EMAIL'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'PASSWORD' => 'Password',
            'EMAIL' => 'Email',
            'AUTH_KEY' => 'Auth Key',
            'ACCESS_TOKEN' => 'Access Token',
            'PASSWORD_RESET_TOKEN' => 'Password Reset Token',
            'STATUS' => 'Status',
            'CREATED_AT' => 'Created At',
            'UPDATED_AT' => 'Updated At',
            'VERIFICATION_TOKEN' => 'Verification Token',
            'USER_ROLE' => 'User Role',
        ];
    }
}
