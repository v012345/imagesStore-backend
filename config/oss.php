<?php

/**
 * object store service
 */
return [
    'key' => env('ALIYUN_OSS_ACCESS_KEY_ID'),
    'secret' => env('ALIYUN_OSS_ACCESS_KEY_SECRET'),
    'endpoint' => env('ALIYUN_OSS_ENDPOINT', "http://oss-cn-hangzhou.aliyuncs.com"),
];
