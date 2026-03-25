<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'models/ActivityLog.php';

echo "<h2>Testing ActivityLog Model Methods</h2>";

$activityLog = new ActivityLog();

// Test 1: Check if we can get connection
echo "<h3>Test 1: Database Connection</h3>";
try {
    $conn = $activityLog->getConnection();
    if ($conn) {
        echo "✓ Database connection successful<br>";
    } else {
        echo "✗ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Direct query to count records
echo "<h3>Test 2: Direct Count Query</h3>";
try {
    $conn = $activityLog->getConnection();
    $query = "SELECT COUNT(*) as total FROM activity_logs";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total records in activity_logs table: " . $result['total'] . "<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Direct query to get all records
echo "<h3>Test 3: Direct Query - All Records</h3>";
try {
    $conn = $activityLog->getConnection();
    $query = "SELECT * FROM activity_logs ORDER BY id DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($records) . " records<br>";
    if (count($records) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>User</th><th>Action</th><th>Description</th><th>Created At</th></tr>";
        foreach ($records as $record) {
            echo "<tr>";
            echo "<td>" . $record['id'] . "</td>";
            echo "<td>" . ($record['username'] ?? 'N/A') . "</td>";
            echo "<td>" . $record['action'] . "</td>";
            echo "<td>" . substr($record['description'] ?? '', 0, 50) . "</td>";
            echo "<td>" . $record['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 4: Test getAllLogs method
echo "<h3>Test 4: getAllLogs() Method</h3>";
try {
    $logs = $activityLog->getAllLogs(10, 0, []);
    echo "getAllLogs returned " . count($logs) . " records<br>";
    
    if (count($logs) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>User</th><th>Action</th><th>Description</th><th>Created At</th></tr>";
        foreach ($logs as $log) {
            echo "<tr>";
            echo "<td>" . $log['id'] . "</td>";
            echo "<td>" . ($log['username'] ?? 'N/A') . "</td>";
            echo "<td>" . $log['action'] . "</td>";
            echo "<td>" . substr($log['description'] ?? '', 0, 50) . "</td>";
            echo "<td>" . $log['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 5: Test countLogs method
echo "<h3>Test 5: countLogs() Method</h3>";
try {
    $total = $activityLog->countLogs([]);
    echo "countLogs returned: " . $total . "<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 6: Check for any PHP errors in the log
echo "<h3>Test 6: Check PHP Error Log</h3>";
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    echo "Error log location: " . $logFile . "<br>";
    $errors = file($logFile);
    $recentErrors = array_slice($errors, -10);
    echo "Recent errors:<br>";
    echo "<pre>";
    foreach ($recentErrors as $error) {
        if (strpos($error, 'ActivityLog') !== false) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
    echo "</pre>";
} else {
    echo "No error log file found or configured.<br>";
}
?>