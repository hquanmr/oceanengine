<?php
namespace Leo108\SDK;

use Exception;

/**
 *
 */
class HandleError
{

    public static function handlerExceptions(Exception $e)
    {
        try {
            throw $e;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        die;
    }
}
