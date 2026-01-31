<?php
$plugins = WP_CLI::runcommand('plugin list --format=json', ['return' => true]);
$plugins = json_decode($plugins, true);

foreach ($plugins as $plugin) {
$path = WP_CONTENT_DIR . '/plugins/' . $plugin['name'];
if (is_dir($path)) {
$size = getFolderSize($path);
echo "{$plugin['name']} | {$plugin['version']} | " . formatBytes($size) . PHP_EOL;
}
}

function getFolderSize($path) {
$size = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
$size += $file->getSize();
}
return $size;
}

function formatBytes($bytes, $precision = 2) {
$units = ['B', 'KB', 'MB', 'GB'];
$bytes = max($bytes, 0);
$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
$pow = min($pow, count($units) - 1);
$bytes /= pow(1024, $pow);
return round($bytes, $precision) . ' ' . $units[$pow];
}
