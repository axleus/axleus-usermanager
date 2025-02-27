<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\Core\Handler\HandlerTrait;
use Axleus\UserManager\Message\VerificationEmail;
use Axleus\UserManager\Form\Register;
use Axleus\UserManager\User\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\EventManager\EventManagerInterface;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegistrationHandler implements RequestHandlerInterface
{
    use HandlerTrait;

    public function __construct(
        private TemplateRendererInterface $renderer,
        private UserRepositoryInterface&UserRepository $userRepositoryInterface,
        private Register $form,
        private UrlHelper $urlHelper,
        private array $config
    ) {
    }

    public function handleGet(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render(
            'user-manager::registration',
            ['form' => $this->form]
        ));
    }

    public function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $this->form->setData($body);
        if ($this->form->isValid()) {
            $eventManager = $request->getAttribute(EventManagerInterface::class);
            $email = new VerificationEmail(VerificationEmail::EVENT_VERIFY_ACCOUNT_EMAIL);
            // flag this message to send a UI notification on success
            $email->setNotify(true);
            $uri   = $request->getUri();
            $host  = $uri->getScheme() . '://' . $uri->getHost();
            $host  .= $uri->getPort() !== null ? ':' . $uri->getPort() : '';
            // set host for email message link
            $email->setParam('host', $host);
            $userEntity = $this->form->getData();
            $userEntity->offsetUnset('conf_password');
            try {
                $userEntity->hashPassword();
                $result     = $this->userRepositoryInterface->save($userEntity, 'id');
                // set event target
                $email->setTarget($result);
                $messageResult = $eventManager->triggerEvent($email);
            } catch (\Throwable $th) {
                throw $th;
            }
            return new RedirectResponse(
                $this->urlHelper->generate('home')
            );
        }
        return new HtmlResponse($this->renderer->render(
            'user-manager::registration',
            ['form' => $this->form]
        ));
    }
}
