<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_GET['programmer_id'])) {
    header("Location: index.php");
    exit;
}

$programmerID = $_GET['programmer_id'];
$programmer = getProgrammerByID($pdo, $programmerID);
$clients = getClientsByProgrammer($pdo, $programmerID);

if (!$programmer) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Programmer</title>
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
        <div><p class="eyebrow-dark">PARENT ENTITY · DELETE</p><h1>Delete Programmer</h1><p class="page-subtitle">Confirm whether you want to delete this programmer record.</p></div>
    </section>
    <p><a class="back-link" href="index.php">← Cancel and return</a></p>

    <section class="panel">
        <div class="panel-heading"><div><h2>Confirm Deletion</h2><p>Review the programmer record before deleting it.</p></div><span class="entity-tag child-tag">DELETE</span></div>
        <div class="alert warning">This programmer has <?php echo count($clients); ?> client record(s). Deleting the programmer will also delete those clients.</div>
        <dl class="detail-list">
            <dt>Programmer ID</dt><dd><?php echo $programmerID; ?></dd>
            <dt>Username</dt><dd><?php echo e($programmer['username']); ?></dd>
            <dt>Full Name</dt><dd><?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?></dd>
            <dt>Date of Birth</dt><dd><?php echo e($programmer['date_of_birth'] ?: 'Not provided'); ?></dd>
            <dt>Specialization</dt><dd><?php echo e($programmer['specialization']); ?></dd>
            <dt>Date Added</dt><dd><?php echo e($programmer['date_added']); ?></dd>
        </dl>
        <form action="core/handleForms.php?programmer_id=<?php echo $programmerID; ?>" method="POST" class="form-actions delete-form">
            <button class="btn btn-danger" type="submit" name="deleteProgrammerBtn">Yes, Delete Programmer and Clients</button>
            <a class="btn btn-light" href="index.php">Cancel</a>
        </form>
    </section>
</main>
</body>
</html>
