<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\MVC\Symfony\FieldType\User;

use DateTime;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getViewParameters(Field $field): array
    {
        $user = $this->userService->loadUser($field->value->contentId);

        $isPasswordExpired = false;
        $passwordExpiresIn = null;
        $passwordExpiresAt = $this->userService->getPasswordExpirationDate($user);
        if ($passwordExpiresAt !== null) {
            $passwordExpiresIn = (new DateTime())->diff($passwordExpiresAt);
            $isPasswordExpired = $this->userService->isPasswordExpired($user);
        }

        return [
            'is_password_expired' => $isPasswordExpired,
            'password_expires_at' => $passwordExpiresAt,
            'password_expires_in' => $passwordExpiresIn,
        ];
    }
}
