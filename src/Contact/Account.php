<?php

namespace MySkeleton\Contact;

class Account extends BaseContactApi
{

    const API_GET_USER_INFO = '2/user/info/';

    public function getUserInfo()
    {

        return static::parseJson($this->apiGet(self::API_GET_USER_INFO));
    }
}
