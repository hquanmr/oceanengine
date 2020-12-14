<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/8/28
 * Time: 08:14
 */

namespace MySkeleton\Contact;

use MySkeleton\Core\BaseApi;

abstract class BaseContactApi extends BaseApi
{
    protected function getAppName()
    {
        return 'contact';
    }
}
