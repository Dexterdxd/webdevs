<?php
session_start();
$fb = new Facebook\Facebook([
    'app_id' => '{app-id}', 
    'app_secret' => '{app-secret}',
    'default_graph_version' => 'v3.2',
]);