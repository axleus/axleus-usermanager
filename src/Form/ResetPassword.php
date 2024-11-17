<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use Axleus\Htmx\Form\HtmxTrait;
use Axleus\UserManager\Form\Fieldset\ResetPasswordFieldset;
use Fig\Http\Message\RequestMethodInterface as Http;
use Laminas\Form;

final class ResetPassword extends Form\Form
{
    use HtmxTrait;

    public function __construct(
        $name = 'reset-password',
        $options = []
    ) {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $this->setAttributes([
            'action' => $this->urlHelper->generate('Reset Password'),
            'method' => Http::METHOD_POST,
        ]);

        $this->add([
            'name' => 'acct-data',
            'type' => ResetPasswordFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ])->add([
            'name'       => 'Register',
            'type'       => Form\Element\Submit::class,
            'attributes' => [
                'value' => 'Reset Password',
            ],
        ]);
    }
}
