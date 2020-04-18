<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
  $dotenv->load();
} catch (Exception $e) {
  echo "{$e->getMessage()}\n";
}

require_once __DIR__.'/includes/OAuthTestCase.php';
