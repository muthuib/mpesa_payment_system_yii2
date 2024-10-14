<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'supportEmail' => 'support@example.com', // Email used for sending emails
    'adminEmail' => 'admin@example.com', // email to used to send password reset link,,,you can Replace with your admin email
    'user.passwordResetTokenExpire' => 3600,  // Token expires after 1 hour

    'mpesa' => [
        'consumerKey' => 'mAEp5BmBSX3VgmS8CLLQbsNeqjf80LV1pGaDhbEFLnRrFiK9',
        'consumerSecret' => 'TqLuovFutfjxrGVeui9bRXB3BpDHPJGolDrWQTeS9SXQWelvUseF64UiNicA0O3q',
        'shortcode' => '174379',
        'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
        'callbackUrl' => 'https://prototype-pms.000webhostapp.com/admin/index.php',
    ],
];