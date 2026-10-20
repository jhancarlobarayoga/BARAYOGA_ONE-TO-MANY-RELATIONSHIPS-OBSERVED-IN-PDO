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

$clients = getClientsByProgrammer($pdo, $programmerID);
$message = "";

if (isset($_GET['message'])) {
    if ($_GET['message'] == "client_added") {
        $message = "Client added successfully.";
    }
    elseif ($_GET['message'] == "client_updated") {
        $message = "Client information updated successfully.";
    }
    elseif ($_GET['message'] == "client_deleted") {
        $message = "Client deleted successfully.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="index.php">
            <span class="brand-mark">PC</span>
            <span class="brand-copy">
                <strong>ProClient</strong>
                <small>Freelance Development Workspace</small>
            </span>
        </a>
        <nav class="site-nav">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link active" href="#client-records">Clients</a>
            <a class="nav-link" href="index.php#relationship-guide">Relationship Guide</a>
        </nav>
    </div>
</header>

<main class="page-shell">
    <p class="breadcrumb"><a href="index.php">Dashboard</a> / Client Management</p>

    <section class="dashboard-heading">
        <div>
            <p class="eyebrow-dark">CHILD ENTITY MANAGEMENT</p>
            <h1>Clients for <?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?></h1>
            <p class="page-subtitle">Each client record is connected to this programmer through programmer_id.</p>
        </div>
        <a class="btn btn-light" href="index.php">← Return to Dashboard</a>
    </section>

    <?php if ($message != "") { ?>
        <div class="alert success"><?php echo e($message); ?></div>
    <?php } ?>

    <section class="stats-grid client-stats-grid">
        <article class="stat-card">
            <div class="stat-topline"><span class="stat-icon icon-green">C</span><span class="stat-caption">CHILD RECORDS</span></div>
            <p class="stat-label">Clients Assigned</p>
            <div class="stat-bottom"><strong class="stat-value"><?php echo count($clients); ?></strong><span class="stat-note">clients linked to this programmer</span></div>
        </article>
        <article class="stat-card">
            <div class="stat-topline"><span class="stat-icon icon-blue">P</span><span class="stat-caption">PARENT REFERENCE</span></div>
            <p class="stat-label">Programmer ID</p>
            <div class="stat-bottom"><strong class="stat-value">#<?php echo $programmerID; ?></strong><span class="stat-note">parent entity record</span></div>
        </article>
        <article class="stat-card">
            <div class="stat-topline"><span class="stat-icon icon-violet">S</span><span class="stat-caption">SPECIALIZATION</span></div>
            <p class="stat-label">Programmer's Field</p>
            <div class="stat-bottom"><strong class="stat-specialization"><?php echo e($programmer['specialization']); ?></strong><span class="stat-note">programmer expertise</span></div>
        </article>
    </section>

    <div class="dashboard-grid">
        <section class="panel" id="add-client">
            <div class="panel-heading">
                <div>
                    <span class="section-kicker">CREATE CHILD RECORD</span>
                    <h2>Add a Client</h2>
                    <p>This client will be assigned to <?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?>.</p>
                </div>
                <span class="entity-tag child-tag">CHILD</span>
            </div>

            <form action="core/handleForms.php?programmer_id=<?php echo $programmerID; ?>" method="POST" class="form-grid">
                <label>First Name
                    <input type="text" name="firstName" maxlength="60" required>
                </label>
                <label>Last Name
                    <input type="text" name="lastName" maxlength="60" required>
                </label>
                <label>Company / Business Name (Optional)
                    <input type="text" name="companyName" maxlength="120">
                </label>
                <label>Email Address
                    <input type="email" name="emailAddress" maxlength="150" required>
                </label>
                <label>Contact Number (Optional)
                    <input type="text" name="contactNumber" maxlength="30">
                </label>
                <label>Service Requested
                    <input type="text" name="serviceRequested" maxlength="120" placeholder="e.g. Website Development" required>
                </label>
                <label class="span-full">Project Details (Optional)
                    <textarea name="projectDetails" rows="3"></textarea>
                </label>
                <div class="form-actions span-full">
                    <button class="btn btn-primary" type="submit" name="insertClientBtn">Save Client</button>
                    <span class="hint">You can register many clients for one programmer.</span>
                </div>
            </form>
        </section>

        <section class="panel relationship-panel">
            <div class="panel-heading">
                <div>
                    <span class="section-kicker">RELATIONSHIP DETAILS</span>
                    <h2>Current Assignment</h2>
                    <p>Parent-to-child database connection.</p>
                </div>
            </div>
            <div class="relationship-content">
                <div class="entity-box parent-entity-box">
                    <span class="entity-symbol">P</span>
                    <div><strong><?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?></strong><small>Programmer · Parent</small></div>
                </div>
                <div class="relationship-connector"><span class="connector-line"></span><span class="connector-label">has many</span><span class="connector-arrow">↓</span></div>
                <div class="entity-box child-entity-box">
                    <span class="entity-symbol">C</span>
                    <div><strong><?php echo count($clients); ?> Client Records</strong><small>Client · Child</small></div>
                </div>
                <div class="foreign-key-note">
                    <span class="key-symbol">FK</span>
                    <div><strong>Foreign key field</strong><code>programmer_id = <?php echo $programmerID; ?></code><small>Saved with each client record.</small></div>
                </div>
            </div>
        </section>
    </div>

    <section class="panel records-panel" id="client-records">
        <div class="panel-heading records-heading">
            <div><span class="section-kicker">DATABASE RECORDS</span><h2>Client Directory</h2><p>All clients assigned to this programmer.</p></div>
            <span class="count-badge"><?php echo count($clients); ?> clients</span>
        </div>

        <?php if (count($clients) == 0) { ?>
            <div class="empty-state">
                <span class="empty-icon child-empty-icon">C</span>
                <h3>No clients assigned yet</h3>
                <p>Use the form above to add the first client.</p>
            </div>
        <?php } else { ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Company</th>
                            <th>Contact Details</th>
                            <th>Service Requested</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($clients as $row) { ?>
                        <tr>
                            <td>
                                <div class="person-cell">
                                    <span class="avatar avatar-green"><?php echo e(strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1))); ?></span>
                                    <div><strong><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></strong><small>Client ID #<?php echo $row['client_id']; ?></small></div>
                                </div>
                            </td>
                            <td><?php echo e($row['company_name'] ?: 'Not provided'); ?></td>
                            <td><strong class="contact-email"><?php echo e($row['email_address']); ?></strong><small class="table-subline"><?php echo e($row['contact_number'] ?: 'No phone provided'); ?></small></td>
                            <td><span class="specialization-pill"><?php echo e($row['service_requested']); ?></span></td>
                            <td><?php echo e($row['date_added']); ?></td>
                            <td>
                                <div class="actions-row">
                                    <a class="icon-action" href="editclient.php?client_id=<?php echo $row['client_id']; ?>&programmer_id=<?php echo $programmerID; ?>">Edit</a>
                                    <a class="icon-action danger-action" href="deleteclient.php?client_id=<?php echo $row['client_id']; ?>&programmer_id=<?php echo $programmerID; ?>">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>

    <footer class="page-footer"><span>ProClient · Client Management</span><span>Child table: client</span></footer>
</main>

</body>
</html>
