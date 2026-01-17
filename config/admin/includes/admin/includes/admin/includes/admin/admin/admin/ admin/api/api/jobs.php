<?php
header('Content-Type: application/json');
require_once '../config/config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => ''];
$jobsData = getJsonData(JOBS_DB);
$jobs = $jobsData['jobs'] ?? [];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            foreach ($jobs as $job) {
                if ($job['id'] === $id) {
                    $response['success'] = true;
                    $response['data'] = $job;
                    break;
                }
            }
        } else {
            $response['success'] = true;
            $response['data'] = $jobs;
            $response['total'] = count($jobs);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $newJob = [
            'id' => generateId(),
            'title' => $data['title'] ?? '',
            'company' => $data['company'] ?? '',
            'location' => $data['location'] ?? '',
            'job_type' => $data['job_type'] ?? 'full-time',
            'category' => $data['category'] ?? '',
            'salary' => $data['salary'] ?? '',
            'description' => $data['description'] ?? '',
            'requirements' => $data['requirements'] ?? [],
            'benefits' => $data['benefits'] ?? [],
            'status' => $data['status'] ?? 'active',
            'posted_date' => date('Y-m-d H:i:s'),
            'expiry_date' => $data['expiry_date'] ?? date('Y-m-d H:i:s', strtotime('+30 days')),
            'created_by' => $_SESSION['admin_id']
        ];
        
        $jobs[] = $newJob;
        $jobsData['jobs'] = $jobs;
        saveJsonData(JOBS_DB, $jobsData);
        
        $response['success'] = true;
        $response['message'] = 'Job created successfully';
        $response['data'] = $newJob;
        break;
        
    case 'PUT':
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($id) {
            foreach ($jobs as &$job) {
                if ($job['id'] === $id) {
                    $job = array_merge($job, $data);
                    $response['success'] = true;
                    $response['message'] = 'Job updated successfully';
                    $response['data'] = $job;
                    break;
                }
            }
            
            $jobsData['jobs'] = $jobs;
            saveJsonData(JOBS_DB, $jobsData);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $jobs = array_filter($jobs, function($job) use ($id) {
                return $job['id'] !== $id;
            });
            
            $jobsData['jobs'] = array_values($jobs);
            saveJsonData(JOBS_DB, $jobsData);
            
            $response['success'] = true;
            $response['message'] = 'Job deleted successfully';
        }
        break;
}

echo json_encode($response);
?>
