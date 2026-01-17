<?php
header('Content-Type: application/json');
require_once '../config/config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $data = json_decode(file_get_contents('php://input'), true);
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            $users = getJsonData(USERS_DB)['users'] ?? [];
            
            foreach ($users as $user) {
                if ($user['email'] === $email && password_verify($password, $user['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_name'] = $user['name'];
                    $_SESSION['admin_email'] = $user['email'];
                    
                    $response['success'] = true;
                    $response['message'] = 'Login successful';
                    $response['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    break;
                }
            }
            
            if (!$response['success']) {
                $response['message'] = 'Invalid credentials';
            }
            break;
            
        case 'logout':
            session_destroy();
            $response['success'] = true;
            $response['message'] = 'Logged out successfully';
            break;
            
        case 'check_session':
            $response['success'] = isLoggedIn();
            $response['message'] = isLoggedIn() ? 'Session active' : 'Session expired';
            if (isLoggedIn()) {
                $response['user'] = [
                    'id' => $_SESSION['admin_id'],
                    'name' => $_SESSION['admin_name'],
                    'email' => $_SESSION['admin_email']
                ];
            }
            break;
    }
}

echo json_encode($response);
?>
