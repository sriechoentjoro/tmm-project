<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Audit Model
 */
class AuditTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('logs');
        $this->setDisplayField('description');
        $this->setPrimaryKey('id');

    }

    /**
     * What each thing the decision trail records is called on this table.
     *
     * The `logs` table was designed before the trail was, and it already had
     * names for most of what the trail needs: the subject is `model` plus
     * `foreign_key`, the free text is `description`, the address is
     * `ip_address`. Adding subject_type, subject_id, detail and ip beside them
     * would have put two names on one meaning in a single table - the same
     * duplication this codebase already suffers from elsewhere, deliberately
     * created this time.
     *
     * So the trail maps onto whatever the table calls them. Each entry lists
     * the acceptable column names in order of preference; the first one the
     * table actually has wins, and on a table that has none of them the first
     * name in the list is what gets created, so a fresh installation ends up
     * with the same shape as the one already deployed.
     *
     * @var array canonical => [column name, older name, ...]
     */
    const COLUMN_CANDIDATES = [
        'user_id' => ['user_id'],
        'username' => ['username'],
        'role_names' => ['role_names'],
        'action' => ['action'],
        'subject_type' => ['model', 'subject_type'],
        'subject_id' => ['foreign_key', 'subject_id'],
        'subject_label' => ['subject_label'],
        'detail' => ['description', 'detail'],
        'ip' => ['ip_address', 'ip'],
        'user_agent' => ['user_agent'],
        'created' => ['created'],
    ];

    /**
     * Column definitions, by canonical name, for the ones that may need adding.
     *
     * @var array
     */
    const COLUMN_TYPES = [
        'user_id' => 'INT NULL',
        'username' => 'VARCHAR(100) NULL',
        'role_names' => 'VARCHAR(255) NULL',
        'action' => 'VARCHAR(100) NULL',
        'subject_type' => 'VARCHAR(100) NULL',
        'subject_id' => 'INT NULL',
        'subject_label' => 'VARCHAR(255) NULL',
        'detail' => 'TEXT NULL',
        'ip' => 'VARCHAR(45) NULL',
        'user_agent' => 'VARCHAR(255) NULL',
        'created' => 'DATETIME NULL',
    ];

    /**
     * Resolve the canonical names against the columns this table really has.
     *
     * @param array|null $columns Column names, or null to read the schema.
     * @return array canonical => real column name, for the ones that exist.
     */
    public function columnMap($columns = null)
    {
        if ($columns === null) {
            try {
                $columns = $this->getSchema()->columns();
            } catch (\Exception $e) {
                return [];
            }
        }

        $map = [];
        foreach (self::COLUMN_CANDIDATES as $canonical => $candidates) {
            foreach ($candidates as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $map[$canonical] = $candidate;
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * Read one canonical field off a row, whatever the column is called here.
     *
     * @param \Cake\Datasource\EntityInterface $entity The log row.
     * @param string $canonical Canonical field name.
     * @param array $map The resolved column map.
     * @return mixed
     */
    public function field($entity, $canonical, array $map)
    {
        if (!isset($map[$canonical])) {
            return null;
        }

        return $entity->get($map[$canonical]);
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_authentication_authorization';
    }
}
