<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_GET['programmer_id'])) {
    header("Location: index.php");
    exit;
}

$programmerID = $_GET['programmer_id'];
$programmer = getProgrammerByID($pdo, $programmerID);

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
    <title>Edit Programmer</title>
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
        <div><p class="eyebrow-dark">PARENT ENTITY · UPDATE</p><h1>Edit Programmer</h1><p class="page-subtitle">Change the programmer's information below.</p></div>
    </section>
    <p><a class="back-link" href="index.php">← Back to Dashboard</a></p>

    <section class="panel">
        <div class="panel-heading"><div><h2>Programmer Information</h2><p>Programmer ID: <?php echo $programmerID; ?></p></div><span class="entity-tag parent-tag">PARENT</span></div>
        <form action="core/handleForms.php?programmer_id=<?php echo $programmerID; ?>" method="POST" class="form-grid single-column">
            <label>Username<input type="text" name="username" value="<?php echo e($programmer['username']); ?>" maxlength="50" required></label>
            <label>First Name<input type="text" name="firstName" value="<?php echo e($programmer['first_name']); ?>" maxlength="50" required></label>
            <label>Last Name<input type="text" name="lastName" value="<?php echo e($programmer['last_name']); ?>" maxlength="50" required></label>
            <label>Date of Birth<input type="date" name="dateOfBirth" value="<?php echo e($programmer['date_of_birth']); ?>"></label>
            <label>Specialization<input type="text" name="specialization" value="<?php echo e($programmer['specialization']); ?>" maxlength="150" required></label>
            <div class="form-actions"><button class="btn btn-primary" type="submit" name="editProgrammerBtn">Save Changes</button><a class="btn btn-light" href="index.php">Cancel</a></div>
        </form>
    </section>
</main>
</body>
</html>
