<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use Axleus\Htmx\Form\HtmxTrait;
use Axleus\UserManager\Form\Fieldset;
use Fig\Http\Message\RequestMethodInterface as Http;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

final class ChangePassword extends Form
{
    use HtmxTrait;

    public function __construct($name = 'change-password', $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $options = $this->getOptions();
        $this->setAttributes([
            'action' => $this->urlHelper->generate(
                routeName: 'Change Password',
                //options: ['reuse_result_params' => false]
            ),
            'method' => Http::METHOD_POST,
        ]);
        $this->add([
            'name' => 'acct-data',
            'type' => Fieldset\ChangePasswordFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
                'password_options'     => $options['password_options']
            ]
        ]);
        $this->add([
            'name' => 'Submit',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Submit',
            ]
        ]);
    }
}
