<?php

declare(strict_types=1);

namespace ProDevTools\QueryLogger\Observer;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Logger\LoggerProxy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use ProDevTools\QueryLogger\Console\Command\QueryLogEnableCommand;
use ProDevTools\QueryLogger\Model\Config;
use Psr\Log\LoggerInterface;


class UpdateDBLoggerConfiguration implements ObserverInterface
{
    /**
     * @param Writer $deployConfigWriter
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Writer $deployConfigWriter,
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Enable/disable the DB log using admin settings.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnable()) {
            try {
                $data = [LoggerProxy::PARAM_ALIAS => LoggerProxy::LOGGER_ALIAS_DISABLED];
                $this->deployConfigWriter->saveConfig(
                    [ConfigFilePool::APP_ENV => [LoggerProxy::CONF_GROUP_NAME => $data]]
                );
            } catch (FileSystemException $e) {
                $this->logger->error($e->getMessage());
            }

            return;
        }

        try {
            $configGroup = [
                LoggerProxy::CONF_GROUP_NAME => [
                    QueryLogEnableCommand::PARAM_TABLE_FILTERS => $this->config->getTableList(),
                    LoggerProxy::PARAM_ALIAS => LoggerProxy::LOGGER_ALIAS_FILE,
                    LoggerProxy::PARAM_LOG_ALL => 1,
                    LoggerProxy::PARAM_QUERY_TIME => number_format($this->config->getQueryTimeThreshold(), 3),
                    LoggerProxy::PARAM_CALL_STACK => (int)($this->config->isLogCallStack() != false)
                ]
            ];

            $this->deployConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $configGroup]);
        } catch (FileSystemException $e) {
            $this->logger->error($e->getMessage());
            return;
        }
    }
}
