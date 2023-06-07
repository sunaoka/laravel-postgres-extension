<?php

return [
    'json_encode_options'        => JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION,
    'information_schema_caching' => true,
    'additional_dns_string'      => sprintf(";application_name='%s'", env('DB_APPLICATION_NAME', env('APP_NAME'))),
];
