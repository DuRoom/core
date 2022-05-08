<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Update\Controller;

use Exception;
use DuRoom\Database\Console\MigrateCommand;
use DuRoom\Foundation\Config;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

class UpdateController implements RequestHandlerInterface
{
    protected $command;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param MigrateCommand $command
     * @param Config $config
     */
    public function __construct(MigrateCommand $command, Config $config)
    {
        $this->command = $command;
        $this->config = $config;
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     */
    public function handle(Request $request): ResponseInterface
    {
        $input = $request->getParsedBody();

        if (Arr::get($input, 'databasePassword') !== $this->config['database.password']) {
            return new HtmlResponse('Incorrect database password.', 500);
        }

        $body = fopen('php://temp', 'wb+');
        $input = new StringInput('');
        $output = new StreamOutput($body);

        try {
            $this->command->run($input, $output);
        } catch (Exception $e) {
            return new HtmlResponse($e->getMessage(), 500);
        }

        return new Response($body, 200);
    }
}
