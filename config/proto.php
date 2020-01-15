<?php

return [

    /*
    |--------------------------------------------------------------------------
    | App Constants
    |--------------------------------------------------------------------------
    |
    */

    'role' => [
        'admin' => 1,
        'staff' => 2,
    ],

    'uploads' => [
        'avatar' => 'uploads/avatars',
        'staff_docs' => 'uploads/staff/documents',
        'staff_notes' => 'uploads/staff/notes',
        'staff_doc_pages' => 'uploads/staff/documents/pages',
        'notice_cover' => 'uploads/notices/covers',
        'client_docs' => 'uploads/client/documents',
        'client_doc_pages' => 'uploads/client/documents/pages',
        'tmp' => 'uploads/tmp',
        'staff_videos' => 'uploads/staff/videos',
        'staff_vid_temp' => 'uploads/staff/videos/temp'	        
    ],

    'defaults' => [
        'pagination' => 10,
    ],

    'emails' => [
        'feedback' => [
            'email' => 'support@mee2box.com',
            'name' => 'Mee2box',
            'cc' => [
                [
                    'email' => 'jose.doe@mailinator.com',
                    'name' => 'Test'
                ]
            ]
        ],
        'forgot_password' => [
            'title' => 'Mee2box Reset Password',
            'email' => 'info@mee2box.com',
            'name' => 'Mee2box運営事務局'
        ],
    ]

];
