<?php
// Create necessary directory structure for AdvancedCache
$dirs = [
    'storage/cache/advanced',
    'storage/cache/advanced/00',
    'storage/cache/advanced/01',
    'storage/cache/advanced/02',
    'storage/cache/advanced/03',
    'storage/cache/advanced/04',
    'storage/cache/advanced/05',
    'storage/cache/advanced/06',
    'storage/cache/advanced/07',
    'storage/cache/advanced/08',
    'storage/cache/advanced/09',
    'storage/cache/advanced/0a',
    'storage/cache/advanced/0b',
    'storage/cache/advanced/0c',
    'storage/cache/advanced/0d',
    'storage/cache/advanced/0e',
    'storage/cache/advanced/0f',
    'storage/cache/advanced/10',
    'storage/cache/advanced/11',
    'storage/cache/advanced/12',
    'storage/cache/advanced/13',
    'storage/cache/advanced/14',
    'storage/cache/advanced/15',
    'storage/cache/advanced/16',
    'storage/cache/advanced/17',
    'storage/cache/advanced/18',
    'storage/cache/advanced/19',
    'storage/cache/advanced/1a',
    'storage/cache/advanced/1b',
    'storage/cache/advanced/1c',
    'storage/cache/advanced/1d',
    'storage/cache/advanced/1e',
    'storage/cache/advanced/1f'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: {$dir}\n";
    }
}

echo "All cache directories created successfully!\n";
