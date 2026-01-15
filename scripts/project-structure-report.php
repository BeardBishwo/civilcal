<?php
/**
 * Project structure reporter.
 * - Skips: .git, .gitignore, .kilocode, .specify
 * - Vendor: prints package names from composer.json instead of walking directories
 */

$root = __DIR__;
$skipDirs = ['.git', '.kilocode', '.specify'];
$skipFiles = ['.gitignore'];
$reportLines = [];
$collapseDirs = [
    $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'ratelimit',
    $root . DIRECTORY_SEPARATOR . 'node_modules'
];

function appendOutput(string $line = '', bool $newline = true): void
{
    global $reportLines;
    $reportLines[] = $line;
    if ($newline) {
        echo $line . PHP_EOL;
    } else {
        echo $line;
    }
}

function collectStats(string $path, bool $exclude = false): array
{
    global $collapseDirs;
    $stats = [
        'dirs' => 0,
        'files' => 0,
        'size' => 0,
        'extCounts' => [],
        'phpLines' => 0
    ];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            function ($file, $key, $iterator) use ($exclude, $collapseDirs) {
                $fullPath = $file->getPathname();
                if ($exclude && in_array($fullPath, $collapseDirs, true)) {
                    return false;
                }
                return true;
            }
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            $stats['dirs']++;
        } elseif ($file->isFile()) {
            $stats['files']++;
            $stats['size'] += $file->getSize();
            $ext = strtolower($file->getExtension());
            $stats['extCounts'][$ext] = ($stats['extCounts'][$ext] ?? 0) + 1;
            if ($ext === 'php') {
                $stats['phpLines'] += count(file($file->getPathname()));
            }
        }
    }
    return $stats;
}

function formatBytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

function getComposerPackages(string $root): array
{
    $composerFile = $root . DIRECTORY_SEPARATOR . 'composer.json';
    if (!is_file($composerFile)) {
        return [];
    }

    $json = json_decode(file_get_contents($composerFile), true);
    if (!is_array($json) || !isset($json['require']) || !is_array($json['require'])) {
        return [];
    }

    $packages = [];
    foreach ($json['require'] as $name => $version) {
        // Hide PHP platform entry
        if (strtolower($name) === 'php') {
            continue;
        }
        $packages[] = sprintf('%s (%s)', $name, $version);
    }

    sort($packages, SORT_NATURAL | SORT_FLAG_CASE);
    return $packages;
}

$vendorPackages = getComposerPackages($root);

function printLine(string $prefix, bool $isLast, string $label): void
{
    $connector = $isLast ? '└── ' : '├── ';
    appendOutput($prefix . $connector . $label);
}

function walkTree(string $path, string $prefix = '', bool $isRoot = true): void
{
    global $skipDirs, $skipFiles, $vendorPackages, $collapseDirs;

    $entries = @scandir($path);
    if ($entries === false) {
        return;
    }

    // Filter and sort entries
    $entries = array_values(array_filter($entries, function ($entry) use ($skipDirs, $skipFiles) {
        if ($entry === '.' || $entry === '..') return false;
        if (in_array($entry, $skipDirs, true)) return false;
        if (in_array($entry, $skipFiles, true)) return false;
        return true;
    }));
    sort($entries, SORT_NATURAL | SORT_FLAG_CASE);

    $count = count($entries);
    foreach ($entries as $idx => $entry) {
        $fullPath = $path . DIRECTORY_SEPARATOR . $entry;
        $isLast = ($idx === $count - 1);

        if (is_dir($fullPath)) {
            if (in_array($fullPath, $collapseDirs, true)) {
                printLine($prefix, $isLast, $entry . '/');
                continue;
            }
            if ($entry === 'vendor') {
                printLine($prefix, $isLast, 'vendor/');
                $childPrefix = $prefix . ($isLast ? '    ' : '│   ');
                foreach ($vendorPackages as $pIdx => $package) {
                    $isPkgLast = ($pIdx === count($vendorPackages) - 1);
                    printLine($childPrefix, $isPkgLast, $package);
                }
                continue;
            }

            printLine($prefix, $isLast, $entry . '/');
            $childPrefix = $prefix . ($isLast ? '    ' : '│   ');
            walkTree($fullPath, $childPrefix, false);
        } elseif (is_file($fullPath)) {
            printLine($prefix, $isLast, $entry);
        }
    }
}

$stats = collectStats($root);
$excludedStats = collectStats($root, true);
appendOutput('=== PROJECT STATISTICS ===');
appendOutput('📁 Directories: ' . number_format($stats['dirs']));
appendOutput('📄 Files: ' . number_format($stats['files']));
appendOutput('💾 Total Size: ' . formatBytes($stats['size']));
appendOutput('🐘 PHP Files: ' . ($stats['extCounts']['php'] ?? 0) . ' (' . number_format($stats['phpLines']) . ' lines)');
appendOutput('📋 JSON Files: ' . ($stats['extCounts']['json'] ?? 0));
appendOutput('🎨 CSS Files: ' . ($stats['extCounts']['css'] ?? 0));
appendOutput('⚡ JS Files: ' . ($stats['extCounts']['js'] ?? 0));
appendOutput('📝 MD Files: ' . ($stats['extCounts']['md'] ?? 0));
appendOutput('🗄️ SQL Files: ' . ($stats['extCounts']['sql'] ?? 0));
appendOutput('');
appendOutput('=== STATISTICS (EXCLUDING VENDOR/NODE_MODULES/STORAGE CACHE) ===');
appendOutput('📁 Directories: ' . number_format($excludedStats['dirs']));
appendOutput('📄 Files: ' . number_format($excludedStats['files']));
appendOutput('💾 Total Size: ' . formatBytes($excludedStats['size']));
appendOutput('🐘 PHP Files: ' . ($excludedStats['extCounts']['php'] ?? 0) . ' (' . number_format($excludedStats['phpLines']) . ' lines)');
appendOutput('📋 JSON Files: ' . ($excludedStats['extCounts']['json'] ?? 0));
appendOutput('🎨 CSS Files: ' . ($excludedStats['extCounts']['css'] ?? 0));
appendOutput('⚡ JS Files: ' . ($excludedStats['extCounts']['js'] ?? 0));
appendOutput('📝 MD Files: ' . ($excludedStats['extCounts']['md'] ?? 0));
appendOutput('🗄️ SQL Files: ' . ($excludedStats['extCounts']['sql'] ?? 0));
appendOutput('');
appendOutput('--- FILE TREE ---');
appendOutput(basename($root) . '/');
walkTree($root);

$markdownPath = $root . DIRECTORY_SEPARATOR . 'project-structure-report.md';
$timestamp = date('Y-m-d H:i:s');
$mdContent = "# Project Structure Report\n\nGenerated: {$timestamp}\n\n";
$mdContent .= "```\n" . implode("\n", $reportLines) . "\n```\n";
file_put_contents($markdownPath, $mdContent);
