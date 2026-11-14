<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * What the registration wizard collects, and where each answer belongs.
 *
 * The wizard's forms were written against names of their own -
 * educations[i][institution_name], families[i][full_name],
 * certifications[i][certification_name] - and saveEducations() and its
 * siblings handed those rows straight to newEntity(). None of those names is
 * a column: candidate_educations holds college_name and college_major,
 * candidate_families holds name, candidate_certifications holds title. An
 * entity drops what is not a column and save() writes what is left, so every
 * education, experience, family, certificate and course the wizard collected
 * was written as an empty row carrying nothing but candidate_id. Nothing
 * errored; the rows were simply blank afterwards.
 *
 * The map lives here, once, because the live preview beside the form has to
 * agree with what the save will do. A preview drawn from the form's own names
 * while the save threw them away would show somebody a CV they were never
 * going to get.
 *
 * Three answers have nowhere of their own to go - a family member's
 * occupation and phone number, a certificate's expiry, a course's training
 * institution, which is a foreign key to an institution table and not free
 * text. They are folded into that row's `detail` rather than dropped, because
 * dropping them silently is the fault being fixed.
 */
trait CandidateWizardTrait
{
    /**
     * An id from a reference table, looked up by its title.
     *
     * @param string $model The table alias.
     * @param string|null $title What the form offered, e.g. "SMA".
     * @return int|null
     */
    protected function referenceIdByTitle($model, $title)
    {
        $title = trim((string)$title);
        if ($title === '') {
            return null;
        }

        try {
            $row = TableRegistry::getTableLocator()->get($model)->find()
                ->select(['id'])
                ->where(['title' => $title])
                ->enableHydration(false)
                ->first();
        } catch (\Exception $e) {
            // A reference table this installation does not carry is not a
            // reason to lose the rest of the row.
            return null;
        }

        return $row ? (int)$row['id'] : null;
    }

    /**
     * The year out of whatever the form sent - a year, or a date.
     *
     * @param string|null $value
     * @return int|null
     */
    protected function wizardYear($value)
    {
        if (preg_match('/(\d{4})/', (string)$value, $m)) {
            return (int)$m[1];
        }

        return null;
    }

    /**
     * A date out of a year or a date.
     *
     * @param string|null $value
     * @return string|null As Y-m-d, or null.
     */
    protected function wizardDate($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return substr($value, 0, 10);
        }
        $year = $this->wizardYear($value);

        return $year ? sprintf('%04d-01-01', $year) : null;
    }

    /**
     * Join what has nowhere else to go into one readable line.
     *
     * @param array $parts ['label' => value, ...]
     * @return string|null
     */
    protected function wizardDetail(array $parts)
    {
        $said = [];
        foreach ($parts as $label => $value) {
            $value = trim((string)$value);
            if ($value !== '') {
                $said[] = $label === '' ? $value : $label . ': ' . $value;
            }
        }

        return $said ? implode(' | ', $said) : null;
    }

    /**
     * Turn the wizard's rows into rows the table can hold.
     *
     * @param string $kind educations, experiences, families, certifications, courses.
     * @param array $rows What the session holds.
     * @return array Rows keyed by real column names.
     */
    protected function wizardRows($kind, array $rows)
    {
        $out = [];
        foreach ($rows as $row) {
            $row = (array)$row;
            switch ($kind) {
                case 'educations':
                    $mapped = [
                        'college_name' => isset($row['institution_name']) ? $row['institution_name'] : null,
                        'college_major' => isset($row['field_of_study']) ? $row['field_of_study'] : null,
                        // The wizard asks only when they finished.
                        'college_graduate_date' => $this->wizardDate(
                            isset($row['year_graduated']) ? $row['year_graduated'] : null
                        ),
                        'master_strata_id' => $this->referenceIdByTitle('MasterStratas',
                            isset($row['level']) ? $row['level'] : null),
                    ];
                    break;

                case 'experiences':
                    $mapped = [
                        'company_name' => isset($row['company_name']) ? $row['company_name'] : null,
                        'title' => isset($row['position']) ? $row['position'] : null,
                        'employment_start_year' => $this->wizardYear(
                            isset($row['start_date']) ? $row['start_date'] : null),
                        'employment_end_year' => $this->wizardYear(
                            isset($row['end_date']) ? $row['end_date'] : null),
                        'job_detail' => isset($row['job_description']) ? $row['job_description'] : null,
                    ];
                    break;

                case 'families':
                    $mapped = [
                        'name' => isset($row['full_name']) ? $row['full_name'] : null,
                        'age' => isset($row['age']) && $row['age'] !== '' ? (int)$row['age'] : null,
                        'master_family_connection_id' => $this->referenceIdByTitle(
                            'MasterFamilyConnections',
                            isset($row['relationship']) ? $row['relationship'] : null),
                        // Tried as a reference row first: the CV prints the
                        // occupation in its own column, and a row that
                        // matches prints as "Petani" rather than as the whole
                        // detail line.
                        'master_occupation_id' => $this->referenceIdByTitle(
                            'MasterOccupations',
                            isset($row['occupation']) ? $row['occupation'] : null),
                        'detail' => $this->wizardDetail([
                            // The relationship is a column of its own above,
                            // so it is not repeated here. What is left is the
                            // free text no column holds - and the occupation
                            // again, for the reference tables that do not
                            // carry what was typed.
                            (string)__('Occupation') => isset($row['occupation']) ? $row['occupation'] : '',
                            (string)__('Contact') => isset($row['contact_number']) ? $row['contact_number'] : '',
                        ]),
                    ];
                    break;

                case 'certifications':
                    $mapped = [
                        'title' => isset($row['certification_name']) ? $row['certification_name'] : null,
                        'institution_name' => isset($row['issuing_organization']) ? $row['issuing_organization'] : null,
                        'certification_date' => $this->wizardDate(
                            isset($row['issue_date']) ? $row['issue_date'] : null),
                        'detail' => $this->wizardDetail([
                            (string)__('Expires') => isset($row['expiry_date']) ? $row['expiry_date'] : '',
                        ]),
                    ];
                    break;

                case 'courses':
                    $mapped = [
                        'title' => isset($row['course_name']) ? $row['course_name'] : null,
                        'course_year' => $this->wizardYear(
                            isset($row['start_date']) ? $row['start_date'] : null),
                        'detail' => $this->wizardDetail([
                            (string)__('Institution') => isset($row['training_institution']) ? $row['training_institution'] : '',
                        ]),
                    ];
                    break;

                default:
                    $mapped = [];
            }

            // A row where the form was opened and left alone is not a record.
            $hasSomething = false;
            foreach ($mapped as $value) {
                if ($value !== null && $value !== '') {
                    $hasSomething = true;
                    break;
                }
            }
            if ($hasSomething) {
                $out[] = $mapped;
            }
        }

        return $out;
    }

    /**
     * One reference row by id, or null.
     *
     * A reference table this installation does not carry, or an id that
     * matches nothing, is not a reason to lose the preview.
     *
     * @param string $model The table class name.
     * @param mixed $id The foreign key.
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function wizardReferenceRow($model, $id, array $contain = [])
    {
        if (!$id) {
            return null;
        }

        try {
            $query = TableRegistry::getTableLocator()->get($model)->find()
                ->where(['id' => $id]);
            if ($contain) {
                $query->contain($contain);
            }

            return $query->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * The candidate as the wizard has them so far, as an entity nobody saved.
     *
     * The CV element renders an entity and does not care whether it came from
     * the database, so the preview beside the form is the same document the
     * candidate will have afterwards - not a second one drawn to look like it.
     *
     * @param array $wizardData What the session holds.
     * @return \Cake\Datasource\EntityInterface
     */
    protected function wizardCandidateEntity(array $wizardData)
    {
        $candidates = TableRegistry::getTableLocator()->get('Candidates');

        $scalars = $wizardData;
        foreach (['educations', 'experiences', 'families', 'certifications', 'courses'] as $kind) {
            unset($scalars[$kind]);
        }

        $candidate = $candidates->newEntity($scalars, ['validate' => false]);

        // Set directly what mass assignment will not take, so the preview
        // shows what was typed rather than a blank where a blank is wrong.
        foreach ($scalars as $field => $value) {
            if ($candidates->getSchema()->hasColumn($field) && $candidate->get($field) === null) {
                $candidate->set($field, $value);
            }
        }

        $associations = [
            'educations' => ['CandidateEducations', 'candidate_educations'],
            'experiences' => ['CandidateExperiences', 'candidate_experiences'],
            'families' => ['CandidateFamilies', 'candidate_families'],
            'certifications' => ['CandidateCertifications', 'candidate_certifications'],
            'courses' => ['CandidateCourses', 'candidate_courses'],
        ];
        foreach ($associations as $kind => $where) {
            list($model, $property) = $where;
            $rows = isset($wizardData[$kind]) ? (array)$wizardData[$kind] : [];
            $table = TableRegistry::getTableLocator()->get($model);
            $entities = [];
            foreach ($this->wizardRows($kind, $rows) as $mapped) {
                $entity = $table->newEntity([], ['validate' => false]);
                foreach ($mapped as $column => $value) {
                    $entity->set($column, $value);
                }
                $entities[] = $entity;
            }
            $candidate->set($property, $entities);
        }

        // The reference rows the CV prints by name. The form prints the
        // Japanese spelling of each of these, which lives on the master row:
        // the foreign key alone prints nothing.
        //
        // The blood type was keyed 'blood_type_id' here, and the column is
        // master_blood_type_id, so the preview never resolved it and the
        // blood type was blank on the preview and filled on the saved CV -
        // the one thing this shared element exists to prevent.
        $byId = [
            'master_gender_id' => ['MasterGenders', 'master_gender'],
            'master_religion_id' => ['MasterReligions', 'master_religion'],
            'master_marriage_status_id' => ['MasterMarriageStatuses', 'master_marriage_status'],
            'master_blood_type_id' => ['MasterBloodTypes', 'master_blood_type'],
            // The four that make up the one address line the form prints.
            'master_propinsi_id' => ['MasterPropinsis', 'master_propinsi'],
            'master_kabupaten_id' => ['MasterKabupatens', 'master_kabupaten'],
            'master_kecamatan_id' => ['MasterKecamatans', 'master_kecamatan'],
            'master_kelurahan_id' => ['MasterKelurahans', 'master_kelurahan'],
            'acceptance_organization_id' => ['AcceptanceOrganizations', 'acceptance_organization'],
        ];
        // The order carries the employer too, and the CV reads it when the
        // candidate has none of their own.
        $order = $this->wizardReferenceRow('ApprenticeOrders',
            $candidate->get('apprentice_order_id'), ['AcceptanceOrganizations']);
        if ($order) {
            $candidate->set('apprentice_order', $order);
        }
        foreach ($byId as $field => $where) {
            list($model, $property) = $where;
            $row = $this->wizardReferenceRow($model, $candidate->get($field));
            if ($row) {
                $candidate->set($property, $row);
            }
        }

        // And the same for the rows hanging off the candidate: the CV prints
        // 続柄 from master_family_connections and the school level from
        // master_stratas, both of which this builds ids for above.
        $rowReferences = [
            'candidate_families' => [
                'master_family_connection_id' => ['MasterFamilyConnections', 'master_family_connection'],
                'master_occupation_id' => ['MasterOccupations', 'master_occupation'],
            ],
            'candidate_educations' => [
                'master_strata_id' => ['MasterStratas', 'master_strata'],
            ],
        ];
        foreach ($rowReferences as $property => $fields) {
            foreach ((array)$candidate->get($property) as $row) {
                foreach ($fields as $field => $where) {
                    list($model, $target) = $where;
                    $reference = $this->wizardReferenceRow($model, $row->get($field));
                    if ($reference) {
                        $row->set($target, $reference);
                    }
                }
            }
        }

        return $candidate;
    }
}
