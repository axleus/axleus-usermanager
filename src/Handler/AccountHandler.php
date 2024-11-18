<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\Core\Handler\HandlerTrait;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AccountHandler implements RequestHandlerInterface
{
    use HandlerTrait;

    public function __construct(
        private TemplateRendererInterface $renderer
    ) {
    }

    public function handleGet(ServerRequestInterface $request): ResponseInterface
    {
        $data = [
            'title' => 'Account',
        ];

        return new HtmlResponse($this->renderer->render(
            'user-manager::account',
            $data
        ));
    }
}
