<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extension\Exception;

use DuRoom\Extension\ExtensionManager;
use DuRoom\Foundation\ErrorHandling\HandledError;

class DependentExtensionsExceptionHandler
{
    public function handle(DependentExtensionsException $e): HandledError
    {
        return (new HandledError(
            $e,
            'dependent_extensions',
            409
        ))->withDetails($this->errorDetails($e));
    }

    protected function errorDetails(DependentExtensionsException $e): array
    {
        return [
            [
                'extension' => $e->extension->getTitle(),
                'extensions' => ExtensionManager::pluckTitles($e->dependent_extensions),
            ]
        ];
    }
}
