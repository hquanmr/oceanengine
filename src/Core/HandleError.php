<?php

namespace MySkeleton\Core;

use Exception;

class HandleError
{

    public static function handlerExceptions(Exception $e)
    {
        try {
            //
        } catch (Exception $e) {
            throw $e;

        }
    }

}
