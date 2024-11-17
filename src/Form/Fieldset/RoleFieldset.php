<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form\Fieldset;

use Axleus\UserManager\Form\Element\RoleSelect;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

// todo: replace Role usage with config
final class RoleFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var Roles $roleModel */
    protected $roleModel;
    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(Roles $roleModel)
    {
        $this->roleModel = $roleModel;
        parent::__construct('role-data');
    }

    public function init(): void
    {
        $this->add([
            'type'    => RoleSelect::class,
            'options' => [
                'label' => 'Assign Group?',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
