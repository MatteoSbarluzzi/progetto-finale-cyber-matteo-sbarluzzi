<?php
return [
    'allowed_admin_hosts' => array_filter(array_map('trim', explode(',', env('ADMIN_ALLOWED_HOSTS', 'internal.admin')))),
];
