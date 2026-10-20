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
    <title>Edit Client</title>
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
        <div><p class="eyebrow-dark">CHILD ENTITY · UPDATE</p><h1>Edit Client</h1><p class="page-subtitle">Update client details assigned to <?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?>.</p></div>
    </section>
    <p><a class="back-link" href="viewclients.php?programmer_id=<?php echo $programmerID; ?>">← Back to Client Records</a></p>

    <section class="panel">
        <div class="panel-heading"><div><h2>Client Information</h2><p>Client ID: <?php echo $clientID; ?></p></div><span class="entity-tag child-tag">CHILD</span></div>
        <form action="core/handleForms.php?client_id=<?php echo $clientID; ?>&programmer_id=<?php echo $programmerID; ?>" method="POST" class="form-grid single-column">
            <label>First Name<input type="text" name="firstName" value="<?php echo e($client['first_name']); ?>" maxlength="60" required></label>
            <label>Last Name<input type="text" name="lastName" value="<?php echo e($client['last_name']); ?>" maxlength="60" required></label>
            <label>Company / Business Name<input type="text" name="companyName" value="<?php echo e($client['company_name']); ?>" maxlength="120"></label>
            <label>Email Address<input type="email" name="emailAddress" value="<?php echo e($client['email_address']); ?>" maxlength="150" required></label>
            <label>Contact Number<input type="text" name="contactNumber" value="<?php echo e($client['contact_number']); ?>" maxlength="30"></label>
            <label>Service Requested<input type="text" name="serviceRequested" value="<?php echo e($client['service_requested']); ?>" maxlength="120" required></label>
            <label>Project Details<textarea name="projectDetails" rows="4"><?php echo e($client['project_details']); ?></textarea></label>
            <div class="form-actions"><button class="btn btn-primary" type="submit" name="editClientBtn">Save Changes</button><a class="btn btn-light" href="viewclients.php?programmer_id=<?php echo $programmerID; ?>">Cancel</a></div>
        </form>
    </section>
</main>
</body>
</html>
