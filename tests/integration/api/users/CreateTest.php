<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\api\users;

use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\Testing\integration\RetrievesAuthorizedUsers;
use DuRoom\Testing\integration\TestCase;
use DuRoom\User\RegistrationToken;
use DuRoom\User\User;

class CreateTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setting('mail_driver', 'log');
    }

    /**
     * @test
     */
    public function cannot_create_user_without_data()
    {
        $response = $this->send(
            $this->request(
                'POST',
                '/api/users',
                [
                    'json' => ['data' => ['attributes' => []]],
                ]
            )->withAttribute('bypassCsrfToken', true)
        );

        $this->assertEquals(422, $response->getStatusCode());

        // The response body should contain details about the failed validation
        $body = (string) $response->getBody();
        $this->assertJson($body);
        $this->assertEquals([
            'errors' => [
                [
                    'status' => '422',
                    'code' => 'validation_error',
                    'detail' => 'The username field is required.',
                    'source' => ['pointer' => '/data/attributes/username'],
                ],
                [
                    'status' => '422',
                    'code' => 'validation_error',
                    'detail' => 'The email field is required.',
                    'source' => ['pointer' => '/data/attributes/email'],
                ],
                [
                    'status' => '422',
                    'code' => 'validation_error',
                    'detail' => 'The password field is required.',
                    'source' => ['pointer' => '/data/attributes/password'],
                ],
            ],
        ], json_decode($body, true));
    }

    /**
     * @test
     */
    public function can_create_user()
    {
        $response = $this->send(
            $this->request(
                'POST',
                '/api/users',
                [
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'username' => 'test',
                                'password' => 'too-obscure',
                                'email' => 'test@machine.local',
                            ],
                        ]
                    ],
                ]
            )->withAttribute('bypassCsrfToken', true)
        );

        $this->assertEquals(201, $response->getStatusCode());

        /** @var User $user */
        $user = User::where('username', 'test')->firstOrFail();

        $this->assertEquals(0, $user->is_email_confirmed);
        $this->assertEquals('test', $user->username);
        $this->assertEquals('test@machine.local', $user->email);
    }

    /**
     * @test
     */
    public function admins_can_create_activated_users()
    {
        $response = $this->send(
            $this->request(
                'POST',
                '/api/users',
                [
                    'authenticatedAs' => 1,
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'username' => 'test',
                                'password' => 'too-obscure',
                                'email' => 'test@machine.local',
                                'isEmailConfirmed' => 1,
                            ],
                        ]
                    ],
                ]
            )
        );

        $this->assertEquals(201, $response->getStatusCode());

        /** @var User $user */
        $user = User::where('username', 'test')->firstOrFail();

        $this->assertEquals(1, $user->is_email_confirmed);
    }

    /**
     * @test
     */
    public function disabling_sign_up_prevents_user_creation()
    {
        /** @var SettingsRepositoryInterface $settings */
        $settings = $this->app()->getContainer()->make(SettingsRepositoryInterface::class);
        $settings->set('allow_sign_up', false);

        $response = $this->send(
            $this->request(
                'POST',
                '/api/users',
                [
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'username' => 'test',
                                'password' => 'too-obscure',
                                'email' => 'test@machine.local',
                            ],
                        ]
                    ],
                ]
            )->withAttribute('bypassCsrfToken', true)
        );
        $this->assertEquals(403, $response->getStatusCode());

        $settings->set('allow_sign_up', true);
    }

    /**
     * @test
     */
    public function cannot_create_user_with_invalid_avatar_uri_scheme()
    {
        // Boot app
        $this->app();

        $regTokens = [];

        // Add registration tokens that should cause a failure
        $regTokens[] = [
            'token' => RegistrationToken::generate('duroom', '1', [
                'username' => 'test',
                'email' => 'test@machine.local',
                'is_email_confirmed' => 1,
                'avatar_url' =>  'file://localhost/etc/passwd'
            ], []),
            'scheme' => 'file'
        ];

        $regTokens[] = [
            'token' => RegistrationToken::generate('duroom', '1', [
                'username' => 'test',
                'email' => 'test@machine.local',
                'is_email_confirmed' => 1,
                'avatar_url' => 'ftp://localhost/image.png'
            ], []),
            'scheme' => 'ftp'
        ];

        // Test each reg token
        foreach ($regTokens as $regToken) {
            $regToken['token']->saveOrFail();

            // Call the registration endpoint
            $response = $this->send(
                $this->request(
                    'POST',
                    '/api/users',
                    [
                        'json' => [
                            'data' => [
                                'attributes' => [
                                    'token' => $regToken['token']->token,
                                ],
                            ]
                        ],
                    ]
                )->withAttribute('bypassCsrfToken', true)
            );

            // The response body should contain details about the invalid URI
            $body = (string) $response->getBody();
            $this->assertJson($body);
            $decodedBody = json_decode($body, true);

            $this->assertEquals(500, $response->getStatusCode());

            $firstError = $decodedBody['errors'][0];

            // Check that the error is an invalid URI
            $this->assertStringStartsWith('InvalidArgumentException: Provided avatar URL must have scheme http or https. Scheme provided was '.$regToken['scheme'].'.', $firstError['detail']);
        }
    }

    /**
     * @test
     */
    public function cannot_create_user_with_invalid_avatar_uri()
    {
        // Boot app
        $this->app();

        $regTokens = [];

        // Add registration tokens that should cause a failure
        $regTokens[] = RegistrationToken::generate('duroom', '1', [
            'username' => 'test',
            'email' => 'test@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'https://127.0.0.1/image.png'
        ], []);

        $regTokens[] = RegistrationToken::generate('duroom', '1', [
            'username' => 'test',
            'email' => 'test@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'https://192.168.0.1/image.png'
        ], []);

        $regTokens[] = RegistrationToken::generate('duroom', '1', [
            'username' => 'test',
            'email' => 'test@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  '../image.png'
        ], []);

        $regTokens[] = RegistrationToken::generate('duroom', '1', [
            'username' => 'test',
            'email' => 'test@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'image.png'
        ], []);

        // Test each reg token
        foreach ($regTokens as $regToken) {
            $regToken->saveOrFail();

            // Call the registration endpoint
            $response = $this->send(
                $this->request(
                    'POST',
                    '/api/users',
                    [
                        'json' => [
                            'data' => [
                                'attributes' => [
                                    'token' => $regToken->token,
                                ],
                            ]
                        ],
                    ]
                )->withAttribute('bypassCsrfToken', true)
            );

            // The response body should contain details about the invalid URI
            $body = (string) $response->getBody();
            $this->assertJson($body);
            $decodedBody = json_decode($body, true);

            $this->assertEquals(500, $response->getStatusCode());

            $firstError = $decodedBody['errors'][0];

            // Check that the error is an invalid URI
            $this->assertStringStartsWith('InvalidArgumentException: Provided avatar URL must be a valid URI.', $firstError['detail']);
        }
    }

    /**
     * @test
     */
    public function can_create_user_with_valid_avatar_uri()
    {
        // Boot app
        $this->app();

        $regTokens = [];

        // Add registration tokens that should work fine
        $regTokens[] = RegistrationToken::generate('duroom', '1', [
            'username' => 'test1',
            'email' => 'test1@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'https://via.placeholder.com/150.png'
        ], []);

        $regTokens[] = RegistrationToken::generate('duroom', '2', [
            'username' => 'test2',
            'email' => 'test2@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'https://via.placeholder.com/150.jpg'
        ], []);

        $regTokens[] = RegistrationToken::generate('duroom', '3', [
            'username' => 'test3',
            'email' => 'test3@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'https://via.placeholder.com/150.gif'
        ], []);

        $regTokens[] = RegistrationToken::generate('duroom', '4', [
            'username' => 'test4',
            'email' => 'test4@machine.local',
            'is_email_confirmed' => 1,
            'avatar_url' =>  'http://via.placeholder.com/150.png'
        ], []);

        /**
         * Test each reg token.
         *
         * @var RegistrationToken $regToken
         */
        foreach ($regTokens as $regToken) {
            $regToken->saveOrFail();

            // Call the registration endpoint
            $response = $this->send(
                $this->request(
                    'POST',
                    '/api/users',
                    [
                        'json' => [
                            'data' => [
                                'attributes' => [
                                    'token' => $regToken->token,
                                ],
                            ]
                        ],
                    ]
                )->withAttribute('bypassCsrfToken', true)
            );

            $this->assertEquals(201, $response->getStatusCode());

            $user = User::where('username', $regToken->user_attributes['username'])->firstOrFail();

            $this->assertEquals($regToken->user_attributes['is_email_confirmed'], $user->is_email_confirmed);
            $this->assertEquals($regToken->user_attributes['username'], $user->username);
            $this->assertEquals($regToken->user_attributes['email'], $user->email);
        }
    }
}