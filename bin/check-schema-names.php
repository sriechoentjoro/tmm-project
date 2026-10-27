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
 * @param string $root The repository root.
 * @return array [table => [column => true]]
 */
function columnsFromSql($root)
{
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

        if (preg_match_all('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?\s*\((.*?)\n\)\s*(?:ENGINE|;)/is',
            $sql, $tables, PREG_SET_ORDER)) {
            foreach ($tables as $one) {
                $table = strtolower($one[1]);
                if (!isset($out[$table])) {
                    $out[$table] = [];
                }
                if (preg_match_all('/^\s*`?(\w+)`?\s+(?:' . $types . ')\b/im', $one[2], $columns)) {
                    foreach ($columns[1] as $column) {
                        $out[$table][strtolower($column)] = true;
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
                if (preg_match_all('/ADD\s+(?:COLUMN\s+)?`?(\w+)`?\s+(?:' . $types . ')\b/i',
                    $one[2], $added)) {
                    foreach ($added[1] as $column) {
                        $out[$table][strtolower($column)] = true;
                    }
                }
                // CHANGE names the old column and the new one; both have
                // existed, so neither is evidence of a mistake.
                if (preg_match_all('/CHANGE\s+(?:COLUMN\s+)?`?(\w+)`?\s+`?(\w+)`?\s+(?:' . $types . ')\b/i',
                    $one[2], $changed, PREG_SET_ORDER)) {
                    foreach ($changed as $pair) {
                        $out[$table][strtolower($pair[1])] = true;
                        $out[$table][strtolower($pair[2])] = true;
                    }
                }
                if (preg_match_all('/MODIFY\s+(?:COLUMN\s+)?`?(\w+)`?\s+(?:' . $types . ')\b/i',
                    $one[2], $modified)) {
                    foreach ($modified[1] as $column) {
                        $out[$table][strtolower($column)] = true;
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

$columns = columnsFromSql($root);
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

    if (!isset($columns[$table])) {
        $unknown[] = [$rel, $table];
        continue;
    }
    $checked++;

    $stray = [];
    foreach ($names as $name => $where) {
        if (!isset($columns[$table][$name]) && !isset(EXPECTED[$table . '.' . $name])) {
            $stray[$name] = array_values(array_unique($where));
        }
    }

    // The entity beside it, documenting the same table.
    $entity = $root . '/src/Model/Entity/'
        . Inflector::singularize(basename($path, 'Table.php')) . '.php';
    if (is_file($entity)) {
        foreach (documentedColumns($entity) as $name => $_) {
            if (!isset($columns[$table][$name]) && !isset(EXPECTED[$table . '.' . $name])) {
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

printf("%d table class(es) checked against %d table(s) described in SQL, "
    . "%d known exception(s)\n", $checked, count($columns), count(EXPECTED));

foreach (EXPECTED as $what => $why) {
    printf("  %-46s %s\n", $what, $why);
}

if ($unknown) {
    printf("\n%d table class(es) whose table this repository never creates,\n"
        . "so nothing here can check them:\n\n", count($unknown));
    foreach ($unknown as list($rel, $table)) {
        printf("  %-46s %s\n", $table, $rel);
    }
    echo "\n";
}

if (!$problems) {
    echo "every column name the model layer uses appears in the schema\n";
    exit(0);
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
