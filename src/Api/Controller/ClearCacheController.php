<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Foundation\Console\CacheClearCommand;
use DuRoom\Http\RequestUtil;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ClearCacheController extends AbstractDeleteController
{
    /**
     * @var CacheClearCommand
     */
    protected $command;

    /**
     * @param CacheClearCommand $command
     */
    public function __construct(CacheClearCommand $command)
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $this->command->run(
            new ArrayInput([]),
            new NullOutput()
        );

        return new EmptyResponse(204);
    }
}
