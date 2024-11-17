<?php

declare(strict_types=1);

namespace Axleus\UserManager\Message;

use Axleus\Message\SystemMessage;

final class PersonalMessage extends SystemMessage
{
    public const EVENT_PERSONAL_MESSAGE = 'personalMessage';
}
