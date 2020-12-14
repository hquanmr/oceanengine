<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/8/14
 * Time: 11:44
 */

namespace MySkeleton\Core\Middleware;

use Leo108\SDK\Middleware\MiddlewareInterface;
use MySkeleton\Core\Exceptions\ApiException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use MySkeleton\Core\HandleError;
class CheckApiResponseMiddleware implements MiddlewareInterface
{
    /**
     * @var callable|bool
     */
    protected $shouldCheck;

    /**
     * @var callable
     */
    protected $responseParser;

    /**
     * CheckApiResponseMiddleware constructor.
     * @param callable|boolean $shouldCheck
     * @param callable         $responseParser
     */
    public function __construct($shouldCheck, callable $responseParser)
    {

        $this->shouldCheck    = $shouldCheck;
        $this->responseParser = $responseParser;
    }

    public function __invoke()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (is_bool($this->shouldCheck)) {
                    $shouldCheck = $this->shouldCheck;
                } else {
                    $shouldCheck = call_user_func($this->shouldCheck, $request);
                }
                if (!$shouldCheck) {
                    return $handler($request, $options);
                }

                return $handler($request, $options)->then(
                    function (ResponseInterface $response) {
  // throw new ApiException('decode failed, response:' . $response->getBody());
                        $ret = call_user_func($this->responseParser, $response);
                        if (!$ret) {
                            call_user_func([HandleError::class, 'handlerExceptions'], new ApiException('decode failed, response:' . $response->getBody()));
                        }
                        if ($ret['code'] != 0) {
                             call_user_func([HandleError::class, 'handlerExceptions'], new ApiException('decode failed, response:' . $response->getBody()));
                        }

                        return $response;
                    },
                    function ($reason) {
                        throw new ApiException($reason);
                    }
                );
            };
        };
    }
}