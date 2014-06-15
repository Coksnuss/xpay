<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => 'noreply@test.tld',
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
    ],
];
