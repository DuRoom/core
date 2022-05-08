<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Foundation\Console;

use DuRoom\Console\AbstractCommand;
use DuRoom\Extension\ExtensionManager;
use DuRoom\Foundation\Paths;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;

class AssetsPublishCommand extends AbstractCommand
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Paths
     */
    protected $paths;

    /**
     * @param Container $container
     * @param Paths $paths
     */
    public function __construct(Container $container, Paths $paths)
    {
        $this->container = $container;
        $this->paths = $paths;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('assets:publish')
            ->setDescription('Publish core and extension assets.');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $this->info('Publishing core assets...');

        $target = $this->container->make('filesystem')->disk('duroom-assets');
        $local = new Filesystem();

        $pathPrefix = $this->paths->vendor.'/components/font-awesome/webfonts';
        $assetFiles = $local->allFiles($pathPrefix);

        foreach ($assetFiles as $fullPath) {
            $relPath = substr($fullPath, strlen($pathPrefix));
            $target->put("fonts/$relPath", $local->get($fullPath));
        }

        $this->info('Publishing extension assets...');

        $extensions = $this->container->make(ExtensionManager::class);
        $extensions->getMigrator()->setOutput($this->output);

        foreach ($extensions->getEnabledExtensions() as $name => $extension) {
            if ($extension->hasAssets()) {
                $this->info('Publishing for extension: '.$name);
                $extension->copyAssetsTo($target);
            }
        }
    }
}
