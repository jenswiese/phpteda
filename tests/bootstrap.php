<?php

spl_autoload_register(function($class) {
    $file = __DIR__ . '/../src/' . strtr($class, '\\', '/').'.php';
    if (file_exists($file)) {
      require_once $file;
      return true;
    }

    return false;
});