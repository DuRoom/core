<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Install\Steps;

use DuRoom\Install\BaseUrl;
use DuRoom\Install\DatabaseConfig;
use DuRoom\Install\ReversibleStep;
use DuRoom\Install\Step;

class StoreConfig implements Step, ReversibleStep
{
    private $debugMode;

    private $dbConfig;

    private $baseUrl;

    private $configFile;

    public function __construct($debugMode, DatabaseConfig $dbConfig, BaseUrl $baseUrl, $configFile)
    {
        $this->debugMode = $debugMode;
        $this->dbConfig = $dbConfig;
        $this->baseUrl = $baseUrl;

        $this->configFile = $configFile;
    }

    public function getMessage()
    {
        return 'Writing config file';
    }

    public function run()
    {
        file_put_contents(
            $this->configFile,
            '<?php return '.var_export($this->buildConfig(), true).';'
        );
    }

    public function revert()
    {
        @unlink($this->configFile);
    }

    private function buildConfig()
    {
        return [
            'debug'    => $this->debugMode,
            'database' => $this->dbConfig->toArray(),
            'url'      => (string) $this->baseUrl,
            'paths'    => $this->getPathsConfig(),
            'headers'  => [
                'poweredByHeader'  => true,
                'referrerPolicy' => 'same-origin',
            ]
        ];
    }

    private function getPathsConfig()
    {
        return [
            'api'   => 'api',
            'admin' => 'admin',
        ];
    }
}
