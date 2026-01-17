<?php
require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$page_title = 'Manage Jobs';
require_once 'includes/header.php';

$jobsData = getJsonData(JOBS_DB);
$jobs = $jobsData['jobs'] ?? [];
$companies = getJsonData(COMPANIES_DB)['companies'] ?? [];

// Handle job deletion
if (isset($_GET['delete'])) {
    $jobId = $_GET['delete'];
    $jobs = array_filter($jobs, function($job) use ($jobId) {
        return $job['id'] !== $jobId;
    });
    $jobsData['jobs'] = array_values($jobs);
    saveJsonData(JOBS_DB, $jobsData);
    redirect('jobs.php?success=Job deleted successfully');
}

// Handle status update
if (isset($_GET['toggle_status'])) {
    $jobId = $_GET['toggle_status'];
    foreach ($jobs as &$job) {
        if ($job['id'] === $jobId) {
            $job['status'] = $job['status'] === 'active' ? 'inactive' : 'active';
            break;
        }
    }
    $jobsData['jobs'] = $jobs;
    saveJsonData(JOBS_DB, $jobsData);
    redirect('jobs.php?success=Status updated successfully');
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Manage Jobs</h4>
        <a href="add-job.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Job
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Posted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo substr($job['id'], 0, 8); ?>...</td>
                            <td>
                                <strong><?php echo htmlspecialchars($job['title']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($job['category']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($job['company']); ?></td>
                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?php echo ucfirst($job['job_type']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($job['salary'])): ?>
                                $<?php echo number_format($job['salary']); ?>/yr
                                <?php else: ?>
                                <span class="text-muted">Not specified</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $job['status'] === 'active' ? 'badge-active' : 'badge-pending'; ?>">
                                    <?php echo ucfirst($job['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($job['posted_date'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?toggle_status=<?php echo $job['id']; ?>" 
                                       class="btn btn-outline-<?php echo $job['status'] === 'active' ? 'warning' : 'success'; ?>"
                                       onclick="return confirm('Change status to <?php echo $job['status'] === 'active' ? 'inactive' : 'active'; ?>?')">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    <a href="?delete=<?php echo $job['id']; ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirmDelete('job')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
