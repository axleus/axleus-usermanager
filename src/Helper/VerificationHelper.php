<?php

declare(strict_types=1);

namespace Axleus\UserManager\Helper;

use Axleus\Core\ConfigProvider as CoreProvider;
use Axleus\UserManager\ConfigProvider;
use Axleus\UserManager\User\UserRepository;
use Axleus\UserManager\User\UserEntity;
use Axleus\UserManager\Validator\UuidV7TokenValidator as TokenValidator;
use DateTimeImmutable;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

final class VerificationHelper
{
    final public const VERIFICATION_TOKEN   = 'verificationToken';
    final public const PASSWORD_RESET_TOKEN = 'passwordResetToken';
    final public const DEFAULT_LIFETIME     = '1 Hour';

    private UserEntity $target;

    public function __construct(
        private UserRepositoryInterface&UserRepository $userRepositoryInterface,
        private array $config
    ) {
    }

    public function verifyToken(
        ServerRequestInterface $request,
        ?string $type = self::VERIFICATION_TOKEN,
        ?string $tokenLifetime = null,
        ?bool   $returnTarget = false
    ): UserEntity|bool {
        $routeResult   = $request->getAttribute(RouteResult::class);
        $matchedParams = $routeResult->getMatchedParams();
        $data = [];
        try {

            if (empty($this->target)) {
                $this->target = $this->userRepositoryInterface->findOneBy('id', $matchedParams['id']);
            }

            if ($matchedParams['token'] === $this->target->offsetGet($type)) {
                $tokenValidator = new TokenValidator([
                        'max_lifetime' => $tokenLifetime ?? $this->config[ConfigProvider::TOKEN_KEY][$type],
                    ]);
                if ($tokenValidator->isValid($this->target->offsetGet($type))) {
                    $data = $this->target->getArrayCopy();
                    $now                 = new DateTimeImmutable();
                    $data['dateUpdated'] = $now->format($this->config[CoreProvider::class][CoreProvider::DATETIME_FORMAT]);
                    if ($type === self::VERIFICATION_TOKEN) {
                        $data['dateVerified'] = $data['dateUpdated'];
                        $data['verified']     = 1;
                    }
                    $data[$type] = null;
                    $this->target->exchangeArray($data);
                    $this->target = $this->userRepositoryInterface->save($this->target, 'id');
                    if ($returnTarget) {
                        return $this->target;
                    }
                    return true;
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return false;
    }

    public function setTarget(UserEntity $target): void
    {
        $this->target = $target;
    }

    public function getTarget(): UserEntity
    {
        return $this->target;
    }

}
