<?php

namespace App\Bootstrap;

use Dotenv\Dotenv;
use Exception;

class DetectEnvironment
{
    public function bootstrap()
    {
        try {
            (new Dotenv(__DIR__.'/../../'))->load();
        }
        catch (Exception $e) {
            //
            echo $e->getMessage();
            exit();
        }
    }
}


