<?php

namespace App\Enum\Auth;

enum AuthErrorCode: string
{
    case INVALID_EMAIL_VERIFICATION_LINK = 'invalid_email_verification_link';
}
