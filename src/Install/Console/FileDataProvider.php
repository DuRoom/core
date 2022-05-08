<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Install\Console;

use Exception;
use DuRoom\Install\AdminUser;
use DuRoom\Install\BaseUrl;
use DuRoom\Install\DatabaseConfig;
use DuRoom\Install\Installation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

class FileDataProvider implements DataProviderInterface
{
    protected $debug = false;
    protected $baseUrl = null;
    protected $databaseConfiguration = [];
    protected $adminUser = [];
    protected $settings = [];

    public function __construct(InputInterface $input)
    {
        // Get configuration file path
        $configurationFile = $input->getOption('file');

        // Check if file exists before parsing content
        if (file_exists($configurationFile)) {
            $configurationFileContents = file_get_contents($configurationFile);
            // Try parsing JSON
            if (($json = json_decode($configurationFileContents, true)) !== null) {
                //Use JSON if Valid
                $configuration = $json;
            } else {
                //Else use YAML
                $configuration = Yaml::parse($configurationFileContents);
            }

            // Define configuration variables
            $this->debug = $configuration['debug'] ?? false;
            $this->baseUrl = $configuration['baseUrl'] ?? 'http://duroom.localhost';
            $this->databaseConfiguration = $configuration['databaseConfiguration'] ?? [];
            $this->adminUser = $configuration['adminUser'] ?? [];
            $this->settings = $configuration['settings'] ?? [];
        } else {
            throw new Exception('Configuration file does not exist.');
        }
    }

    public function configure(Installation $installation): Installation
    {
        return $installation
            ->debugMode($this->debug)
            ->baseUrl(BaseUrl::fromString($this->baseUrl))
            ->databaseConfig($this->getDatabaseConfiguration())
            ->adminUser($this->getAdminUser())
            ->settings($this->settings);
    }

    private function getDatabaseConfiguration(): DatabaseConfig
    {
        return new DatabaseConfig(
            $this->databaseConfiguration['driver'] ?? 'mysql',
            $this->databaseConfiguration['host'] ?? 'localhost',
            $this->databaseConfiguration['port'] ?? 3306,
            $this->databaseConfiguration['database'] ?? 'duroom',
            $this->databaseConfiguration['username'] ?? 'root',
            $this->databaseConfiguration['password'] ?? '',
            $this->databaseConfiguration['prefix'] ?? ''
        );
    }

    private function getAdminUser(): AdminUser
    {
        return new AdminUser(
            $this->adminUser['username'] ?? 'admin',
            $this->adminUser['password'] ?? 'password',
            $this->adminUser['email'] ?? 'admin@example.com'
        );
    }
}
