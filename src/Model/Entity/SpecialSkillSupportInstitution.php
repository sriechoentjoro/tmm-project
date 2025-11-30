<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;
use Cake\Utility\Security;

/**
 * SpecialSkillSupportInstitution Entity
 *
 * @property int $id
 * @property string|null $company_name
 * @property string|null $address
 * @property string|null $contact_person
 * @property string $email
 * @property string $username
 * @property string|null $password
 * @property string|null $registration_token
 * @property \Cake\I18n\FrozenTime|null $token_expires_at
 * @property bool $is_registered
 * @property \Cake\I18n\FrozenTime|null $registered_at
 */
class SpecialSkillSupportInstitution extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'company_name' => true,
        'address' => true,
        'contact_person' => true,
        'email' => true,
        'username' => true,
        'password' => true,
        'is_registered' => true,
        'registered_at' => true,
        'registration_token' => false, // Only set programmatically
        'token_expires_at' => false, // Only set programmatically
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
        'registration_token',
    ];

    /**
     * Password setter - automatically hash passwords
     *
     * @param string $password Password to hash
     * @return string|null
     */
    protected function _setPassword($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
        return null;
    }

    /**
     * Generate a unique registration token
     *
     * @param int $expiryHours Hours until token expires (default 48)
     * @return void
     */
    public function generateRegistrationToken($expiryHours = 48)
    {
        $this->registration_token = Security::randomString(64);
        $this->token_expires_at = new \DateTime("+{$expiryHours} hours");
        $this->is_registered = false;
    }

    /**
     * Check if registration token is valid
     *
     * @return bool
     */
    public function isTokenValid()
    {
        if (empty($this->registration_token) || empty($this->token_expires_at)) {
            return false;
        }

        $now = new \DateTime();
        return $now < $this->token_expires_at;
    }

    /**
     * Mark registration as complete
     *
     * @return void
     */
    public function completeRegistration()
    {
        $this->is_registered = true;
        $this->registered_at = new \DateTime();
        $this->registration_token = null;
        $this->token_expires_at = null;
    }

    /**
     * Check if institution has completed registration
     *
     * @return bool
     */
    public function isRegistered()
    {
        return (bool)$this->is_registered;
    }
}
