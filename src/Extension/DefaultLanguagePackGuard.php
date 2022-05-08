<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extension;

use DuRoom\Extension\Event\Disabling;
use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\User\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;

class DefaultLanguagePackGuard
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(Disabling $event)
    {
        if (! in_array('duroom-locale', $event->extension->extra)) {
            return;
        }

        $defaultLocale = $this->settings->get('default_locale');
        $locale = Arr::get($event->extension->extra, 'duroom-locale.code');

        if ($locale === $defaultLocale) {
            throw new PermissionDeniedException('You cannot disable the default language pack!');
        }
    }
}
