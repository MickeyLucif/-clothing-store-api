<?php

namespace App\Enum\Auth;

enum AuthErrorCode: string
{
    case INVALID_EMAIL_VERIFICATION_LINK = 'invalid_email_verification_link';
    case EMAIL_VERIFICATION_USER_NOT_FOUND = 'email_verification_user_not_found';
    case EMAIL_NOT_VERIFIED = 'email_not_verified';
    case INVALID_CREDENTIALS = 'invalid_credentials';
}
