<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Install\Controller;

use DuRoom\Http\Controller\AbstractHtmlController;
use DuRoom\Install\Installation;
use Illuminate\Contracts\View\Factory;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexController extends AbstractHtmlController
{
    /**
     * @var Factory
     */
    protected $view;

    /**
     * @var Installation
     */
    protected $installation;

    /**
     * @param Factory $view
     * @param Installation $installation
     */
    public function __construct(Factory $view, Installation $installation)
    {
        $this->view = $view;
        $this->installation = $installation;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function render(Request $request)
    {
        $view = $this->view->make('duroom.install::app')->with('title', 'Install DuRoom');

        $problems = $this->installation->prerequisites()->problems();

        if ($problems->isEmpty()) {
            $view->with('content', $this->view->make('duroom.install::install'));
        } else {
            $view->with('content', $this->view->make('duroom.install::problems')->with('problems', $problems));
        }

        return $view;
    }
}
