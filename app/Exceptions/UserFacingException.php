<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Business-rule failure that should be shown to the user (modal/flash),
 * not as a Laravel exception page.
 */
class UserFacingException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?string $redirectUrl = null,
        int $code = 422,
    ) {
        parent::__construct($message, $code);
    }
}
