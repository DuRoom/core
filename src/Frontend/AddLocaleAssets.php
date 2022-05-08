<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Frontend;

use DuRoom\Frontend\Compiler\Source\SourceCollector;
use DuRoom\Locale\LocaleManager;

/**
 * @internal
 */
class AddLocaleAssets
{
    /**
     * @var LocaleManager
     */
    protected $locales;

    /**
     * @param LocaleManager $locales
     */
    public function __construct(LocaleManager $locales)
    {
        $this->locales = $locales;
    }

    public function to(Assets $assets)
    {
        $assets->localeJs(function (SourceCollector $sources, string $locale) {
            foreach ($this->locales->getJsFiles($locale) as $file) {
                $sources->addFile($file);
            }
        });

        $assets->localeCss(function (SourceCollector $sources, string $locale) {
            foreach ($this->locales->getCssFiles($locale) as $file) {
                $sources->addFile($file);
            }
        });
    }
}
