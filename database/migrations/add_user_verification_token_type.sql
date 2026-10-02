-- Let a login account carry a verification token of its own kind.
--
-- email_verification_tokens.token_type is an ENUM, and it was created with two
-- values: email_verification (an institution confirming its registration) and
-- password_reset. A login account confirming its own address is neither of
-- those, and it cannot share the institution's value: both kinds are keyed on
-- an email address and nothing else, and for an LPK the account and the
-- institution have the same address, so resending one would spend the other's
-- live token and kill a link somebody was about to click.
--
-- Until this runs, the resend button on /users fails: MySQL refuses the value
-- and the token is never written.
--
-- The change only adds a value. No existing row holds a value that is being
-- removed, so nothing can be truncated and no row changes.

USE cms_authentication_authorization;

ALTER TABLE email_verification_tokens
    MODIFY token_type ENUM('email_verification', 'user_verification', 'password_reset')
    NOT NULL DEFAULT 'email_verification' COMMENT 'Type of token';

-- Check it took.
SELECT COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'cms_authentication_authorization'
  AND TABLE_NAME = 'email_verification_tokens'
  AND COLUMN_NAME = 'token_type';
