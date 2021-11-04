<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'weixin' => [
        'appid' => 'wx79cb0a612897f300',
        'appSecret' => 'f8e40462235a56381867fae9b8248d6f',
        'pay' => [
            'key' => 'F79889016055B4E04867107489A802A9',
            'mch_id' => '1550830641',
            'notify_url' => [
                'pay' => '/pays/call'
            ]
        ]
    ]

];
