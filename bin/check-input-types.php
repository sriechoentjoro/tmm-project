#!/usr/bin/env php
<?php
/**
 * Find form controls wired to the wrong kind of input.
 *
 * Two shapes, both produced by the same generator and both invisible until
 * somebody opens the form:
 *
 *  - a calendar on a foreign key. src/Template/Bake/Template/add.ctp decided
 *    date fields with preg_match('/(date|tanggal)/i', $field), and
 *    "candidate_id" contains the letters of "date". Every candidate selector
 *    it generated came out as a text box with a date picker bound to it -
 *    twenty-two of them across eleven screens. My own first sweep for this
 *    made the identical mistake and reported nothing wrong, which is why the
 *    test below splits the name into words instead of searching it.
 *
 *  - a foreign key as free text. The controller fetches the list, passes it to
 *    the template, and the template asks a person to type the number. The
 *    options variable sitting unused next to it is what makes this checkable.
 *
 * Only literal 'field' => [...] control calls are read. A control built from a
 * variable is the template doing something deliberate.
 *
 * Usage:
 *     php bin/check-input-types.php          report problems, exit 1 if any
 *     php bin/check-input-types.php --all    also list every control read
 */

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = dirname(__DIR__);
$showAll = in_array('--all', $argv, true);

/**
 * Words that make a column name a password.
 *
 * Whole words. The generator tested /(password|pass|pwd)/i as a substring,
 * and "pass" is inside passport, passed, bypass and every is_..._pass flag in
 * this schema - so the two flags the whole programme turns on, is_training_pass
 * and is_apprenticeship_pass, were masked boxes advising eight characters with
 * a symbol in them.
 */
const PASSWORD_WORDS = [
    'password', 'passwd', 'pwd', 'sandi', 'kata',
];

/**
 * Words that make a column name a date.
 *
 * Whole words only. "candidate" is not a date and "updated" is, and a
 * substring search gets both of them wrong - the first by matching, the
 * second by matching for the wrong reason.
 */
const DATE_WORDS = [
    'date', 'dates', 'tanggal', 'tgl', 'dob', 'birth', 'birthday',
    'expiry', 'expires', 'expired', 'issued', 'departure', 'arrival',
    'start', 'end', 'deadline', 'period', 'periode', 'schedule',
    'scheduled', 'time', 'day', 'updated', 'created', 'at',
];

/**
 * Every literal Form->control() call in the templates.
 *
 * @param string $root The repository root.
 * @return array [[file, line, field, options string], ...]
 */
function controls($root)
{
    $out = [];
    $pattern = '/Form->control\(\s*\'([a-zA-Z0-9_.]+)\'\s*,\s*\[(.*?)\n\s*\]\)/s';
    $walk = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root . '/src/Template')
    );
    $files = [];
    foreach ($walk as $file) {
        $path = $file->getPathname();
        // Bake's own templates are generators, not forms; they are checked by
        // reading the rule they apply, not the markup they emit.
        if (substr($path, -4) === '.ctp' && strpos($path, '/Template/Bake/') === false) {
            $files[] = $path;
        }
    }
    sort($files);

    foreach ($files as $path) {
        $php = file_get_contents($path);
        if (!preg_match_all($pattern, $php, $found, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            continue;
        }
        foreach ($found as $one) {
            $out[] = [
                ltrim(str_replace($root, '', $path), '/'),
                substr_count(substr($php, 0, $one[0][1]), "\n") + 1,
                $one[1][0],
                $one[2][0],
            ];
        }
    }

    return $out;
}

/**
 * Is this column name a date, by its words?
 *
 * @param string $field The column name.
 * @return bool
 */
function namesADate($field)
{
    $words = preg_split('/[_.]/', strtolower($field));

    return (bool)array_intersect($words, DATE_WORDS);
}

$calendars = [];
$freeText = [];
$masked = [];
$booleans = [];
$checked = 0;

foreach (controls($root) as list($rel, $line, $field, $body)) {
    $checked++;
    $isKey = substr($field, -3) === '_id';
    $hasDatepicker = (bool)preg_match('/(?<![-\w])datepicker(?![-\w])/', $body);
    $hasOptions = strpos($body, "'options'") !== false;

    $type = preg_match("/'type'\s*=>\s*'(\w+)'/", $body, $m) ? $m[1] : null;
    // A yes/no column, by the only naming rule this schema keeps to.
    $isFlag = strpos($field, 'is_') === 0;
    $words = preg_split('/[_.]/', strtolower($field));

    if ($hasDatepicker && $isKey && !namesADate($field)) {
        $calendars[] = [$rel, $line, $field];
    } elseif ($isKey && !$hasOptions
        && preg_match("/'type'\s*=>\s*'text'/", $body)) {
        $freeText[] = [$rel, $line, $field];
    } elseif ($type === 'password' && !array_intersect($words, PASSWORD_WORDS)) {
        $masked[] = [$rel, $line, $field];
    } elseif ($isFlag && $type !== null && $type !== 'checkbox' && $type !== 'select'
        && !$hasOptions) {
        $booleans[] = [$rel, $line, $field, $type];
    } elseif ($showAll) {
        printf("  %s:%d  %s\n", $rel, $line, $field);
    }
}

printf("%d form control(s) read\n", $checked);

if (!$calendars && !$freeText && !$masked && !$booleans) {
    echo "every control matches the kind of value its field holds\n";
    exit(0);
}

if ($calendars) {
    printf("\n%d foreign key(s) with a date picker bound to them:\n\n", count($calendars));
    foreach ($calendars as list($rel, $line, $field)) {
        printf("  %s:%d\n    %s\n", $rel, $line, $field);
    }
}

if ($freeText) {
    printf("\n%d foreign key(s) rendered as a text box, so somebody has to type the id:\n\n",
        count($freeText));
    foreach ($freeText as list($rel, $line, $field)) {
        printf("  %s:%d\n    %s\n", $rel, $line, $field);
    }
}

if ($masked) {
    printf("\n%d field(s) masked as a password that are not one:\n\n", count($masked));
    foreach ($masked as list($rel, $line, $field)) {
        printf("  %s:%d\n    %s\n", $rel, $line, $field);
    }
}

if ($booleans) {
    printf("\n%d yes/no field(s) asked as something other than a checkbox:\n\n",
        count($booleans));
    foreach ($booleans as list($rel, $line, $field, $type)) {
        printf("  %s:%d\n    %s  as type=%s\n", $rel, $line, $field, $type);
    }
}

echo "\nA foreign key belongs in a select built from the list its controller\n";
echo "already fetches. Deciding by the field's name is what put a calendar on\n";
echo "candidate_id: the word \"candidate\" contains \"date\".\n";
exit(1);
