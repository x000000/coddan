<?php

error_reporting(E_ALL);

require_once 'App.php';

$app = new App(require 'config/web.php');
$app->start();