<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Forum\Controller;

use DuRoom\Http\Controller\AbstractHtmlController;
use DuRoom\User\EmailToken;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConfirmEmailViewController extends AbstractHtmlController
{
    /**
     * @var Factory
     */
    protected $view;

    /**
     * @param Factory $view
     */
    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function render(Request $request)
    {
        $token = Arr::get($request->getQueryParams(), 'token');

        $token = EmailToken::validOrFail($token);

        return $this->view->make('duroom.forum::confirm-email')
            ->with('csrfToken', $request->getAttribute('session')->token());
    }
}
