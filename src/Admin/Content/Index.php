<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Admin\Content;

use DuRoom\Extension\ExtensionManager;
use DuRoom\Foundation\Application;
use DuRoom\Frontend\Document;
use DuRoom\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Psr\Http\Message\ServerRequestInterface as Request;

class Index
{
    /**
     * @var Factory
     */
    protected $view;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(Factory $view, ExtensionManager $extensions, SettingsRepositoryInterface $settings)
    {
        $this->view = $view;
        $this->extensions = $extensions;
        $this->settings = $settings;
    }

    public function __invoke(Document $document, Request $request): Document
    {
        $extensions = $this->extensions->getExtensions();
        $extensionsEnabled = json_decode($this->settings->get('extensions_enabled', '{}'), true);
        $csrfToken = $request->getAttribute('session')->token();

        $mysqlVersion = $document->payload['mysqlVersion'];
        $phpVersion = $document->payload['phpVersion'];
        $duroomVersion = Application::VERSION;

        $document->content = $this->view->make(
            'duroom.admin::frontend.content.admin',
            compact('extensions', 'extensionsEnabled', 'csrfToken', 'duroomVersion', 'phpVersion', 'mysqlVersion')
        );

        return $document;
    }
}
