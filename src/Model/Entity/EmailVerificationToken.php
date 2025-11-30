<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EmailVerificationToken Entity
 *
 * @property int $id
 * @property string $user_email
 * @property string $token
 * @property string $token_type
 * @property bool $is_used
 * @property \Cake\I18n\Time|null $used_at
 * @property \Cake\I18n\Time $expires_at
 * @property \Cake\I18n\Time $created
 */
class EmailVerificationToken extends Entity
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
        'user_email' => true,
        'token' => true,
        'token_type' => true,
        'is_used' => true,
        'used_at' => true,
        'expires_at' => true,
        'created' => true,
    ];

    /**
     * Fields that are hidden from JSON/Array conversion
     *
     * @var array
     */
    protected $_hidden = [
        'token', // Never expose token in API responses
    ];

    /**
     * Check if token is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return true;
        }
        
        $now = new \Cake\I18n\Time();
        $expiresAt = new \Cake\I18n\Time($this->expires_at);
        
        return $now->greaterThan($expiresAt);
    }

    /**
     * Check if token is valid (not used and not expired)
     *
     * @return bool
     */
    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Get time remaining until expiration
     *
     * @return \DateInterval|false
     */
    public function getTimeRemaining()
    {
        if ($this->isExpired()) {
            return false;
        }
        
        $now = new \Cake\I18n\Time();
        $expiresAt = new \Cake\I18n\Time($this->expires_at);
        
        return $now->diff($expiresAt);
    }

    /**
     * Get human-readable expiration status
     *
     * @return string
     */
    public function getExpirationStatus()
    {
        if ($this->is_used) {
            return 'Used';
        }
        
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        $remaining = $this->getTimeRemaining();
        if ($remaining) {
            $hours = $remaining->h;
            $minutes = $remaining->i;
            return "Expires in {$hours}h {$minutes}m";
        }
        
        return 'Active';
    }
}
