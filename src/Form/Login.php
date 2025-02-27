<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use Axleus\Htmx\Form\HtmxTrait;
use Fig\Http\Message\RequestMethodInterface as Http;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Filter;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;
use Laminas\Form;

final class Login extends Form\Form implements AdapterAwareInterface, InputFilterProviderInterface
{
    use AdapterAwareTrait;
    use HtmxTrait;

    public const OPTION_KEY = 'db_table_name';

    protected $attributes = ['class' => 'login-form'];

    /** @inheritDoc */
    public function __construct($name = 'login-form', $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $this->setAttributes([
            'method' => Http::METHOD_POST,
            'action' => $this->urlHelper->generate('login')
        ]);
        $this->add([
            'name' => 'email',
            'type' => Form\Element\Text::class,
            'attributes' => [
                'placeholder' => 'Email',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => Form\Element\Password::class,
            'attributes' => [
                'placeholder' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'Login',
            'type' => Form\Element\Submit::class,
            'attributes' => [
                'value' => 'Login',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'email' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                    [
                        'name'    => Validator\Db\RecordExists::class,
                        'options' => [
                            'table'     => $this->getOption(self::OPTION_KEY),
                            'field'     => 'email',
                            'dbAdapter' => $this->adapter,
                            'messages'  => [
                                Validator\Db\RecordExists::ERROR_NO_RECORD_FOUND => 'Email not found!!',
                            ],
                        ],
                    ],
                ],
            ],
            'password' => [
                'required' => true,
            ],
        ];
    }
}
