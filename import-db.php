<?php
/**
 * Import large SQL file (compressed or uncompressed) into Laravel DB
 * Usage: php import-db.php <path-to-file.sql or .sql.gz>
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filePath = $argv[1] ?? null;

if (!$filePath) {
    die("Usage: php import-db.php <path-to-sql-file>\n");
}

if (!file_exists($filePath)) {
    die("File not found: $filePath\n");
}

$isCompressed = substr($filePath, -3) === '.gz';

echo "Importing database from: $filePath\n";
echo "File size: " . round(filesize($filePath) / (1024 * 1024 * 1024), 2) . " GB\n";

$pdo = DB::connection()->getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Open file handle
if ($isCompressed) {
    echo "Decompressing and importing...\n";
    $handle = gzopen($filePath, 'rb');
    if (!$handle) {
        die("Failed to open gzip file\n");
    }
} else {
    echo "Importing SQL file...\n";
    $handle = fopen($filePath, 'r');
    if (!$handle) {
        die("Failed to open SQL file\n");
    }
}

$sql = '';
$lineCount = 0;
$statementCount = 0;
$chunkSize = 1024 * 1024; // 1MB chunks

try {
    while (!($isCompressed ? gzeof($handle) : feof($handle))) {
        $line = $isCompressed ? gzgets($handle) : fgets($handle);
        
        if ($line === false) break;
        
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 1) === '#') {
            continue;
        }
        
        $sql .= $line . "\n";
        $lineCount++;
        
        // Execute when statement is complete (ends with ;)
        if (substr(rtrim($sql), -1) === ';') {
            try {
                $pdo->exec($sql);
                $statementCount++;
                
                if ($statementCount % 100 === 0) {
                    echo "Executed $statementCount statements, processed $lineCount lines...\n";
                }
            } catch (Exception $e) {
                echo "Error executing statement $statementCount: " . $e->getMessage() . "\n";
                echo "SQL: " . substr($sql, 0, 200) . "...\n";
                throw $e;
            }
            $sql = '';
        }
    }
    
    // Execute any remaining SQL
    if (!empty(trim($sql))) {
        $pdo->exec($sql);
        $statementCount++;
    }
    
    $isCompressed ? gzclose($handle) : fclose($handle);
    
    echo "\n✓ Import completed successfully!\n";
    echo "Total statements executed: $statementCount\n";
    echo "Total lines processed: $lineCount\n";
    
} catch (Exception $e) {
    $isCompressed ? gzclose($handle) : fclose($handle);
    echo "\n✗ Import failed: " . $e->getMessage() . "\n";
    exit(1);
}
