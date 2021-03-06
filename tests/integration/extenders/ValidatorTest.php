<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\extenders;

use DuRoom\Extend;
use DuRoom\Group\GroupValidator;
use DuRoom\Testing\integration\TestCase;
use DuRoom\User\UserValidator;
use Illuminate\Validation\ValidationException;

class ValidatorTest extends TestCase
{
    private function extendToRequireLongPassword()
    {
        $this->extend((new Extend\Validator(UserValidator::class))->configure(function ($duroomValidator, $validator) {
            $validator->setRules([
                'password' => [
                    'required',
                    'min:20'
                ]
            ] + $validator->getRules());
        }));
    }

    private function extendToRequireLongPasswordViaInvokableClass()
    {
        $this->extend((new Extend\Validator(UserValidator::class))->configure(CustomValidatorClass::class));
    }

    /**
     * @test
     */
    public function custom_validation_rule_does_not_exist_by_default()
    {
        $this->app()->getContainer()->make(UserValidator::class)->assertValid(['password' => 'simplePassword']);

        // If we have gotten this far, no validation exception has been thrown, so the test is succesful.
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function custom_validation_rule_exists_if_added()
    {
        $this->extendToRequireLongPassword();

        $this->expectException(ValidationException::class);

        $this->app()->getContainer()->make(UserValidator::class)->assertValid(['password' => 'simplePassword']);
    }

    /**
     * @test
     */
    public function custom_validation_rule_exists_if_added_via_invokable_class()
    {
        $this->extendToRequireLongPasswordViaInvokableClass();

        $this->expectException(ValidationException::class);

        $this->app()->getContainer()->make(UserValidator::class)->assertValid(['password' => 'simplePassword']);
    }

    /**
     * @test
     */
    public function custom_validation_rule_doesnt_affect_other_validators()
    {
        $this->extendToRequireLongPassword();

        $this->app()->getContainer()->make(GroupValidator::class)->assertValid(['password' => 'simplePassword']);

        // If we have gotten this far, no validation exception has been thrown, so the test is succesful.
        $this->assertTrue(true);
    }
}

class CustomValidatorClass
{
    public function __invoke($duroomValidator, $validator)
    {
        $validator->setRules([
            'password' => [
                'required',
                'min:20'
            ]
        ] + $validator->getRules());
    }
}