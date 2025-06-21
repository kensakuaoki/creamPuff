<?php
return [
    'region' => getenv('AWS_REGION') ?: 'us-east-1',
    'version' => 'latest',
    'credentials' => [
        'key' => getenv('AWS_ACCESS_KEY_ID'),
        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    ],
    'cognito' => [
        'user_pool_id' => getenv('AWS_COGNITO_USER_POOL_ID'),
        'client_id' => getenv('AWS_COGNITO_CLIENT_ID'),
    ],
    's3_bucket' => getenv('AWS_S3_BUCKET'),
];
