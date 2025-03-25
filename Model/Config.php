<?php

declare(strict_types=1);

namespace ProDevTools\QueryLogger\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const PATH_TO_ENABLE_CONFIG = 'query_logger/general/enable';
    private const PATH_TO_TABLE_LIST = 'query_logger/general/table_list';
    private const PATH_TO_PARAM_QUERY_TIME = 'query_logger/general/query_time_threshold';
    private const PATH_TO_PARAM_CALL_STACK = 'query_logger/general/include_stacktrace';

    /**
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        private readonly ScopeConfigInterface $config
    ) {
    }

    /**
     * Get Flag Value
     *
     * @param string $path
     * @return bool
     */
    public function getFlagValue(string $path): bool
    {
        return $this->config->isSetFlag(
            $path,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get Store Config
     *
     * @param string $key
     * @return mixed
     */
    public function getStoreConfig(string $key)
    {
        return $this->config->getValue(
            $key,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Is Enable
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->getFlagValue(self::PATH_TO_ENABLE_CONFIG);
    }

    /**
     * Get Table List
     *
     * @return string
     */
    public function getTableList(): string
    {
        return $this->getStoreConfig(self::PATH_TO_TABLE_LIST) ?? '';
    }

    /**
     * Get Query Time Threshold
     *
     * @return float
     */
    public function getQueryTimeThreshold(): float
    {
        return (float)($this->getStoreConfig(self::PATH_TO_PARAM_QUERY_TIME) ?? 0.001);
    }

    /**
     * Is Log CallStack
     *
     * @return bool
     */
    public function isLogCallStack(): bool
    {
        return $this->getFlagValue(self::PATH_TO_PARAM_CALL_STACK);
    }
}
