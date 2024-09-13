<?php

declare(strict_types=1);

namespace ProDevTools\QueryLogger\Plugin\DB\Logger;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\DB\Logger\File;
use Magento\Framework\DB\Logger\LoggerProxy;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use ProDevTools\QueryLogger\Console\Command\QueryLogEnableCommand;

class FilterTableQuery
{
    /**
     * @var array List of tables to be filtered from logging.
     */
    private array $configFilterTables = [];

    /**
     * Constructor
     *
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        private readonly DeploymentConfig $deploymentConfig
    ) {
    }

    /**
     *  Around plugin for the getStats method.
     *
     *  This method filters the query logging based on specified table names.
     *
     * @param File $subject
     * @param callable $proceed
     * @param string $type
     * @param string $sql
     * @param array $bind
     * @param mixed|null $result
     * @return string
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function aroundGetStats(
        File     $subject,
        callable $proceed,
        string   $type,
        string   $sql,
        array    $bind = [],
        mixed $result = null
    ): string {
        // Get the list of filtered tables from the configuration
        $filterTables = $this->getFilterTables();

        // If no filter is set, proceed with normal logging
        if (empty($filterTables)) {
            return $proceed($type, $sql, $bind, $result);
        }

        // Extract the table name from the query
        $tableName = $this->getTableNameFromQuery($sql);

        // Log the query if the table is in the filter list
        if ($tableName && in_array($tableName, $filterTables, true)) {
            return $proceed($type, $sql, $bind, $result);
        }

        return '';
    }

    /**
     * Retrieves the list of tables to be filtered from logging.
     *
     * @return array List of filtered table names.
     * @throws FileSystemException
     * @throws RuntimeException
     */
    private function getFilterTables(): array
    {
        // If the filter tables have not been loaded yet, load them from configuration
        if (empty($this->configFilterTables)) {
            $configValue = $this->deploymentConfig->get(
                LoggerProxy::CONF_GROUP_NAME . '/' . QueryLogEnableCommand::PARAM_TABLE_FILTERS
            );

            // If the config value is a string, split it into an array
            if ($configValue) {
                $this->configFilterTables = explode(',', $configValue);
            }
        }

        return $this->configFilterTables;
    }

    /**
     * Extracts the table name from a SQL query.
     *
     * @param string $query The SQL query string.
     * @return string|null The extracted table name, or null if no match is found.
     */
    private function getTableNameFromQuery(string $query): ?string
    {
        // Patterns to match the table name in different types of SQL queries
        $patterns = [
            '/\bFROM\s+`?(\w+)`?/i',        // SELECT, DELETE
            '/\bUPDATE\s+`?(\w+)`?/i',      // UPDATE
            '/\bINTO\s+`?(\w+)`?/i',        // INSERT
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $query, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
