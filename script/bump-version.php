#!/usr/bin/env php
<?php
/**
 * Version bump + CHANGELOG entry – single source of truth.
 * ALWAYS use this script. Never edit api/version.php or CHANGELOG.md manually.
 *
 * Usage:
 *   php script/bump-version.php patch [--added "item"] [--changed "item"] [--fixed "item"]
 *   php script/bump-version.php minor [--added "item"] ...
 *   php script/bump-version.php major [--added "item"] ...
 *
 * Date/time is taken from system at execution – 100% correct.
 */
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$versionFile = $baseDir . '/api/version.php';
$changelogFile = $baseDir . '/CHANGELOG.md';

date_default_timezone_set('Europe/Prague');
$timestamp = date('d.m.Y H:i');

// Parse args
$bump = $argv[1] ?? null;
if ($bump === 'mark-pushed') {
    $changelog = file_get_contents($changelogFile);
    $pushedLine = '> Pushed: ' . date('d.m.Y H:i');
    $changelog = preg_replace('/^> Nepushnuto$/m', $pushedLine, $changelog, 1);
    file_put_contents($changelogFile, $changelog);
    echo "Replaced '> Nepushnuto' with '$pushedLine' for latest version.\n";
    exit(0);
}
if (!in_array($bump, ['patch', 'minor', 'major'], true)) {
    fwrite(STDERR, "Usage: php bump-version.php patch|minor|major [--added \"...\"] [--changed \"...\"] [--fixed \"...\"]\n");
    fwrite(STDERR, "       php bump-version.php mark-pushed   (after git push)\n");
    exit(1);
}

$added = [];
$changed = [];
$fixed = [];
for ($i = 2; $i < $argc; $i++) {
    if ($argv[$i] === '--added' && isset($argv[$i + 1])) {
        $added[] = $argv[$i + 1];
        $i++;
    } elseif ($argv[$i] === '--changed' && isset($argv[$i + 1])) {
        $changed[] = $argv[$i + 1];
        $i++;
    } elseif ($argv[$i] === '--fixed' && isset($argv[$i + 1])) {
        $fixed[] = $argv[$i + 1];
        $i++;
    }
}

// Read current version
if (!preg_match("/define\s*\(\s*'APP_VERSION'\s*,\s*'([\d.]+)'\s*\)/", file_get_contents($versionFile), $m)) {
    fwrite(STDERR, "Cannot parse APP_VERSION in api/version.php\n");
    exit(1);
}
$current = $m[1];
[$major, $minor, $patch] = array_map('intval', explode('.', $current));

switch ($bump) {
    case 'major':
        $major++;
        $minor = 0;
        $patch = 0;
        break;
    case 'minor':
        $minor++;
        $patch = 0;
        break;
    case 'patch':
    default:
        $patch++;
        break;
}
$newVersion = "$major.$minor.$patch";

// Update api/version.php
$versionContent = "<?php\n/**\n * Verze aplikace – jediný soubor ke změně při povýšení verze\n * vMAJOR.MINOR.PATCH – při běžné změně zvyš PATCH\n */\ndefine('APP_VERSION', '$newVersion');\n";
file_put_contents($versionFile, $versionContent);

// Build changelog entry
$entry = "## [$newVersion] – $timestamp\n";
$entry .= "> Nepushnuto\n\n";

if (!empty($added)) {
    $entry .= "### Přidáno\n";
    foreach ($added as $item) {
        $entry .= "- $item\n";
    }
    $entry .= "\n";
}
if (!empty($changed)) {
    $entry .= "### Změněno\n";
    foreach ($changed as $item) {
        $entry .= "- $item\n";
    }
    $entry .= "\n";
}
if (!empty($fixed)) {
    $entry .= "### Opraveno\n";
    foreach ($fixed as $item) {
        $entry .= "- $item\n";
    }
    $entry .= "\n";
}

if (empty($added) && empty($changed) && empty($fixed)) {
    $entry .= "### Změněno\n";
    $entry .= "- (doplň popis změn)\n\n";
}

// Prepend to CHANGELOG (after intro block)
$changelog = file_get_contents($changelogFile);
$introEnd = strpos($changelog, "\n## ");
if ($introEnd === false) {
    $introEnd = strlen($changelog);
}
$before = substr($changelog, 0, $introEnd + 1);
$after = substr($changelog, $introEnd + 1);
$changelog = $before . $entry . $after;
file_put_contents($changelogFile, $changelog);

echo "Version: $current → $newVersion\n";
echo "Timestamp: $timestamp\n";
echo "CHANGELOG entry added. After git push run: php script/bump-version.php mark-pushed\n";
