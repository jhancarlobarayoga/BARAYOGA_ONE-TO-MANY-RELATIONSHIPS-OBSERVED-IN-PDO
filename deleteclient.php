<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_GET['programmer_id']) || !isset($_GET['client_id'])) {
    header("Location: index.php");
    exit;
}

$programmerID = $_GET['programmer_id'];
$clientID = $_GET['client_id'];
$programmer = getProgrammerByID($pdo, $programmerID);
$client = getClientByID($pdo, $clientID, $programmerID);

if (!$programmer || !$client) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Client</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="index.php"><span class="brand-mark">PC</span><span class="brand-copy"><strong>ProClient</strong><small>Freelance Development Workspace</small></span></a>
        <nav class="site-nav"><a class="nav-link active" href="index.php">Dashboard</a></nav>
    </div>
</header>

<main class="page-shell narrow-shell">
    <section class="dashboard-heading compact-page-heading">
        <div><p class="eyebrow-dark">CHILD ENTITY · DELETE</p><h1>Delete Client</h1><p class="page-subtitle">Confirm whether you want to remove this client record.</p></div>
    </section>
    <p><a class="back-link" href="viewclients.php?programmer_id=<?php echo $programmerID; ?>">← Cancel and return to clients</a></p>

    <section class="panel">
        <div class="panel-heading"><div><h2>Confirm Deletion</h2><p>This action deletes only this client record.</p></div><span class="entity-tag child-tag">DELETE</span></div>
        <dl class="detail-list">
            <dt>Client ID</dt><dd><?php echo $clientID; ?></dd>
            <dt>Full Name</dt><dd><?php echo e($client['first_name'] . ' ' . $client['last_name']); ?></dd>
            <dt>Company</dt><dd><?php echo e($client['company_name'] ?: 'Not provided'); ?></dd>
            <dt>Email Address</dt><dd><?php echo e($client['email_address']); ?></dd>
            <dt>Service Requested</dt><dd><?php echo e($client['service_requested']); ?></dd>
            <dt>Assigned Programmer</dt><dd><?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?></dd>
        </dl>
        <form action="core/handleForms.php?client_id=<?php echo $clientID; ?>&programmer_id=<?php echo $programmerID; ?>" method="POST" class="form-actions delete-form">
            <button class="btn btn-danger" type="submit" name="deleteClientBtn">Yes, Delete Client</button>
            <a class="btn btn-light" href="viewclients.php?programmer_id=<?php echo $programmerID; ?>">Cancel</a>
        </form>
    </section>
</main>
</body>
</html>
