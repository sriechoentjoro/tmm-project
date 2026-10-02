<?php
namespace App\Controller;

/**
 * Which institution a login account belongs to, by name.
 *
 * users.institution_id and users.institution_type say which institution an
 * account belongs to, and no screen ever turned that into a name. The user list
 * showed "LPK #12", the detail page showed "Vocational Training" and "#12", and
 * the account holder's own profile had a row for it guarded on
 * $user->has('vocational_training_institution') - an association UsersTable does
 * not declare and never could, because institution_id points at one of two
 * different tables depending on institution_type. So that row has never
 * rendered, and an administrator reading the list had to go and look up what 12
 * was.
 *
 * It cannot be a belongsTo for that same reason, so it is resolved here instead,
 * in one place rather than in four screens - the password reset already had its
 * own copy, and that copy compared institution_type with 'special_skill_support',
 * a spelling nothing in this application writes.
 *
 * What is written: LpkRegistrationController sets 'vocational_training', in the
 * three places that create or activate an LPK account, and nothing creates an
 * account of any other kind. The special-skill spellings are matched anyway
 * because InstitutionRegistrationController uses 'special_skill' for the
 * institution's own type column, and an account made from that side would
 * reasonably carry the same word.
 *
 * The two tables do not even name their institutions in the same column:
 * vocational_training_institutions has name, special_skill_support_institutions
 * has company_name. A caller that reads ->name off whichever it got is wrong
 * half the time, which is why this returns the label already chosen.
 */
trait InstitutionNameTrait
{
    /**
     * The model and display column behind each spelling of institution_type.
     *
     * @param string|null $type What the account carries.
     * @return array [model alias, display column, controller for a link]
     */
    protected function institutionKind($type)
    {
        $special = ['special_skill_support', 'special_skill', 'special'];
        if (in_array((string)$type, $special, true)) {
            return ['SpecialSkillSupportInstitutions', 'company_name',
                'SpecialSkillSupportInstitutions'];
        }

        return ['VocationalTrainingInstitutions', 'name',
            'VocationalTrainingInstitutions'];
    }

    /**
     * One account's institution.
     *
     * @param \Cake\Datasource\EntityInterface|array|null $user The account.
     * @return array|null ['name' => ..., 'controller' => ..., 'id' => ...], or
     *  null when the account belongs to none or the institution is gone.
     */
    protected function institutionFor($user)
    {
        $id = is_array($user)
            ? (isset($user['institution_id']) ? $user['institution_id'] : null)
            : $user->institution_id;
        $type = is_array($user)
            ? (isset($user['institution_type']) ? $user['institution_type'] : null)
            : $user->institution_type;

        $found = $this->institutionNamesFor([
            ['institution_id' => $id, 'institution_type' => $type],
        ]);

        return isset($found[$type . ':' . $id]) ? $found[$type . ':' . $id] : null;
    }

    /**
     * The institutions a page full of accounts belongs to, in one query each.
     *
     * A list of fifty accounts must not become fifty lookups, so the ids are
     * gathered per kind and asked for together.
     *
     * @param iterable $users Accounts, as entities or arrays.
     * @return array "type:id" => ['name' => ..., 'controller' => ..., 'id' => ...]
     */
    protected function institutionNamesFor($users)
    {
        $wanted = [];
        foreach ($users as $user) {
            $id = is_array($user)
                ? (isset($user['institution_id']) ? $user['institution_id'] : null)
                : $user->institution_id;
            if (!$id) {
                continue;
            }
            $type = is_array($user)
                ? (isset($user['institution_type']) ? $user['institution_type'] : null)
                : $user->institution_type;
            list($model) = $this->institutionKind($type);
            $wanted[$model][(int)$id] = $type;
        }

        $found = [];
        foreach ($wanted as $model => $ids) {
            list(, $column, $controller) = $this->institutionKind(reset($ids));
            try {
                $table = $this->loadModel($model);
                $rows = $table->find()
                    ->select([$table->aliasField('id'), $table->aliasField($column)])
                    ->where([$table->aliasField('id') . ' IN' => array_keys($ids)])
                    ->toArray();
            } catch (\Exception $e) {
                // An institution table that cannot be read says nothing about
                // the account, and the screens fall back to the id.
                continue;
            }
            foreach ($rows as $row) {
                $id = (int)$row->id;
                if (!isset($ids[$id])) {
                    continue;
                }
                $found[$ids[$id] . ':' . $id] = [
                    'name' => (string)$row->get($column),
                    'controller' => $controller,
                    'id' => $id,
                ];
            }
        }

        return $found;
    }
}
