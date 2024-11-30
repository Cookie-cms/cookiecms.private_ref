<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *"); // Replace '*' with specific domain if needed
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once $_SERVER['DOCUMENT_ROOT'] . "/src/define.php"; // Ensure this path is correct
// Define file path for configuration
$file_path = $_SERVER['DOCUMENT_ROOT'] . "/configs/config.yml"; // Adjust path if needed
require_once __mysql__;

// Check if it's a preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // // Read the YAML configuration
    $yaml_data = read_yaml($file_path);

    // Validate token
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (empty($yaml_data['debugToken']) || $token !== $yaml_data['debugToken']) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'error' => 403,
            'message' => 'Access denied. Invalid or missing token.',
        ]);
        exit;
    }

    if (isset($_GET['describe'])) {
        $tableName = $_GET['describe'];
        

        // Connect to database (adjust credentials)
    
        // Prepare and execute query securely
        $stmt = $conn->prepare("SELECT * FROM `" . $tableName . "`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Return the result as JSON
        echo json_encode([
            'success' => true,
            'data' => $rows,
        ]);
    }

    if (isset($_GET['showtables'])) {
        // Connect to database (adjust credentials)
        // $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Query to list all tables
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_NUM); // Fetch as numeric array
    
        // Format table names into a JSON array
        $tableNames = array_map(fn($table) => $table[0], $tables);
    
        // Return as JSON
        echo json_encode([
            'success' => true,
            'tables' => $tableNames,
        ]);
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['query'])) {
        $query = $input['query'];
    
        // Connect to database (adjust credentials)
    
        try {
    
            // Prevent dangerous commands: Block DROP, ALTER, TRUNCATE, DELETE TABLE
            $forbiddenCommands = [
                'DROP', 'TRUNCATE', 'ALTER', 'CREATE', 'DELETE TABLE', 'DROP TABLE'
            ];
    
            foreach ($forbiddenCommands as $cmd) {
                if (stripos($query, $cmd) === 0) {
                    echo json_encode(['success' => false, 'error' => 'Forbidden command']);
                    exit;
                }
            }
    
            // Execute the SQL query
            $stmt = $conn->prepare($query);
            $stmt->execute();
    
            // Fetch results if it's a SELECT query
            if (stripos(trim($query), 'SELECT') === 0) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([
                    'success' => true,
                    'data' => $rows,
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Query executed successfully.',
                ]);
            }
        } catch (PDOException $e) {
            // Handle query execution errors
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

} catch (Exception $e) {
    http_response_code(500); // Internal server error
    echo json_encode([
        'error' => 500,
        'message' => $e->getMessage(),
    ]);
}
?>
