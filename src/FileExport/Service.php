<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\FileExport;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use ZipArchive;

class Service
{
    private array $logEntries = [];
    private string $zipFilePath;

    private string $tempDir
        {
            set(string $value) {
                if (!is_dir($value)) {
                    mkdir($value, 0755, true);
                }
                $this->tempDir = $value;
            }
        }

    private int $maxRetries = 3;

    public function __construct(
        private readonly Filesystem $sourceFilesystem,
    ) {
    }

    /**
     * Export files to a ZIP archive
     *
     * @param iterable $filePaths Array with keys as output filenames and values as source paths
     * @param string $outputZipPath Path where the ZIP file will be saved
     * @param string $tempDir Directory to create and write files during export process
     * @param int $maxRetries Maximum retries amount during files export
     * @return bool Success status
     */
    public function exportToZip(
        iterable $filePaths,
        string $outputZipPath,
        string $tempDir = '/tmp/exports',
        int $maxRetries = 3
    ): bool {
        $this->tempDir = $tempDir;
        $this->maxRetries = $maxRetries;
        $this->zipFilePath = $outputZipPath;
        $this->logEntries = [];
        $this->addLogEntry("Export started at " . date('Y-m-d H:i:s'));
        $this->addLogEntry("Total files to export: " . is_array($filePaths) ? count($filePaths) : "unknown");

        $zip = new ZipArchive();
        if ($zip->open($outputZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Failed to create ZIP file at {$outputZipPath}");
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($filePaths as $outputName => $sourcePath) {
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $outputFileName = $outputName . ($extension ? ".{$extension}" : "");

            if ($this->addFileToZip($zip, $sourcePath, $outputFileName)) {
                $successCount++;
            } else {
                $failureCount++;
            }

            // Close and reopen ZIP periodically to free memory
            if (($successCount + $failureCount) % 100 === 0) {
                $this->addLogEntry("Progress: {$successCount} successful, {$failureCount} failed");
                $logContent = implode(PHP_EOL, $this->logEntries);
                $zip->addFromString('export_log.txt', $logContent);

                $zip->close();
                $zip = new ZipArchive();
                $zip->open($outputZipPath, ZipArchive::CREATE);
            }
        }

        $this->addLogEntry("Export completed. Successful: {$successCount}, Failed: {$failureCount}");
        $logContent = implode(PHP_EOL, $this->logEntries);
        $zip->addFromString('export_log.txt', $logContent);

        $zip->close();
        return true;
    }

    /**
     * Add a single file to ZIP with retry logic
     */
    private function addFileToZip(ZipArchive $zip, string $sourcePath, string $outputFileName): bool
    {
        $attempts = 0;

        while ($attempts < $this->maxRetries) {
            $attempts++;

            try {
                if (!$this->sourceFilesystem->fileExists($sourcePath)) {
                    throw new Exception("File does not exist: {$sourcePath}");
                }

                // Create a temp file to avoid memory issues with large files
                $tempFile = $this->tempDir . '/' . md5($sourcePath . microtime());
                $stream = $this->sourceFilesystem->readStream($sourcePath);

                $tempHandle = fopen($tempFile, 'w+b');
                if ($tempHandle === false) {
                    throw new Exception("Failed to create temp file");
                }

                // Copy file in chunks to avoid memory issues
                $chunkSize = 1024 * 1024; // 1MB chunks
                while (!feof($stream)) {
                    $chunk = fread($stream, $chunkSize);
                    if ($chunk === false) {
                        break;
                    }
                    fwrite($tempHandle, $chunk);
                }

                if (is_resource($stream)) {
                    fclose($stream);
                }
                fclose($tempHandle);

                // Add to ZIP from temp file
                $result = $zip->addFile($tempFile, $outputFileName);

                if ($result === false) {
                    throw new Exception("Failed to add {$sourcePath} to ZIP");
                }

                // Schedule the temp file for deletion
                register_shutdown_function(fn() => file_exists($tempFile) && @unlink($tempFile));

                $this->addLogEntry("Successfully added: {$outputFileName} (source: {$sourcePath})");
                return true;
            } catch (FilesystemException $e) {
                $this->addLogEntry("Attempt {$attempts}: Failed to process {$sourcePath}: " . $e->getMessage());

                if ($attempts >= $this->maxRetries) {
                    $this->addLogEntry("FAILED after {$attempts} attempts: {$sourcePath} → {$outputFileName}");
                    return false;
                }

                // Wait before retry (exponential backoff)
                usleep(100000 * pow(2, $attempts)); // 0.1s, 0.2s, 0.4s...
            } finally {
                // Clean up any temp file if it exists but wasn't scheduled for deletion
                if (isset($tempFile) && file_exists($tempFile) && !isset($result)) {
                    unlink($tempFile);
                }
            }
        }

        return false;
    }

    private function addLogEntry(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $this->logEntries[] = "[{$timestamp}] {$message}";

        // If log gets too large, write to temporary log file to save memory
        if (count($this->logEntries) > 1000) {
            $tempLogFile = $this->tempDir . '/temp_log_' . md5($this->zipFilePath) . '.txt';
            file_put_contents(
                $tempLogFile,
                implode(PHP_EOL, $this->logEntries) . PHP_EOL,
                FILE_APPEND
            );

            // Clear in-memory log but keep the last entry
            $lastEntry = end($this->logEntries);
            $this->logEntries = [$lastEntry];
        }
    }
}
