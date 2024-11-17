<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use Axleus\Htmx\Form\HtmxTrait;
use Laminas\Form;

final class ResendVerification extends Form\Form
{
    use HtmxTrait;

    public function init(): void
    {
        $this->add([
            'name' => 'acct-data',
            'type' => Fieldset\ResendVerification::class
        ]);

        $this->add([
            'name'       => 'resend-verify',
            'type'       => Form\Element\Submit::class,
            'attributes' => [
                'value' => 'Resend Verification',
            ],
        ]);
    }
}
