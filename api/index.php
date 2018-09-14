<?php
require './vendor/autoload.php';
$app = (new api\App())->get();
$app->run();
