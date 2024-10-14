<?php

namespace app\components;

use Yii;
use yii\base\Component;

class Mpesa extends Component
{
    public $consumerKey;
    public $consumerSecret;
    public $environment; // 'sandbox' or 'production'

    public function getAccessToken()
    {
        $url = $this->environment === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $credentials,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Yii::error('Curl error: ' . curl_error($ch), __METHOD__);
            return null;
        }

        curl_close($ch);
        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            return $result['access_token'];
        }

        Yii::error('Error getting access token: ' . $response, __METHOD__);
        return null;
    }
}