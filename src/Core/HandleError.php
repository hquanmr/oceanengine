<?php

namespace MySkeleton\Core;

use Exception;

class HandleError
{

    public static function handlerExceptions(Exception $exception)
    {
        try {
            throw $exception;
        } catch (Exception $e) {
            echo $e->getMessage();
            die();

        }
    }

}
