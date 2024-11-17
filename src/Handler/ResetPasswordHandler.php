<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\Core\Handler\HandlerTrait;
use Axleus\UserManager\Form\ResetPassword;
use Axleus\UserManager\Helper\VerificationHelper;
use Axleus\Htmx\HtmxHandlerTrait;
use Axleus\UserManager\Message\PasswordResetEmail;
use Axleus\UserManager\User\UserEntity;
use Axleus\UserManager\User\UserRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Exception\DomainException;
use Laminas\View\Model\ModelInterface;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Flash\Exception\InvalidHopsValueException;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ResetPasswordHandler implements RequestHandlerInterface
{
    use HandlerTrait;
    use HtmxHandlerTrait;

    public final const MESSAGE_KEY = 'reset_notice_message';

    public function __construct(
        private TemplateRendererInterface $renderer,
        private UserRepositoryInterface&UserRepository $userRepositoryInterface,
        private UrlHelper $url,
        private VerificationHelper $verifyHelper,
        private ResetPassword $form,
        private array $config
    ) {
    }

    public function handleGet(ServerRequestInterface $request): ResponseInterface
    {
        $model = $request->getAttribute(ModelInterface::class);
        $model->setVariable('form', $this->form);
        return new HtmlResponse(
            $this->renderer->render(
                'user-manager::reset-password',
                $model
            )
        );
    }

    /**
     * todo: switch out email message config for reset message templates
     * todo: code Email Message abstraction
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws Throwable
     * @throws InvalidHopsValueException
     */
    public function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $this->form->setData($body);
        if ($this->form->isValid()) {
            $userEntity   = $this->form->getData();
            $eventManager = $request->getAttribute(EventManagerInterface::class);
            $email        = new PasswordResetEmail(PasswordResetEmail::EVENT_PASSWORD_RESET_EMAIL);
            $email->setNotify(true);
            $uri   = $request->getUri();
            $host  = $uri->getScheme() . '://' . $uri->getHost();
            $host .= $uri->getPort() !== null ? ':' . $uri->getPort() : '';
            $email->setParam('host', $host);
            try {
                /** @var UserEntity */
                $result = $this->userRepositoryInterface->findOneBy('email', $userEntity->email);
                $result->setPasswordResetToken($userEntity->getPasswordResetToken());
                $result = $this->userRepositoryInterface->save($result, 'id');
                $email->setTarget($result);
                $messageResult = $eventManager->triggerEvent($email);
            } catch (\Throwable $th) {
                throw $th;
            }
            return new RedirectResponse(
                $this->url->generate('Home')
            );
        }

        $model = $request->getAttribute(ModelInterface::class);
        return new HtmlResponse(
            html: $this->renderer->render(
                'user-manager::reset-password',
                $model
            )
        );
    }
}
