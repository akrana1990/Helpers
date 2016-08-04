<?php

/*
 * ------------------------------------------------------
 *  Load any environment-specific settings from .env file
 * ------------------------------------------------------
 * Load environment settings from .env files
 * into $_SERVER and $_ENV
 *
 */

$env = new \App\Bootstrap\DetectEnvironment();
$env->bootstrap();
unset($env);