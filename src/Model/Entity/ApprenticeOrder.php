<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeOrder Entity
 *
 * @property int $id
 * @property string|null $reference_file
 * @property int $cooperative_association_id
 * @property int $acceptance_organization_id
 * @property string $title
 * @property int|null $male_trainee_number
 * @property int|null $female_trainee_number
 * @property int $job_category_id
 * @property string $departure_year
 * @property string $departure_month
 * @property bool $is_practical_test_required
 * @property string|null $other_requirements
 *
 * @property \App\Model\Entity\CooperativeAssociation $cooperative_association
 * @property \App\Model\Entity\AcceptanceOrganization $acceptance_organization
 * @property \App\Model\Entity\MasterJobCategory $master_job_category
 * @property \App\Model\Entity\Apprentice[] $apprentices
 */
class ApprenticeOrder extends Entity
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
        'reference_file' => true,
        'cooperative_association_id' => true,
        'acceptance_organization_id' => true,
        'title' => true,
        'male_trainee_number' => true,
        'female_trainee_number' => true,
        'job_category_id' => true,
        'departure_year' => true,
        'departure_month' => true,
        'is_practical_test_required' => true,
        'other_requirements' => true,
        'cooperative_association' => true,
        'acceptance_organization' => true,
        'master_job_category' => true,
        'apprentices' => true,
    ];
}
