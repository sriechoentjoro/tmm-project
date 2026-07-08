<?php
/**
 * Repairs double-encoded UTF-8 (mojibake) in template files.
 *
 * The PowerShell generators read UTF-8 bytes as Windows-1252 and re-saved
 * them as UTF-8, so 'プ' (E3 83 97) became 'ãƒ—'. This reverses it:
 * each codepoint <= U+00FF maps back to its single byte, the 27 CP1252
 * specials map back via the reverse table, and genuine multi-byte text
 * (already-correct content) is passed through untouched.
 *
 * Only writes a file when the repaired content is valid UTF-8 and no
 * mojibake signature remains. Originals are backed up first.
 *
 * Usage: php fix_mojibake.php [--scan-only]
 */

$scanOnly = in_array('--scan-only', $argv);
$backupDir = '/root/template_mojibake_backup_' . date('Ymd');

// CP1252 codepoint -> byte for the 27 non-Latin1 positions
$cp1252 = [
    0x20AC => 0x80, 0x201A => 0x82, 0x0192 => 0x83, 0x201E => 0x84,
    0x2026 => 0x85, 0x2020 => 0x86, 0x2021 => 0x87, 0x02C6 => 0x88,
    0x2030 => 0x89, 0x0160 => 0x8A, 0x2039 => 0x8B, 0x0152 => 0x8C,
    0x017D => 0x8E, 0x2018 => 0x91, 0x2019 => 0x92, 0x201C => 0x93,
    0x201D => 0x94, 0x2022 => 0x95, 0x2013 => 0x96, 0x2014 => 0x97,
    0x02DC => 0x98, 0x2122 => 0x99, 0x0161 => 0x9A, 0x203A => 0x9B,
    0x0153 => 0x9C, 0x017E => 0x9E, 0x0178 => 0x9F,
];

function repairDoubleEncoding($content, array $cp1252)
{
    $out = '';
    $len = strlen($content);
    $i = 0;
    while ($i < $len) {
        $byte = ord($content[$i]);
        if ($byte < 0x80) {                  // ASCII passes through
            $out .= $content[$i];
            $i++;
            continue;
        }
        // decode one UTF-8 sequence
        if (($byte & 0xE0) === 0xC0) { $need = 2; $cp = $byte & 0x1F; }
        elseif (($byte & 0xF0) === 0xE0) { $need = 3; $cp = $byte & 0x0F; }
        elseif (($byte & 0xF8) === 0xF0) { $need = 4; $cp = $byte & 0x07; }
        else { $out .= $content[$i]; $i++; continue; } // stray byte, keep
        if ($i + $need > $len) { $out .= substr($content, $i); break; }
        $seq = substr($content, $i, $need);
        for ($j = 1; $j < $need; $j++) {
            $cp = ($cp << 6) | (ord($seq[$j]) & 0x3F);
        }
        if ($cp <= 0xFF) {
            $out .= chr($cp);                 // was a raw byte read as CP1252/Latin1
        } elseif (isset($cp1252[$cp])) {
            $out .= chr($cp1252[$cp]);        // CP1252 special back to its byte
        } else {
            $out .= $seq;                     // genuine text (kanji etc.) untouched
        }
        $i += $need;
    }
    return $out;
}

function hasMojibake($content)
{
    // signatures of double-encoded UTF-8 leads:
    //  'ã'-runs (Japanese kana), 'â'+continuation (symbols/punctuation),
    //  'ð'+U+009F (4-byte emoji), 'ï¼' (fullwidth forms)
    return (bool)preg_match(
        '/\xC3\xA3[\xC2-\xE2\x80-\xBF]|\xC3\xA5|\xC3\xA6|\xC3\xA7\xC2|\xC3\xAF\xC2\xBC|\xC3\xA2[\xC2-\xC3][\x80-\xBF]|\xC3\xB0\xC2\x9F/',
        $content
    );
}

$files = glob('/var/www/html/tmm/src/Template/*/*.ctp');
$files = array_merge($files, glob('/var/www/html/tmm/src/Template/*.ctp'));

$fixed = $skippedClean = $failed = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (!hasMojibake($content)) {
        $skippedClean++;
        continue;
    }
    if ($scanOnly) {
        echo "MOJIBAKE: $file\n";
        $fixed++;
        continue;
    }

    $repaired = repairDoubleEncoding($content, $cp1252);

    // a file may have been corrupted twice; repair until stable (max 3)
    $rounds = 1;
    while (hasMojibake($repaired) && $rounds < 3) {
        $repaired = repairDoubleEncoding($repaired, $cp1252);
        $rounds++;
    }

    if (!mb_check_encoding($repaired, 'UTF-8')) {
        echo "FAILED (invalid UTF-8 after repair): $file\n";
        $failed++;
        continue;
    }
    if (hasMojibake($repaired)) {
        echo "FAILED (mojibake remains after $rounds rounds): $file\n";
        $failed++;
        continue;
    }

    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0700, true);
    }
    copy($file, $backupDir . '/' . str_replace('/', '__', substr($file, strlen('/var/www/html/tmm/src/Template/'))));
    file_put_contents($file, $repaired);
    echo "fixed ($rounds round" . ($rounds > 1 ? 's' : '') . "): $file\n";
    $fixed++;
}

echo "----\n" . ($scanOnly ? "with mojibake: " : "fixed: ") . "$fixed, clean: $skippedClean, failed: $failed\n";
if (!$scanOnly && $fixed > 0) {
    echo "backups in: $backupDir\n";
}
