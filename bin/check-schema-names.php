#!/usr/bin/env php
<?php
/**
 * Hold the model layer to the schema, not to its own idea of it.
 *
 * Every other check in this repository reads the application's description of
 * the schema - the @property list on an entity, the names a template writes,
 * the fields a validator names. That works until the application is wrong in
 * more than one place at once, and then the wrong answers agree with each
 * other and everything looks consistent.
 *
 * special_skill_support_institutions is what that looks like. The table class
 * sets displayField('company_name'), the entity documents company_name,
 * $_accessible lists it, five validation rules name it, both forms post it,
 * the index sorts and filters on it - and the column is called name.
 * UsersController passes 'valueField' => 'name' explicitly, which is somebody
 * having hit this and worked around it rather than fixed it. Nothing could
 * catch it, because every source anybody was reading said company_name.
 *
 * So this one reads the SQL: the dumps under database_exports, the migrations
 * under database/migrations and config/sql, and the loose .sql files at the
 * root. That is an independent account of the schema - written by the
 * database, not by the code being checked.
 *
 * It is not the live database. A dump goes stale, and a table the repository
 * never creates cannot be checked at all; both are reported rather than
 * counted as clean. bin/check-entity-columns.php asks the real database.
 *
 * Usage:
 *     php bin/check-schema-names.php          report problems, exit 1 if any
 *     php bin/check-schema-names.php --all    also list every table resolved
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

/**
 * Names this reports that are not mistakes, with the reason.
 *
 * Keyed table.column. A column that exists on the live database but that no
 * SQL in this repository creates belongs here rather than being quietly
 * dropped from the report: the gap is in the repository, and writing it down
 * is how it stays visible.
 */
const EXPECTED = [
    // Added by hand on the live database. CandidatesTable::fitnessFilter()
    // guards every read of it with hasColumn('is_fit') precisely because the
    // repository's SQL does not create it, and MasterMedicalCheckUpResult
    // makes it accessible so the form can set it. Run
    //   php bin/query.php cms_masters \
    //     "SHOW COLUMNS FROM master_medical_check_up_results"
    // and add the migration, and this line can go.
    'master_medical_check_up_results.is_fit'
        => 'on the live database; no migration in this repository creates it',

    // The dump gives this table four columns - applicant_id, document_id,
    // submitted, submission_date - and no primary key at all. The table class
    // sets none either, so CakePHP assumes id: get() puts it in the WHERE,
    // and the index screen sorts on it and passes $row['id'] to the view and
    // edit links. If the dump is current, view/edit/delete on that screen
    // cannot work. Which way to fix it depends on the live table, so:
    //   php bin/query.php cms_lpk_candidate_documents \
    //     "SHOW COLUMNS FROM candidate_submission_documents"
    // A column there means the dump is stale and wants a migration; no column
    // means the table needs one, or the screen must stop pretending.
    'candidate_submission_documents.id'
        => 'the dump shows no id and no primary key; not yet settled against the live table',
];

// For Inflector alone, to pair a table class with its entity. Cake knows the
// irregular plurals in this schema - MasterStratas, CandidateFamilies - and
// guessing them here would get some of them wrong.
require $root . '/vendor/autoload.php';

use Cake\Utility\Inflector;

/**
 * Every column this repository's SQL says a table has.
 *
 * CREATE TABLE and ALTER TABLE ... ADD/CHANGE both count: the schema is the
 * sum of what has been applied, and several of these tables are only ever
 * widened by a migration.
 *
 * A table name is not unique across this schema. There are fourteen
 * databases here, and seven table names appear in more than one of them with
 * different columns - chart_of_accounts is declared `code`/`title` in
 * asahi_accountings and `account_code`/`account_name`/`account_type` in
 * asahi_inventories. Merging those into one list answers the wrong question
 * in both directions: it accuses a name that is right in the database the
 * model actually opens, and it passes a name that is wrong there because
 * some other database has the column. So the definitions are kept per
 * database as well, and the caller resolves them against the connection the
 * model declares.
 *
 * @param string $root The repository root.
 * @param array|null $sources Filled with [table => [database => [column => true]]].
 * @return array [table => [column => true]]
 */
function columnsFromSql($root, &$sources = null)
{
    $sources = [];
    // JSON belongs here: stakeholder_activities.additional_data is declared
    // JSON, and leaving the type out made this report it as a column that
    // does not exist - the check accusing the code of its own blind spot.
    $types = 'int|integer|varchar|char|text|date|datetime|timestamp|tinyint|decimal'
        . '|float|double|enum|bigint|smallint|longtext|mediumtext|blob|time|year'
        . '|bool|boolean|json|set|binary|varbinary|real|numeric';
    $out = [];
    $files = array_merge(
        glob($root . '/database_exports/*/*.sql'),
        glob($root . '/database/migrations/*.sql'),
        glob($root . '/config/sql/*.sql'),
        glob($root . '/*.sql')
    );
    sort($files);

    foreach ($files as $path) {
        $sql = file_get_contents($path);

        // A dump is named after the database it came out of. A migration
        // says so with USE; the ones that say nothing are recorded as
        // unstated rather than silently attributed to a database.
        $fileDatabase = null;
        if (preg_match('#database_exports/[^/]+/(\w+)\.sql$#', $path, $named)) {
            $fileDatabase = $named[1];
        }

        if (preg_match_all('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?\s*\((.*?)\n\)\s*(?:ENGINE|;)/is',
            $sql, $tables, PREG_SET_ORDER)) {
            foreach ($tables as $one) {
                $table = strtolower($one[1]);
                if (!isset($out[$table])) {
                    $out[$table] = [];
                }
                $database = $fileDatabase === null
                    ? usedDatabase($sql, strpos($sql, $one[0]))
                    : $fileDatabase;
                if (preg_match_all('/^\s*`?(\w+)`?\s+(?:' . $types . ')\b/im', $one[2], $columns)) {
                    foreach ($columns[1] as $column) {
                        $out[$table][strtolower($column)] = true;
                        $sources[$table][$database][strtolower($column)] = true;
                    }
                }
            }
        }

        if (preg_match_all('/ALTER TABLE\s+`?(\w+)`?(.*?);/is', $sql, $alters, PREG_SET_ORDER)) {
            foreach ($alters as $one) {
                $table = strtolower($one[1]);
                if (!isset($out[$table])) {
                    $out[$table] = [];
                }
                $database = $fileDatabase === null
                    ? usedDatabase($sql, strpos($sql, $one[0]))
                    : $fileDatabase;
                // An ALTER widens a table that already exists somewhere. It
                // is only recorded against a database that has the table, so
                // a migration cannot invent a table in a database it was
                // never run against.
                $widen = isset($sources[$table][$database]);
                if (preg_match_all('/ADD\s+(?:COLUMN\s+)?`?(\w+)`?\s+(?:' . $types . ')\b/i',
                    $one[2], $added)) {
                    foreach ($added[1] as $column) {
                        $out[$table][strtolower($column)] = true;
                        if ($widen) {
                            $sources[$table][$database][strtolower($column)] = true;
                        }
                    }
                }
                // CHANGE names the old column and the new one; both have
                // existed, so neither is evidence of a mistake.
                if (preg_match_all('/CHANGE\s+(?:COLUMN\s+)?`?(\w+)`?\s+`?(\w+)`?\s+(?:' . $types . ')\b/i',
                    $one[2], $changed, PREG_SET_ORDER)) {
                    foreach ($changed as $pair) {
                        $out[$table][strtolower($pair[1])] = true;
                        $out[$table][strtolower($pair[2])] = true;
                        if ($widen) {
                            $sources[$table][$database][strtolower($pair[1])] = true;
                            $sources[$table][$database][strtolower($pair[2])] = true;
                        }
                    }
                }
                if (preg_match_all('/MODIFY\s+(?:COLUMN\s+)?`?(\w+)`?\s+(?:' . $types . ')\b/i',
                    $one[2], $modified)) {
                    foreach ($modified[1] as $column) {
                        $out[$table][strtolower($column)] = true;
                        if ($widen) {
                            $sources[$table][$database][strtolower($column)] = true;
                        }
                    }
                }
            }
        }
    }

    foreach ($out as $table => $columns) {
        if (!$columns) {
            unset($out[$table]);
        }
    }

    return $out;
}

/**
 * The database a statement is in, read from the last USE before it.
 *
 * @param string $sql The whole file.
 * @param int $at Where the statement starts.
 * @return string
 */
function usedDatabase($sql, $at)
{
    if (preg_match_all('/\bUSE\s+`?(\w+)`?/i', substr($sql, 0, $at), $used)) {
        return end($used[1]);
    }

    return '(unstated)';
}

/**
 * connection alias => database name, as config/app_datasources.php has it.
 *
 * Read rather than included: the file pulls in credentials from the
 * environment, and this needs nothing but the names.
 *
 * @param string $root The repository root.
 * @return array
 */
function connectionDatabases($root)
{
    static $map = null;

    if ($map === null) {
        $map = [];
        $php = @file_get_contents($root . '/config/app_datasources.php');
        if ($php !== false && preg_match('/\$databases\s*=\s*\[(.*?)\n\];/s', $php, $block)
            && preg_match_all("/'([\w]+)'\s*=>\s*'([\w]+)'/", $block[1], $pairs, PREG_SET_ORDER)) {
            foreach ($pairs as $pair) {
                $map[$pair[1]] = $pair[2];
            }
        }
    }

    return $map;
}

/**
 * The database a model's table lives in.
 *
 * @param string $root The repository root.
 * @param string $model A table class name, without the Table suffix.
 * @return string|null
 */
function databaseOf($root, $model)
{
    $path = $root . '/src/Model/Table/' . $model . 'Table.php';
    $connection = 'default';
    if (is_file($path)
        && preg_match('/defaultConnectionName\(\).*?return\s+\x27(\w+)\x27/s',
            file_get_contents($path), $m)) {
        $connection = $m[1];
    }
    $map = connectionDatabases($root);

    return isset($map[$connection]) ? $map[$connection] : null;
}

/**
 * The columns to judge a table by, for the model that opens it.
 *
 * Where one table name is declared differently in several databases, the
 * merged list is not an answer: it is the union of two tables that happen to
 * share a name. Only the database this model actually connects to settles it,
 * and when that database is not among the ones described here, nothing in
 * this repository can - which is reported, not guessed.
 *
 * @param string $table The table name.
 * @param string|null $database The database the model opens.
 * @param array $columns The merged list.
 * @param array $sources [table => [database => [column => true]]].
 * @return array [columns|null, reason|null]
 */
function columnsToJudgeBy($table, $database, array $columns, array $sources)
{
    if (!isset($columns[$table])) {
        return [null, 'no SQL here describes ' . $table];
    }

    $byDatabase = isset($sources[$table]) ? $sources[$table] : [];
    $shapes = [];
    foreach ($byDatabase as $where => $cols) {
        $names = array_keys($cols);
        sort($names);
        $shapes[implode(',', $names)] = true;
    }

    // One shape, however many databases carry it: the merged list is that
    // shape, and the ALTERs that widened it.
    if (count($shapes) < 2) {
        return [$columns[$table], null];
    }

    if ($database !== null && isset($byDatabase[$database])) {
        return [$byDatabase[$database], null];
    }

    return [null, $table . ' is declared differently in ' . count($byDatabase)
        . ' databases (' . implode(', ', array_keys($byDatabase)) . '), none of them '
        . ($database === null ? 'the one this model opens' : $database)];
}

/**
 * The table a class is about, and the names it claims that table has.
 *
 * @param string $path A *Table.php file.
 * @return array|null [table, [name => where it was said]]
 */
function claimsOf($path)
{
    $php = file_get_contents($path);
    if (!preg_match("/setTable\(\s*'(\w+)'/", $php, $m)) {
        return null;
    }
    $table = strtolower($m[1]);
    $claims = [];

    if (preg_match("/setDisplayField\(\s*'(\w+)'/", $php, $d)) {
        $claims[strtolower($d[1])][] = 'setDisplayField';
    }

    // The validator names a field per rule. Only the rules that take a field
    // name first; add() and others carry rule names, not columns.
    $rules = 'scalar|integer|boolean|date|dateTime|time|email|numeric|decimal|uuid'
        . '|notEmptyString|notEmptyDate|notEmptyDateTime|allowEmptyString'
        . '|allowEmptyDate|allowEmptyDateTime|allowEmptyFile|requirePresence'
        . '|maxLength|minLength|lengthBetween|inList|range|greaterThan';
    if (preg_match_all('/->(?:' . $rules . ")\(\s*'(\w+)'/", $php, $fields)) {
        foreach ($fields[1] as $field) {
            $claims[strtolower($field)][] = 'validation';
        }
    }

    return [$table, $claims];
}

/**
 * The columns an entity's docblock says its table has.
 *
 * @param string $path A Model/Entity file.
 * @return array [name => true]
 */
function documentedColumns($path)
{
    $php = file_get_contents($path);
    $out = [];

    // A property the entity computes is not a column. User::$role_names is
    // built by _getRoleNames() from the user's roles; documenting it is
    // correct and it will never be in the schema.
    $virtual = [];
    if (preg_match_all('/function _get(\w+)\(/', $php, $getters)) {
        foreach ($getters[1] as $name) {
            $virtual[strtolower(preg_replace('/(?<!^)(?=[A-Z])/', '_', $name))] = true;
        }
    }
    if (!preg_match_all('/@property\s+([^\s$]+)\s+\$(\w+)/', $php, $found, PREG_SET_ORDER)) {
        return $out;
    }
    foreach ($found as $one) {
        // An entity docblock documents its associations in the same way it
        // documents its columns, and an association is not a column:
        // \App\Model\Entity\Candidate $candidate is the record this one
        // belongs to, not a field on this table.
        if (strpos($one[1], 'Model\\Entity') !== false || substr($one[1], -2) === '[]') {
            continue;
        }
        if (isset($virtual[strtolower($one[2])])) {
            continue;
        }
        $out[strtolower($one[2])] = true;
    }

    return $out;
}

/**
 * The model an index screen really lists.
 *
 * Not the one its folder is named after. UserRoles/index.ctp sorts on
 * username, full_name and email - none of them columns of user_roles - and it
 * is right to: UserRolesController::index() paginates Users. A check that
 * assumed the folder reported three faults that were not there, which is how
 * this function came to exist.
 *
 * @param string $root The repository root.
 * @param string $screen The folder under src/Template.
 * @return string|null The model alias, or null when index() cannot be read.
 */
function paginatedModel($root, $screen)
{
    $controller = $root . '/src/Controller/' . $screen . 'Controller.php';
    if (!is_file($controller)) {
        return null;
    }
    $php = file_get_contents($controller);

    $at = strpos($php, 'public function index(');
    if ($at === false) {
        return null;
    }
    $next = strpos($php, 'public function ', $at + 10);
    $body = substr($php, $at, $next === false ? strlen($php) : $next - $at);

    // The shapes this application actually writes:
    //   paginate($this->Model)
    //   paginate($this->Model->find())
    //   paginate($this->helper($this->Model->find()))
    //   $query = $this->Model->find(); ... paginate($query)
    //   $query = $this->getInstitutionFilteredQuery('Model', ...); ... paginate($query)
    $alias = null;
    if (preg_match('/paginate\(\s*(?:\$this->\w+\(\s*)*\$this->(\w+)/', $body, $m)) {
        $alias = $m[1];
    }

    // A helper's name is not a model. When what came back is not one, the
    // model is named where the query was built instead - which is how
    // scopeToInstitution ended up being reported as a table.
    if ($alias === null || !is_file($root . '/src/Model/Table/' . $alias . 'Table.php')) {
        if (preg_match("/getInstitutionFilteredQuery\(\s*'(\w+)'/", $body, $m)) {
            $alias = $m[1];
        } elseif (preg_match('/\$\w+\s*=\s*\$this->(\w+)->find\(/', $body, $m)) {
            $alias = $m[1];
        }
    }

    return $alias;
}

/**
 * The table that model reads.
 *
 * @param string $root The repository root.
 * @param string $screen The template folder name.
 * @return string|null
 */
function paginatedTable($root, $screen)
{
    $alias = paginatedModel($root, $screen);
    if ($alias === null) {
        return null;
    }

    // The alias is a model name; its table is the alias in snake_case unless
    // the table class says otherwise.
    $table = $root . '/src/Model/Table/' . $alias . 'Table.php';
    if (is_file($table)
        && preg_match("/setTable\(\s*'(\w+)'/", file_get_contents($table), $t)) {
        return strtolower($t[1]);
    }

    return strtolower(preg_replace('/(?<!^)(?=[A-Z])/', '_', $alias));
}

/**
 * The column names an index screen sorts and filters on.
 *
 * @param string $path The index.ctp.
 * @return array [[kind, name], ...]
 */
function screenColumns($path)
{
    $markup = file_get_contents($path);
    $out = [];
    if (preg_match_all("/Paginator->sort\(\s*'([a-zA-Z0-9_.]+)'/", $markup, $sorts)) {
        foreach ($sorts[1] as $name) {
            $out[] = ['sort', $name];
        }
    }
    if (preg_match_all('/data-column="([a-zA-Z0-9_.]+)"/', $markup, $filters)) {
        foreach ($filters[1] as $name) {
            $out[] = ['filter', $name];
        }
    }

    return $out;
}

$columns = columnsFromSql($root, $sources);
$problems = [];
$unknown = [];
$checked = 0;

foreach (glob($root . '/src/Model/Table/*Table.php') as $path) {
    $claims = claimsOf($path);
    if ($claims === null) {
        continue;
    }
    list($table, $names) = $claims;
    $rel = ltrim(str_replace($root, '', $path), '/');

    $model = basename($path, 'Table.php');
    list($has, $why) = columnsToJudgeBy(
        $table, databaseOf($root, $model), $columns, $sources);
    if ($has === null) {
        $unknown[] = [$rel, $table, $why];
        continue;
    }
    $checked++;

    $stray = [];
    foreach ($names as $name => $where) {
        if (!isset($has[$name]) && !isset(EXPECTED[$table . '.' . $name])) {
            $stray[$name] = array_values(array_unique($where));
        }
    }

    // The entity beside it, documenting the same table.
    $entity = $root . '/src/Model/Entity/'
        . Inflector::singularize(basename($path, 'Table.php')) . '.php';
    if (is_file($entity)) {
        foreach (documentedColumns($entity) as $name => $_) {
            if (!isset($has[$name]) && !isset(EXPECTED[$table . '.' . $name])) {
                $stray[$name][] = '@property';
                $stray[$name] = array_values(array_unique($stray[$name]));
            }
        }
    }

    if ($stray) {
        $problems[] = [$rel, $table, $stray];
    } elseif ($showAll) {
        printf("  %-52s %s\n", $table, 'every name it uses is a column');
    }
}

// ---------------------------------------------------- index screens ----
//
// The same question asked of the templates: a sort or a filter naming a
// column the table has not got. CakePHP drops an unknown sort field without
// a word, so the header looks clickable and does nothing - which is how
// thirteen screens came to sort on Candidates.fullname and Trainees.fullname,
// neither table having ever had a column called fullname.
$screenProblems = [];
$screenUnchecked = [];
$screensChecked = 0;

foreach (glob($root . '/src/Template/*/index.ctp') as $path) {
    $screen = basename(dirname($path));
    $rel = ltrim(str_replace($root, '', $path), '/');
    $own = paginatedTable($root, $screen);

    if ($own === null) {
        $screenUnchecked[] = [$rel, 'index() does not paginate a model this can read'];
        continue;
    }
    list($ownColumns, $why) = columnsToJudgeBy(
        $own, databaseOf($root, paginatedModel($root, $screen)), $columns, $sources);
    if ($ownColumns === null) {
        $screenUnchecked[] = [$rel, $why];
        continue;
    }
    $screensChecked++;

    $stray = [];
    foreach (screenColumns($path) as $one) {
        list($kind, $name) = $one;
        if (strpos($name, '.') !== false) {
            // Candidates.name belongs to candidates, not to this screen's
            // table - and candidates is read through the Candidates model's
            // own connection, which need not be this screen's either.
            list($model, $field) = explode('.', $name, 2);
            $table = strtolower(preg_replace('/(?<!^)(?=[A-Z])/', '_', $model));
            $modelFile = $root . '/src/Model/Table/' . $model . 'Table.php';
            if (is_file($modelFile)
                && preg_match("/setTable\(\s*'(\w+)'/", file_get_contents($modelFile), $t)) {
                $table = strtolower($t[1]);
            }
            list($has, $_) = columnsToJudgeBy(
                $table, databaseOf($root, $model), $columns, $sources);
        } else {
            $table = $own;
            $field = $name;
            $has = $ownColumns;
        }
        if ($has === null) {
            continue;
        }
        if (!isset($has[strtolower($field)])
            && !isset(EXPECTED[$table . '.' . strtolower($field)])) {
            $stray[$kind . ':' . $name] = $table . ' has no ' . $field;
        }
    }
    if ($stray) {
        $screenProblems[] = [$rel, $stray];
    }
}

printf("%d table class(es) checked against %d table(s) described in SQL, "
    . "%d known exception(s)\n", $checked, count($columns), count(EXPECTED));

foreach (EXPECTED as $what => $why) {
    printf("  %-46s %s\n", $what, $why);
}

if ($unknown) {
    printf("\n%d table class(es) this cannot check:\n\n", count($unknown));
    foreach ($unknown as list($rel, $table, $why)) {
        printf("  %-46s %s\n", $table, $why);
    }
    echo "\n";
}

printf("%d index screen(s) checked\n", $screensChecked);

if ($screenUnchecked) {
    printf("\n%d index screen(s) this could not check:\n\n", count($screenUnchecked));
    foreach ($screenUnchecked as list($rel, $why)) {
        printf("  %-56s %s\n", $rel, $why);
    }
    echo "\n";
}

if ($screenProblems) {
    printf("\n%d index screen(s) sorting or filtering on a column that is not there:\n\n",
        count($screenProblems));
    foreach ($screenProblems as list($rel, $stray)) {
        printf("  %s\n", $rel);
        foreach ($stray as $what => $why) {
            printf("    %-34s %s\n", $what, $why);
        }
        echo "\n";
    }
    echo "CakePHP drops a sort on a field it cannot find, so the header looks\n";
    echo "clickable and does nothing at all.\n";
}

if (!$problems && !$screenProblems) {
    echo "every column name the model layer and the index screens use appears"
        . " in the schema\n";
    exit(0);
}

if (!$problems) {
    exit(1);
}

printf("\n%d table class(es) naming a column the schema does not have:\n\n", count($problems));
foreach ($problems as list($rel, $table, $stray)) {
    printf("  %s\n", $rel);
    foreach ($stray as $name => $where) {
        printf("    %s has no column %s  (said by: %s)\n",
            $table, $name, implode(', ', $where));
    }
    echo "\n";
}
echo "Vendor libraries and vendor code are not checked; this reads only the\n";
echo "SQL in this repository, which can be older than the live database.\n";
echo "bin/check-entity-columns.php asks the database itself.\n";
exit(1);
