<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Serializer;

use DuRoom\Mail\DriverInterface;
use InvalidArgumentException;

class MailSettingsSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'mail-settings';

    /**
     * {@inheritdoc}
     *
     * @param array $settings
     * @throws InvalidArgumentException
     */
    protected function getDefaultAttributes($settings)
    {
        return [
            'fields' => array_map([$this, 'serializeDriver'], $settings['drivers']),
            'sending' => $settings['sending'],
            'errors' => $settings['errors'],
        ];
    }

    private function serializeDriver(DriverInterface $driver)
    {
        return $driver->availableSettings();
    }

    public function getId($model)
    {
        return 'global';
    }
}
