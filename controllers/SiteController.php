<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use GuzzleHttp\Client;
use yii\web\Controller;
use app\models\FormData;
use app\models\LoginForm;
use app\models\MpesaForm;
use app\models\SignupForm;
use app\models\ContactForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\MpesaTransaction;
use app\models\ResetPasswordForm;
use app\models\ChangePasswordForm;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\models\PasswordResetRequestForm;
use GuzzleHttp\Exception\RequestException;
use Mpdf\Mpdf;  // Correct way to import mPDF class

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', "Welcome!! You have logged in successfully");
            // $this->layout = 'dash';
            // return $this->goBack();
            return $this->render('/dashboard/index');
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            // Attempt to sign up the user
            if ($model->signup()) {
                // Redirect if signup is successful
                Yii::$app->session->setFlash('success', 'Please check your email to confirm your account.');
                return $this->redirect(Yii::$app->homeUrl);
            } else {
                // If signup fails, flash error messages are set in the model
                Yii::$app->session->setFlash('error', 'There was a problem signing up. Please ensure your email is not already registered.');
            }
        }

        return $this->render('signup', [
            'model' => $model
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', "You have logged out successfully");
        return $this->redirect('/site/login');
        // return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    /**
     * actionChangePassword
     *
     * @return void
     */
    public function actionChangePassword()
    {
        // Check if the user is logged in
        if (Yii::$app->user->isGuest) {
            // Redirect the user to the login page or display an error message
            Yii::$app->session->setFlash('error', 'Please log in to change your password.');
            return $this->redirect(['site/login']);
        }
        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = Yii::$app->user->identity;
            $user->setPassword($model->newPassword);
            if ($user->save()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('success', 'Password changed successfully.');
                return $this->redirect(['/site/login']);
            } else {
                // var_dump($model->errors);
                // exit;
                Yii::$app->session->setFlash('error', 'Failed to change your password');
            }
        }

        return $this->render('/site/change-password', ['model' => $model]);
    }

    //MPESA PAYMENT FUNCTIONS
    public function actionMpesa()
    {
        $model = new MpesaForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = new MpesaTransaction();
            $transaction->phone_number = $model->phoneNumber;
            $transaction->amount = $model->amount;

            if ($this->initiateMpesaStkPush($model->phoneNumber, $model->amount, $transaction)) {
                Yii::$app->session->setFlash('success', 'Mpesa payment initiated successfully, check your phone and enter Mpesa pin.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to initiate STK push.');
            }

            $transaction->save();

            return $this->refresh();
        }

        return $this->render('mpesa', [
            'model' => $model,
        ]);
    }

    protected function initiateMpesaStkPush($phoneNumber, $amount, $transaction)
    {
        $params = Yii::$app->params['mpesa'];
        $timestamp = date('YmdHis');
        $password = base64_encode($params['shortcode'] . $params['passkey'] . $timestamp);

        $client = new Client();

        try {
            $response = $client->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getMpesaAccessToken(),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'BusinessShortCode' => $params['shortcode'],
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => $amount,
                    'PartyA' => $phoneNumber,
                    'PartyB' => $params['shortcode'],
                    'PhoneNumber' => $phoneNumber,
                    'CallBackURL' => $params['callbackUrl'],
                    'AccountReference' => 'Test123',
                    'TransactionDesc' => 'Payment for XYZ',
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            // Log request and response
            Yii::info('M-Pesa STK Push Request: ' . json_encode([
                'url' => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getMpesaAccessToken(),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'BusinessShortCode' => $params['shortcode'],
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => $amount,
                    'PartyA' => $phoneNumber,
                    'PartyB' => $params['shortcode'],
                    'PhoneNumber' => $phoneNumber,
                    'CallBackURL' => $params['callbackUrl'],
                    'AccountReference' => 'Test123',
                    'TransactionDesc' => 'Payment for XYZ',
                ],
            ]), 'mpesa');
            Yii::info('M-Pesa STK Push Response: ' . json_encode($responseBody), 'mpesa');

            // Save transaction details
            $transaction->transaction_status = 'Success';
            $transaction->request_id = $responseBody['CheckoutRequestID'];
            $transaction->response_code = $responseBody['ResponseCode'];
            $transaction->response_description = $responseBody['ResponseDescription'];

            return isset($responseBody['ResponseCode']) && $responseBody['ResponseCode'] == '0';
        } catch (RequestException $e) {
            // Log request exception
            Yii::error('M-Pesa STK Push Request Exception: ' . $e->getMessage(), 'mpesa');
            Yii::error('M-Pesa STK Push Request Exception Response: ' . $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response', 'mpesa');

            // Save failed transaction details
            $transaction->transaction_status = 'Failed';
            $transaction->response_code = 'N/A';
            $transaction->response_description = $e->getMessage();

            return false;
        }
    }

    protected function getMpesaAccessToken()
    {
        $params = Yii::$app->params['mpesa'];

        $client = new Client();
        $response = $client->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', [
            'auth' => [$params['consumerKey'], $params['consumerSecret']],
        ]);

        $responseBody = json_decode($response->getBody(), true);
        return $responseBody['access_token'];
    }

    // //Email confirmation
    public function actionConfirm($token)
    {
        $user = User::findOne(['VERIFICATION_TOKEN' => $token]);

        if (!$user) {
            // Token is invalid or does not exist
            Yii::$app->session->setFlash('error', 'The link has been used, Invalid or expired.');
            return $this->redirect(['site/login']);
        }

        // Check if the user is already active (the token has already been used)
        if ($user->STATUS == 10) {
            Yii::$app->session->setFlash('error', 'This account has already been verified.');
            return $this->redirect(['site/login']);
        }

        // Mark user as active and clear the verification token
        $user->STATUS = 10;  // User is now active
        $user->VERIFICATION_TOKEN = null;  // Clear the token

        if ($user->save()) {
            Yii::$app->session->setFlash('success', 'Your account has been verified.');
        } else {
            Yii::$app->session->setFlash('error', 'There was a problem verifying your account. Please try again.');
        }

        return $this->redirect(['site/login']);
    }

    // Request password reset
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email, Reset Password link has been sent.');
                return $this->goHome();
            } else {
                // Flash an error message if the email could not be sent
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset your password. No user with this email address found.');
            }
        }

        return $this->render('requestPasswordReset', [
            'model' => $model,
        ]);
    }
    // Reset password
    public function actionResetPassword($token)
    {
        // Find the user by the password reset token
        $user = User::findByPasswordResetToken($token);

        // If no user is found or the token is invalid/expired
        if (!$user || !User::isPasswordResetTokenValid($token)) {
            Yii::$app->session->setFlash('error', 'The password reset token is either used, invalid or expired.');
            return $this->goHome();
        }

        // Initialize the reset password form
        $model = new ResetPasswordForm();

        // Process the form submission
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword($user)) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->goHome();
        }

        // Render the reset password form if not submitted
        return $this->render('resetPassword', [
            'model' => $model,
            'token' => $token,
        ]);
    }
}