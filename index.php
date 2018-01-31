<?php
require __DIR__ . '/vendor/autoload.php';

use Spatie\ImageOptimizer\OptimizerChainFactory;

$optimizerChain = OptimizerChainFactory::create();

$inputPath = realpath(__DIR__ . '/input');
$outputBasePath = realpath(__DIR__ . '/output');

$counter = 0;
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($inputPath)/*, RecursiveIteratorIterator::SELF_FIRST*/);

foreach ($objects as $filePath => $object) {
    $fileName = basename($filePath);
    $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    $skip = ['.', '..'];
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

    if (!in_array($fileName, $skip) && in_array($fileExt, $allowedExt)) {
        $relPath = str_replace($inputPath, '', $filePath);
        $relPath = str_replace($fileName, '', $relPath);

        $outputPath = preg_replace('/\s+/', '-', $outputBasePath . $relPath);

        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        $parts = explode(DIRECTORY_SEPARATOR, trim(str_replace($fileName, '', $filePath), DIRECTORY_SEPARATOR));
        $baseFileName = $parts[count($parts) - 1];
        $outputFileName = preg_replace('/\s+/', '-', $baseFileName . '-' . ($counter + 1) . '.' . $fileExt);

        $outputFilePath = $outputPath . $outputFileName;

        $optimizerChain->optimize($filePath, $outputFilePath);

        $counter++;
    }
}

echo ($counter + 1) . " Images converted" . PHP_EOL;