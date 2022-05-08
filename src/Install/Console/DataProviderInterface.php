<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Install\Console;

use DuRoom\Install\Installation;

interface DataProviderInterface
{
    public function configure(Installation $installation): Installation;
}
