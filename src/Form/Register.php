<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use Axleus\Htmx\Form\HtmxTrait;
use Axleus\UserManager\Form\Fieldset\AcctDataFieldset;
use Fig\Http\Message\RequestMethodInterface as Http;
use Laminas\Form;

class Register extends Form\Form
{
    use HtmxTrait;

    public function init(): void
    {
        $this->setAttributes([
            'action' => $this->urlHelper->generate('register'),
            'method' => Http::METHOD_POST,
        ]);
        $this->add([
                'name'    => 'acct-data',
                'type'    => AcctDataFieldset::class,
                'options' => [
                    'use_as_base_fieldset' => true,
                ],
        ])->add([
            'name'       => 'Register',
            'type'       => Form\Element\Submit::class,
            'attributes' => [
                'value' => 'Register',
            ],
        ]);
    }
}
