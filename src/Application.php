<?php
namespace MySkeleton;

use GuzzleHttp\ClientInterface;
use Leo108\SDK\SDK;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use MySkeleton\Contact\Account;
use MySkeleton\Contact\Advert;
use MySkeleton\Contact\Campaign;
use MySkeleton\Contact\Creative;
use MySkeleton\Core\AccessToken;
use MySkeleton\Core\Exceptions\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class Application extends SDK
{
    /**
     * @var string
     */
    protected $appId;
    protected $secrets;
    protected $cache          = null;
    protected $cacheKeyPrefix = 'oceanengine';
    protected $logPath;
    public function __construct(
        array $config = [],
        CacheInterface $cache = null,
        ClientInterface $httpClient = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($config, $httpClient, new Logger('my_logger'));

        $this->cache = $cache ?: new Psr16Cache(new FilesystemAdapter());
        $this->parseConfig($config);
        $stream = new StreamHandler($this->logPath, Logger::INFO);
        $this->logger->pushHandler($stream);
    }

    public function getAppId()
    {
        return $this->app_id;
    }

    public function getSecret()
    {
        return $this->secrets;
    }
    public function getCache()
    {
        return $this->cache;
    }

    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    public function getCacheKeyPrefix()
    {
        return $this->cacheKeyPrefix;
    }

    protected function getApiMap()
    {
        return [
            'accessToken' => AccessToken::class,
            'account'     => Account::class,
            'campaign'    => Campaign::class,
            'advert'      => Advert::class,
            'creative'    => Creative::class,
        ];
    }

    protected function parseConfig(array $config)
    {

        if (!isset($config['app_id'])) {
            $this->handleError(new InvalidArgumentException('缺少 app_id 参数'));
        }

        $this->app_id = $config['app_id'];

        if (!isset($config['secrets']) || empty($config['secrets'])) {
            $this->handleError(new InvalidArgumentException('缺少 secrets 参数'));
        }

        $this->secrets = $config['secrets'];

        if (isset($config['cache_key_prefix'])) {
            $this->cacheKeyPrefix = $config['cache_key_prefix'];
        }

        $this->logPath = $config['log']['path'] ?? \sys_get_temp_dir() . '/ad/ad.log';
        $this->config  = $config;
    }

}
