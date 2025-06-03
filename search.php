<?php
include 'config/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$input_search = $data['input_search'];

session_start(); 
$current_user = $_SESSION['username'];

$sql = "SELECT username, foto_profil 
        FROM account 
        WHERE username LIKE '%" . $conn->real_escape_string($input_search) . "%' 
        AND username != '" . $conn->real_escape_string($current_user) . "'";
$result = $conn->query($sql);

$response = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = [
            'username' => $row['username'],
            'foto_profil' => $row['foto_profil']
        ];
    }
}

echo json_encode($response);
?>