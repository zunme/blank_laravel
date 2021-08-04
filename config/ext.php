<?php
return [
    'user' => [
        'user_level' => [
            'default' => 100,
            'roles' => [
                'admin' => [
                    'label' => '관리자',
                    'level' => 1024,
                ],
                'user' => [
                    'label' => '회원',
                    'level' => 100,
                ]
            ]
        ],
    ],
];