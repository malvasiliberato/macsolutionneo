<?php

return [
    'connection' => env('LEGACY_IMPORT_DB_CONNECTION', 'legacy_mysql'),

    'cli' => [
        'binary' => env('LEGACY_IMPORT_MYSQL_CLI_BINARY', 'mysql'),
        'user' => env('LEGACY_IMPORT_MYSQL_CLI_USER', 'root'),
    ],

    'auth_org' => [
        'tables' => [
            'dealer' => env('LEGACY_IMPORT_AUTH_ORG_DEALER_TABLE', 'dealer'),
            'membership' => env('LEGACY_IMPORT_AUTH_ORG_MEMBERSHIP_TABLE', 'dealer_user_assignment'),
            'collaborator' => env('LEGACY_IMPORT_AUTH_ORG_COLLABORATOR_TABLE', 'dealer_collaboratore'),
            'commercial' => env('LEGACY_IMPORT_AUTH_ORG_COMMERCIAL_TABLE', 'commerciali'),
            'assignment' => env('LEGACY_IMPORT_AUTH_ORG_ASSIGNMENT_TABLE', 'dealer_operatore_figura'),
        ],
        'create_only' => [
            'synthetic_email_domain' => env('LEGACY_IMPORT_AUTH_ORG_SYNTHETIC_EMAIL_DOMAIN', 'legacy-auth.macsolution-neo.local'),
        ],
    ],
];
