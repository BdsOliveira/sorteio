<?php 

// box/spout (abandonado) gera avisos de depreciação no PHP 8.1+
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

session_start();

require __DIR__ .'/src/App.php';