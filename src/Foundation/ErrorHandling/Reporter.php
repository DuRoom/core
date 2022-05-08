<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Foundation\ErrorHandling;

use Throwable;

interface Reporter
{
    /**
     * Report an error that DuRoom was not able to handle to a backend.
     *
     * @param Throwable $error
     * @return void
     */
    public function report(Throwable $error);
}
