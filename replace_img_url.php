

<?php

/**
 * Script de remplacement automatique : route('media.show', ...) → img_url(...)
 */

$baseDir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir)
);

$count = 0;
$files = [];

foreach ($iterator as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;

    // Pattern 1 : route('media.show', ['path' => ltrim($X, '/')])
    $content = preg_replace(
        "/route\('media\.show', \['path' => ltrim\(([^,]+), '\/'\)\]\)/",
        'img_url($1)',
        $content
    );

    // Pattern 2 : route('media.show', ['path' => $X])
    $content = preg_replace(
        "/route\('media\.show', \['path' => ([^\]]+)\]\)/",
        'img_url($1)',
        $content
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        $files[] = str_replace(__DIR__ . '/', '', $path);
        $count++;
    }
}

echo "✅ Fichiers modifiés : {$count}\n\n";
foreach ($files as $f) {
    echo "  • {$f}\n";
}
