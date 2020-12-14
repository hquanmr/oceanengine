<?php

namespace MySkeleton\Core;

/**
 *
 */
use MySkeleton\Core\Exceptions\GetAccessTokenException;

class AccessToken extends BaseApi
{
    const API_GET_TOKEN         = 'oauth2/access_token/';
    const API_GET_REFRESH_TOKEN = 'oauth2/refresh_token/';
    const API_GET_ADVERTISER    = 'oauth2/advertiser/get/';

    public function getToken($refresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cache    = $this->getApp()->getCache();

        if ($refresh || !$ret = $cache->get($cacheKey)) {
            var_dump($refresh);
              var_dump($cache->get($cacheKey));exit();
            $retdata = $this->getTokenFromRefreshToken();
            $this->setToken($retdata);
            return $retdata['access_token'];
        }

        return $ret;
    }

    protected function setToken($retdata)
    {
        $token_cacheKey   = $this->getCacheKey();
        $refresh_cacheKey = $this->getRefreshCacheKey();
        $cache            = $this->getApp()->getCache();
        $cache->set($token_cacheKey, $retdata['access_token'], $retdata['expires_in'] - 1500);
        $cache->set($refresh_cacheKey, $retdata['refresh_token'], $retdata['refresh_token_expires_in'] - 1500);
    }
    public function getTokenFromServer($code)
    {
        $ret = static::parseJson($this->apiJson(self::API_GET_TOKEN, [
            'app_id'     => $this->getApp()->getAppId(),
            'secret'     => $this->getApp()->getSecret(),
            "grant_type" => "auth_code",
            "auth_code"  => $code,
        ], ['headers' => [
            'Content-Type' => 'application/json',
        ]]));
        if (empty($ret['data']['access_token'])) {
            $this->getApp()->handlerExceptions(new GetAccessTokenException('get AccessToken fail. response: ' . json_encode($ret, JSON_UNESCAPED_UNICODE)));
        }

        $this->setToken($ret['data']);

    }
    public function getTokenFromRefreshToken()
    {
        $refresh_cacheKey = $this->getRefreshCacheKey();
        $cache            = $this->getApp()->getCache();
        $refresh_token    = $cache->get($refresh_cacheKey);
        $ret              = static::parseJson($this->apiJson(self::API_GET_REFRESH_TOKEN, [
            'app_id'        => $this->getApp()->getAppId(),
            'secret'        => $this->getApp()->getSecret(),
            "grant_type"    => "refresh_token",
            "refresh_token" => $refresh_token,
        ], ['headers' => [
            'Content-Type' => 'application/json',
        ]]));
        if (empty($ret['data']['access_token'])) {
            $this->getApp()->handlerExceptions(new GetAccessTokenException('get AccessToken fail. response: ' . json_encode($ret, JSON_UNESCAPED_UNICODE)));
        }

        return $ret['data'];
    }
    public function getCacheKey()
    {
        $prefix = $this->getApp()->getCacheKeyPrefix();

        return sprintf('%s.access_token', $prefix);
    }
    public function getRefreshCacheKey()
    {
        $prefix = $this->getApp()->getCacheKeyPrefix();

        return sprintf('%s.refresh_token', $prefix);
    }

    public function getAdvertiser()
    {
        return static::parseJson($this->apiGet(self::API_GET_ADVERTISER, [
            'app_id'       => $this->getApp()->getAppId(),
            'secret'       => $this->getApp()->getSecret(),
            'access_token' => $this->getToken(),
        ]));
    }

    protected function getAppName()
    {
        // disable token middleware
        return null;
    }
}
