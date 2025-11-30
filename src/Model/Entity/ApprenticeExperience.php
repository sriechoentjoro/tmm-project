<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeExperience Entity
 *
 * @property int $id
 * @property int $apprentice_id
 * @property string|null $employment_start_year
 * @property string|null $employment_end_year
 * @property string|null $company_name
 * @property string $title
 * @property int|null $master_employee_status_id
 * @property string|null $employment_awards
 * @property int|null $last_salary_amount
 * @property string|null $job_detail
 * @property string|null $termination_reasons
 *
 * @property \App\Model\Entity\Apprentice $apprentice
 * @property \App\Model\Entity\MasterEmployeeStatus $master_employee_status
 */
class ApprenticeExperience extends Entity
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
        'apprentice_id' => true,
        'employment_start_year' => true,
        'employment_end_year' => true,
        'company_name' => true,
        'title' => true,
        'master_employee_status_id' => true,
        'employment_awards' => true,
        'last_salary_amount' => true,
        'job_detail' => true,
        'termination_reasons' => true,
        'apprentice' => true,
        'master_employee_status' => true,
    ];
}
