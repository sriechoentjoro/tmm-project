<?php
namespace App\Controller;

/**
 * The one statement of what counts as an acceptable password.
 *
 * The rules were written out inline in LpkRegistration::setPassword, as six
 * elseif branches each with its own Flash message. Adding an admin reset screen
 * meant either repeating all six or sharing them, and this application has
 * already paid for the first choice: nine copies of one AJAX endpoint, five
 * copies of a condition that turned out not to work, seventeen edit templates
 * carrying the same mistake. A rule that is written twice is a rule that will
 * disagree with itself.
 *
 * So both screens ask here. The rules themselves are unchanged - whatever an LPK
 * had to type to set its own password is what an administrator has to type to
 * reset it, which is also the only honest answer to "what are the rules?".
 */
trait PasswordPolicyTrait
{
    /**
     * What is wrong with this password, or null when nothing is.
     *
     * The first problem is returned rather than all of them, because that is
     * what the existing screen said and because a list of six rules is read as
     * a wall rather than as an instruction. The form states the rules up front;
     * this names the one that was missed.
     *
     * @param string|null $password What was typed.
     * @param string|null $confirmation What was typed again, or null when the
     *  form has no confirmation field.
     * @return string|null A message for the person, already translated.
     */
    protected function passwordProblem($password, $confirmation = null)
    {
        $password = (string)$password;

        if ($password === '') {
            return __('Please enter a new password.');
        }
        if ($confirmation !== null && $password !== (string)$confirmation) {
            return __('Passwords do not match. Please try again.');
        }
        if (strlen($password) < 8) {
            return __('Password must be at least 8 characters long.');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return __('Password must contain at least one uppercase letter.');
        }
        if (!preg_match('/[a-z]/', $password)) {
            return __('Password must contain at least one lowercase letter.');
        }
        if (!preg_match('/[0-9]/', $password)) {
            return __('Password must contain at least one number.');
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            return __('Password must contain at least one special character (!@#$%^&* etc).');
        }

        return null;
    }

    /**
     * The rules, as a list a form can print.
     *
     * Printed from the same place they are enforced, so a screen cannot promise
     * one thing and the check refuse another.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return [
            __('At least 8 characters'),
            __('At least one uppercase letter'),
            __('At least one lowercase letter'),
            __('At least one number'),
            __('At least one special character (!@#$%^&* etc)'),
        ];
    }
}
