<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\httpclient\Client;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property string $BusinessShortCode
 * @property string $Password
 * @property string $Timestamp
 * @property string $TransactionType
 * @property float $Amount
 * @property string $PartyA
 * @property string $PartyB
 * @property string $phone_number
 * @property string $CallBackURL
 * @property string $AccountReference
 * @property string $TransactionDesc
 * @property int $created_at
 * @property int $updated_at
 */
class Mpesa extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mpesa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['BusinessShortCode', 'Password', 'Timestamp', 'TransactionType', 'Amount', 'PartyA', 'PartyB', 'phone_number', 'CallBackURL', 'AccountReference', 'TransactionDesc', 'created_at', 'updated_at'], 'required'],
            [['Timestamp'], 'safe'],
            [['Amount'], 'number'],
            [['created_at', 'updated_at'], 'integer'],
            [['BusinessShortCode', 'Password', 'TransactionType', 'PartyA', 'PartyB', 'phone_number', 'CallBackURL', 'AccountReference', 'TransactionDesc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'BusinessShortCode' => 'Business Short Code',
            'Password' => 'Password',
            'Timestamp' => 'Timestamp',
            'TransactionType' => 'Transaction Type',
            'Amount' => 'Amount',
            'PartyA' => 'Party A',
            'PartyB' => 'Party B',
            'phone_number' => 'Phone Number',
            'CallBackURL' => 'Call Back URL',
            'AccountReference' => 'Account Reference',
            'TransactionDesc' => 'Transaction Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Initiates an STK push transaction.
     * 
     * @param string $phone_number
     * @param float $amount
     * @param string $accountReference
     * @param string $transactionDesc
     * @return array
     */

    /**
     * Get the access token for Mpesa API.
     * 
     * @return string
     */
    private function getAccessToken()
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials') // Use the correct URL for the API
            ->setHeaders(['Authorization' => 'Basic ' . base64_encode(Yii::$app->params['mpesaConsumerKey'] . ':' . Yii::$app->params['mpesaConsumerSecret'])])
            ->send();

        if ($response->isOk) {
            return $response->data['access_token'];
        } else {
            throw new \Exception('Unable to get access token: ' . json_encode($response->data));
        }
    }
    
    public function stkPush($phone_number, $amount, $accountReference, $transactionDesc)
    {
        $timestamp = date('YmdHis');
        $password = base64_encode($this->BusinessShortCode . $this->Password . $timestamp);

        $data = [
            'BusinessShortCode' => $this->BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone_number,
            'PartyB' => $this->BusinessShortCode,
            'PhoneNumber' => $phone_number,
            'CallBackURL' => $this->CallBackURL,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc
        ];

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest') // Use the correct URL for the API
            ->setHeaders(['Authorization' => 'Bearer ' . $this->getAccessToken()])
            ->setData($data)
            ->send();

        if ($response->isOk) {
            return $response->data;
        } else {
            return ['error' => $response->data];
        }
    }

    
}