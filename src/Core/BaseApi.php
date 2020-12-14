<?php

namespace MySkeleton\Core;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Leo108\SDK\AbstractApi;
use Leo108\SDK\Middleware\TokenMiddleware;
use MySkeleton\Core\Middleware\CheckApiResponseMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

abstract class BaseApi extends AbstractApi
{
    // protected $app;

    protected function getApp()
    {
        return $this->sdk;
    }

    protected function getFullApiUrl($api)
    {
        return 'https://ad.oceanengine.com/open_api/' . ltrim($api, '/');
    }

    public static function parseJson(ResponseInterface $response)
    {
        return \GuzzleHttp\json_decode($response->getBody(), true, 512, JSON_BIGINT_AS_STRING);
    }

    protected function getHttpMiddleware()
    {
        return array_filter([
            $this->getCheckApiResponseMiddleware(),
            $this->getRetryMiddleware(),
            $this->getTokenMiddleware(),
            $this->getLogRequestMiddleware(),
        ]);
    }
    /**
     * @return CheckApiResponseMiddleware
     */
    protected function getCheckApiResponseMiddleware()
    {
        return new CheckApiResponseMiddleware(true, [static::class, 'parseJson']);
    }

    /**
     * @return callable
     */
    protected function getLogRequestMiddleware()
    {

        $logger    = $this->getApp()->getLogger();
        $formatter = new MessageFormatter($this->getApp()->getConfig('log.format', MessageFormatter::CLF));
        $logLevel  = $this->getApp()->getConfig('log.level', LogLevel::INFO);

        return Middleware::log($logger, $formatter, $logLevel);
    }

    /**
     * @return TokenMiddleware
     */
    protected function getTokenMiddleware()
    {
        $app = $this->getAppName();
        if (is_null($app)) {
            return null;
        }

        return new TokenMiddleware(true, function (RequestInterface $request) {
            return $this->attachAccessToken($request);
        });
    }

    /**
     * @return callable
     */
    protected function getRetryMiddleware()
    {
        return Middleware::retry(function ($retries, RequestInterface $request, ResponseInterface $response = null) {
            if ($retries >= $this->getApp()->getConfig('api_retry', 3)) {
                return false;
            }
            if (!$response || $response->getStatusCode() >= 400) {
                return true;
            }

            $ret = static::parseJson($response);

            if (in_array($ret['code'], ['40104', '40102'])) {
                // 刷新 access token
                $this->getApp()->accessToken->getToken(true);

                return true;
            }

            return false;
        });
    }
    /**
     * 在请求的 url 后加上 access_token 参数
     *
     * @param string           $app
     * @param RequestInterface $request
     * @param bool             $cache
     *
     * @return RequestInterface
     */
    private function attachAccessToken(RequestInterface $request, $cache = true)
    {

        $access_token = $this->getApp()->accessToken->getToken();
        $request->withHeader("Content-Type", 'application/json');
        return $request->withHeader("Access-Token", $access_token);
    }

    /**
     * @param string|\DateTime $date
     * @return string
     */
    protected function formatDate($date)
    {
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }

        return $date;
    }

    abstract protected function getAppName();
}
