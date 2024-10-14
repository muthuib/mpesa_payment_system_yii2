<p>Hello <?= $user->EMAIL ?>,</p>
<p>Thank you for signing up. Please click the link below to confirm your email:</p>
<p><?= Yii::$app->urlManager->createAbsoluteUrl(['site/confirm', 'token' => $user->VERIFICATION_TOKEN]) ?></p>