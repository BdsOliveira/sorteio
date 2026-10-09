<?php

// Servidor embutido do PHP (php -S ... api/index.php): entrega os arquivos de assets/ diretamente
if (PHP_SAPI === 'cli-server' && str_starts_with($_SERVER['REQUEST_URI'], '/assets/')) {
    return false;
}

// Ponto de entrada da função PHP na Vercel (vercel-php)
require __DIR__ . '/../index.php';
