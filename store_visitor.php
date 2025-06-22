<?php
include 'connection.php';

    // This PHP script should be placed at the end of your HTML file
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Get the JSON input
            $data = json_decode(file_get_contents('php://input'), true);
            $ip = $data['ip'];
    
            // Check if the IP already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE ip_address = ?");
            $stmt->execute([$ip]);
            $count = $stmt->fetchColumn();
    
            if ($count > 0) {
                echo json_encode(['status' => 'error', 'message' => 'IP address already exists.']);
            } else {
                // Prepare the SQL statement for insertion
                $stmt = $pdo->prepare("INSERT INTO visitors (ip_address, city, region, country) VALUES (?, ?, ?, ?)");
                $stmt->execute([$data['ip'], $data['city'], $data['region'], $data['country']]);
                echo json_encode(['status' => 'success', 'message' => 'Visitor details stored successfully.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit; // Stop further execution of the script
    }
    ?>