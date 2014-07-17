<?php
return [
	'name' => 'xPay',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'libreidapi' => [
            'class' => 'common\vendor\LibreIdApi',
            'apiKey' => 'nKeVcdtWNEoCQyw3xN8Q6tM1gqUz7wYx',
            'secretKey' => base64_decode("O+mODkHB+ZNqQ3aM7y3SCKb6o9JlQKlxQppwUav8JTiBNcmh", true),
        ],
        'secauthapi' => [
        	'class' => 'common\vendor\SecAuthClient',
        ],
        'user' => [
            'class' => 'common\components\ExtendedUser',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => 'xpay@wsp.lab.sit.cased.de',
            ],
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'mail.wsp.lab.sit.cased.de',
                'username'   => 'xpay@wsp.lab.sit.cased.de',
                'password'   => 'O}j[bmi[wb+E',
                'port'       => '465',
                'encryption' => 'ssl',
            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        // Alternative mail account
        'altMail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => 'ops1@wsp.lab.sit.cased.de',
            ],
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'mail.wsp.lab.sit.cased.de',
                'username'   => 'ops1@wsp.lab.sit.cased.de',
                'password'   => 'S@:zf>oi=e(R',
                'port'       => '465',
                'encryption' => 'ssl',
            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'security' => [
        		'cryptBlockSize' => 16,
                'cryptKeySize' => 24,
                'derivationIterations' => 1000,
                'deriveKeyStrategy' => 'hmac', // for PHP version < 5.5.0
                //'deriveKeyStrategy' => 'pbkdf2', // for PHP version >= 5.5.0
                'useDeriveKeyUniqueSalt' => false,
        ],
    ],
];
