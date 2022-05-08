<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User;

use DuRoom\Http\UrlGenerator;
use DuRoom\Mail\Job\SendRawEmailJob;
use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\User\Event\EmailChangeRequested;
use Illuminate\Contracts\Queue\Queue;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailConfirmationMailer
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(SettingsRepositoryInterface $settings, Queue $queue, UrlGenerator $url, TranslatorInterface $translator)
    {
        $this->settings = $settings;
        $this->queue = $queue;
        $this->url = $url;
        $this->translator = $translator;
    }

    public function handle(EmailChangeRequested $event)
    {
        $email = $event->email;
        $data = $this->getEmailData($event->user, $email);

        $body = $this->translator->trans('core.email.confirm_email.body', $data);
        $subject = $this->translator->trans('core.email.confirm_email.subject');

        $this->queue->push(new SendRawEmailJob($email, $subject, $body));
    }

    /**
     * @param User $user
     * @param string $email
     * @return EmailToken
     */
    protected function generateToken(User $user, $email)
    {
        $token = EmailToken::generate($email, $user->id);
        $token->save();

        return $token;
    }

    /**
     * Get the data that should be made available to email templates.
     *
     * @param User $user
     * @param string $email
     * @return array
     */
    protected function getEmailData(User $user, $email)
    {
        $token = $this->generateToken($user, $email);

        return [
            'username' => $user->display_name,
            'url' => $this->url->to('forum')->route('confirmEmail', ['token' => $token->token]),
            'forum' => $this->settings->get('forum_title')
        ];
    }
}
