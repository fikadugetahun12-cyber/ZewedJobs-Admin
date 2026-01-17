<?php
header('Content-Type: application/json');
require_once '../config/config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => ''];
$usersData = getJsonData(USERS_DB);
$users = $usersData['users'] ?? [];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            foreach ($users as $user) {
                if ($user['id'] === $id) {
                    unset($user['password']);
                    $response['success'] = true;
                    $response['data'] = $user;
                    break;
                }
            }
        } else {
            // Remove passwords from response
            $usersWithoutPasswords = array_map(function($user) {
                unset($user['password']);
                return $user;
            }, $users);
            
            $response['success'] = true;
            $response['data'] = $usersWithoutPasswords;
            $response['total'] = count($users);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $newUser = [
            'id' => generateId(),
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => password_hash($data['password'] ?? 'password123', PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'user',
            'phone' => $data['phone'] ?? '',
            'location' => $data['location'] ?? '',
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $users[] = $newUser;
        $usersData['users'] = $users;
        saveJsonData(USERS_DB, $usersData);
        
        unset($newUser['password']);
        $response['success'] = true;
        $response['message'] = 'User created successfully';
        $response['data'] = $newUser;
        break;
        
    case 'PUT':
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($id) {
            foreach ($users as &$user) {
                if ($user['id'] === $id) {
                    if (isset($data['password']) && !empty($data['password'])) {
                        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                    }
                    $user = array_merge($user, $data);
                    unset($user['password']);
                    
                    $response['success'] = true;
                    $response['message'] = 'User updated successfully';
                    $response['data'] = $user;
                    break;
                }
            }
            
            $usersData['users'] = $users;
            saveJsonData(USERS_DB, $usersData);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Prevent deleting the last admin
            $adminCount = 0;
            foreach ($users as $user) {
                if ($user['role'] === 'admin') {
                    $adminCount++;
                }
            }
            
            $userToDelete = null;
            foreach ($users as $user) {
                if ($user['id'] === $id && $user['role'] === 'admin' && $adminCount <= 1) {
                    $response['message'] = 'Cannot delete the last admin user';
                    echo json_encode($response);
                    exit;
                }
                if ($user['id'] === $id) {
                    $userToDelete = $user;
                }
            }
            
            if ($userToDelete) {
                $users = array_filter($users, function($user) use ($id) {
                    return $user['id'] !== $id;
                });
                
                $usersData['users'] = array_values($users);
                saveJsonData(USERS_DB, $usersData);
                
                $response['success'] = true;
                $response['message'] = 'User deleted successfully';
            }
        }
        break;
}

echo json_encode($response);
?>
