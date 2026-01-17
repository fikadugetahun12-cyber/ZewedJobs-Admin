<?php
header('Content-Type: application/json');
require_once '../config/config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => ''];
$companiesData = getJsonData(COMPANIES_DB);
$companies = $companiesData['companies'] ?? [];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            foreach ($companies as $company) {
                if ($company['id'] === $id) {
                    $response['success'] = true;
                    $response['data'] = $company;
                    break;
                }
            }
        } else {
            $response['success'] = true;
            $response['data'] = $companies;
            $response['total'] = count($companies);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $newCompany = [
            'id' => generate
