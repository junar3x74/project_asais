<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$backupFolder = __DIR__ . '/' . $_ENV['BACKUP_FOLDER'];

if (!is_dir($backupFolder)) {
    mkdir($backupFolder, 0755, true);
}

$files = glob($backupFolder . '/*.xlsx');
$latestBackup = 0;
$existingBackupFile = null;

foreach ($files as $file) {
    $fileTime = filemtime($file);
    if ($fileTime > $latestBackup) {
        $latestBackup = $fileTime;
        $existingBackupFile = $file;
    }
}

$oneWeekInSeconds = 7 * 24 * 60 * 60;
$now = time();

if ($now - $latestBackup < $oneWeekInSeconds) {
    echo "Backup already performed within the last week. Updating the existing backup...\n";
    
    $spreadsheet = IOFactory::load($existingBackupFile);
    $sheet = $spreadsheet->getActiveSheet();
    $currentRow = $sheet->getHighestRow() + 1; 
} else {
 
    echo "Creating a new backup...\n";
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $currentRow = 1;
}

try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $data = $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);

        $sheet->setCellValue("A$currentRow", "Table: $table");
        $sheet->getStyle("A$currentRow")->getFont()->setBold(true);
        $currentRow++;

        if (!empty($data)) {
            $columns = array_keys($data[0]);
            $colIndex = 'A';
            foreach ($columns as $column) {
                $sheet->setCellValue("$colIndex$currentRow", $column);
                $colIndex++;
            }
            $currentRow++;

            foreach ($data as $row) {
                $colIndex = 'A';
                foreach ($row as $cell) {
                    $sheet->setCellValue("$colIndex$currentRow", $cell);
                    $colIndex++;
                }
                $currentRow++;
            }
        } else {
            $sheet->setCellValue("A$currentRow", "No data found in $table");
            $currentRow++;
        }
        $currentRow++;
    }
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

$backupFile = $backupFolder . '/backup_' . date('Y-m-d_H-i-s') . '.xlsx';
if ($existingBackupFile) {
 
    $backupFile = $existingBackupFile;
}

$writer = new Xlsx($spreadsheet);
$writer->save($backupFile);

echo "Backup (or update) successful! File: $backupFile\n";

$retentionPeriod = (int)$_ENV['BACKUP_RETENTION_DAYS'] * 24 * 60 * 60;
$files = glob($backupFolder . '/*.xlsx');

foreach ($files as $file) {
    if ($now - filemtime($file) > $retentionPeriod) {
        unlink($file);
        echo "Deleted old backup: $file\n";
    }
}
?>
