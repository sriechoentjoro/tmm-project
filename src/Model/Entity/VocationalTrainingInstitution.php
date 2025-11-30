<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;
use Cake\Utility\Security;

/**
 * VocationalTrainingInstitution Entity
 *
 * @property int $id
 * @property bool $is_special_skill_support_institution
 * @property string|null $abbreviation
 * @property string $name
 * @property int $master_propinsi_id
 * @property int $master_kabupaten_id
 * @property int $master_kecamatan_id
 * @property int $master_kelurahan_id
 * @property string $address
 * @property string|null $post_code
 * @property string|null $director
 * @property string|null $director_katakana
 * @property string $email
 * @property string $username
 * @property string|null $password
 * @property string|null $registration_token
 * @property \Cake\I18n\FrozenTime|null $token_expires_at
 * @property bool $is_registered
 * @property \Cake\I18n\FrozenTime|null $registered_at
 * @property string $mou_file
 *
 * @property \App\Model\Entity\MasterPropinsi $master_propinsi
 * @property \App\Model\Entity\MasterKabupaten $master_kabupaten
 * @property \App\Model\Entity\MasterKecamatan $master_kecamatan
 * @property \App\Model\Entity\MasterKelurahan $master_kelurahan
 * @property \App\Model\Entity\VocationalTrainingInstitutionStory[] $vocational_training_institution_stories
 */
class VocationalTrainingInstitution extends Entity
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
        'is_special_skill_support_institution' => true,
        'abbreviation' => true,
        'name' => true,
        'master_propinsi_id' => true,
        'master_kabupaten_id' => true,
        'master_kecamatan_id' => true,
        'master_kelurahan_id' => true,
        'address' => true,
        'post_code' => true,
        'director' => true,
        'director_katakana' => true,
        'email' => true,
        'username' => true,
        'password' => true,
        'is_registered' => true,
        'registered_at' => true,
        'mou_file' => true,
        'master_propinsi' => true,
        'master_kabupaten' => true,
        'master_kecamatan' => true,
        'master_kelurahan' => true,
        'vocational_training_institution_stories' => true,
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
