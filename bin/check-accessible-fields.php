#!/usr/bin/env php
<?php
/**
 * Find fields handed to newEntity() or patchEntity() that are dropped silently.
 *
 * Mass assignment only sets what the entity's $_accessible list allows.
 * Anything else is discarded without an error, without a validation message
 * and without a log line: the save succeeds, the screen says it worked, and
 * the column keeps its old value or its default.
 *
 * Two flows in this application created a login account with
 *
 *     'status' => 'active', 'email_verified_at' => new Time()
 *
 * inside newEntity(). Neither field is accessible on User, so both were thrown
 * away and every account created that way landed on the column default,
 * pending_verification - while the code plainly said active, and a reviewer
 * reading it would agree. That is what makes this worth a tool rather than a
 * reading: the fault is invisible at the place it happens.
 *
 * It reads the literal arrays only. A variable (newEntity($data)) is the
 * ordinary form-handling case, where dropping an inaccessible field is the
 * protection working as intended; a literal array is the programmer naming
 * fields, and a named field that goes nowhere is a mistake every time.
 *
 * Usage:
 *     php bin/check-accessible-fields.php          report problems, exit 1
 *     php bin/check-accessible-fields.php --all    also list every call read
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

require $root . '/vendor/autoload.php';

use Cake\Utility\Inflector;

/**
 * The $_accessible list of each entity class, by its table alias.
 *
 * @param string $root The repository root.
 * @return array [alias => ['fields' => [name => true], 'where' => file]]
 */
function accessibleByAlias($root)
{
    $out = [];
    foreach (glob($root . '/src/Model/Entity/*.php') as $path) {
        $php = file_get_contents($path);
        $class = basename($path, '.php');
        $alias = Inflector::pluralize($class);

        if (!preg_match('/\$_accessible\s*=\s*\[(.*?)\n\s*\];/s', $php, $m)) {
            continue;
        }
        $fields = [];
        if (preg_match_all("/'([^']+)'\s*=>\s*(true|false)/", $m[1], $rows, PREG_SET_ORDER)) {
            foreach ($rows as $row) {
                $fields[$row[1]] = $row[2] === 'true';
            }
        }
        $out[$alias] = [
            'fields' => $fields,
            'where' => ltrim(str_replace($root, '', $path), '/'),
        ];
    }

    return $out;
}

/**
 * Split a literal array's top-level 'key' => ... pairs.
 *
 * Only the keys matter here, and only the ones at depth zero: a nested array
 * belongs to an association and is governed by that entity, not this one.
 *
 * @param string $body What stood between the outer brackets.
 * @return array The key names, in order.
 */
function topLevelKeys($body)
{
    $keys = [];
    $depth = 0;
    $length = strlen($body);
    for ($i = 0; $i < $length; $i++) {
        $c = $body[$i];
        if ($c === '[' || $c === '(') {
            $depth++;
            continue;
        }
        if ($c === ']' || $c === ')') {
            $depth--;
            continue;
        }
        if ($depth !== 0 || ($c !== "'" && $c !== '"')) {
            continue;
        }
        // A quoted string. Read it whole, then see whether => follows.
        $quote = $c;
        $value = '';
        for ($j = $i + 1; $j < $length; $j++) {
            if ($body[$j] === '\\') {
                $value .= $body[$j + 1];
                $j++;
                continue;
            }
            if ($body[$j] === $quote) {
                break;
            }
            $value .= $body[$j];
        }
        $after = substr($body, $j + 1, 10);
        if (preg_match('/^\s*=>/', $after)) {
            $keys[] = $value;
        }
        $i = $j;
    }

    return $keys;
}

/**
 * Read the literal array that starts at $from, balanced.
 *
 * @param string $php The file.
 * @param int $from Offset of the opening bracket.
 * @return string|null The body, or null when it does not close.
 */
function literalArray($php, $from)
{
    $depth = 0;
    $length = strlen($php);
    for ($i = $from; $i < $length; $i++) {
        if ($php[$i] === '[') {
            $depth++;
        } elseif ($php[$i] === ']') {
            $depth--;
            if ($depth === 0) {
                return substr($php, $from + 1, $i - $from - 1);
            }
        } elseif ($php[$i] === "'" || $php[$i] === '"') {
            $quote = $php[$i];
            for ($i++; $i < $length; $i++) {
                if ($php[$i] === '\\') {
                    $i++;
                } elseif ($php[$i] === $quote) {
                    break;
                }
            }
        }
    }

    return null;
}

$entities = accessibleByAlias($root);

$files = [];
foreach (['/src/Controller', '/src/Model', '/src/Shell', '/src/Command'] as $dir) {
    if (!is_dir($root . $dir)) {
        continue;
    }
    $walk = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . $dir));
    foreach ($walk as $file) {
        if (substr($file->getPathname(), -4) === '.php') {
            $files[] = $file->getPathname();
        }
    }
}
sort($files);

$problems = [];
$unknown = [];
$checked = 0;

foreach ($files as $path) {
    $rel = ltrim(str_replace($root, '', $path), '/');
    $php = file_get_contents($path);

    // $this->Users->newEntity([...]) / $Users->patchEntity($user, [...])
    $pattern = '/\$(?:this->)?(\w+)->(newEntity|patchEntity|newEntities|patchEntities)\(\s*'
        . '(?:\$[\w>\-\[\]\']+\s*,\s*)?\[/';
    if (!preg_match_all($pattern, $php, $found, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
        continue;
    }

    foreach ($found as $one) {
        $alias = $one[1][0];
        $open = $one[0][1] + strlen($one[0][0]) - 1;
        $body = literalArray($php, $open);
        if ($body === null) {
            continue;
        }
        $line = substr_count(substr($php, 0, $one[0][1]), "\n") + 1;
        $checked++;

        // Inside a Table class, $this->newEntity() builds that table's own
        // entity.
        if ($alias === 'this' && preg_match('#/Model/Table/(\w+)Table\.php$#', $rel, $own)) {
            $alias = $own[1];
        }

        if (!isset($entities[$alias])) {
            // A table with no entity class of its own gets Cake\ORM\Entity,
            // whose $_accessible is ['*' => true] - nothing is dropped, so
            // there is nothing to report. Anything else is a variable this
            // cannot trace back to a table, and saying so is better than
            // counting it as clean.
            if (!preg_match('/^[A-Z]/', $alias)) {
                $unknown[$alias][] = $rel . ':' . $line;
            }
            continue;
        }

        $fields = $entities[$alias]['fields'];
        if (isset($fields['*']) && $fields['*']) {
            continue;
        }

        $dropped = [];
        foreach (topLevelKeys($body) as $key) {
            if (!isset($fields[$key]) || !$fields[$key]) {
                $dropped[] = $key;
            }
        }
        if ($dropped) {
            $problems[] = [$rel, $line, $alias, $dropped, $entities[$alias]['where']];
        } elseif ($showAll) {
            printf("  %s:%d  %s - every field is accessible\n", $rel, $line, $alias);
        }
    }
}

printf(
    "%d literal mass-assignment(s) checked against %d entity class(es)\n",
    $checked,
    count($entities)
);

if ($unknown) {
    printf("\n%d variable(s) this cannot trace back to a table, so its fields\n"
        . "went unchecked:\n\n", count($unknown));
    foreach ($unknown as $alias => $where) {
        printf("  %s\n    %s\n", $alias, implode("\n    ", array_slice($where, 0, 3)));
    }
    echo "\n";
}

if (!$problems) {
    echo "every field named in a literal mass assignment is accessible on its entity\n";
    exit(0);
}

printf("\n%d call(s) naming a field the entity will discard:\n\n", count($problems));
foreach ($problems as list($rel, $line, $alias, $dropped, $entity)) {
    printf("  %s:%d\n    %s drops: %s\n    see %s\n\n", $rel, $line, $alias,
        implode(', ', $dropped), $entity);
}
echo "Mass assignment sets only what \$_accessible allows and discards the rest\n";
echo "without an error. Either add the field to \$_accessible, or - when it must\n";
echo "not be settable from a form - assign it on the entity after the call.\n";
exit(1);
