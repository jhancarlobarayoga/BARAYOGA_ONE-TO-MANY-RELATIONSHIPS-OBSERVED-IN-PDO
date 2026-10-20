<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

$programmers = getAllProgrammers($pdo);
$totalProgrammers = count($programmers);
$totalClients = 0;
$programmersWithClients = 0;

foreach ($programmers as $programmer) {
    $totalClients = $totalClients + $programmer['client_count'];

    if ($programmer['client_count'] > 0) {
        $programmersWithClients = $programmersWithClients + 1;
    }
}

$message = "";
$messageType = "success";

if (isset($_GET['message'])) {
    if ($_GET['message'] == "programmer_added") {
        $message = "Programmer added successfully.";
    }
    elseif ($_GET['message'] == "programmer_updated") {
        $message = "Programmer information updated successfully.";
    }
    elseif ($_GET['message'] == "programmer_deleted") {
        $message = "Programmer and associated clients deleted successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programmer and Client Management System</title>
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
            <a class="nav-link active" href="index.php">Dashboard</a>
            <a class="nav-link" href="#programmer-records">Programmers</a>
            <a class="nav-link" href="#relationship-guide">Relationship Guide</a>
        </nav>
    </div>
</header>

<main class="page-shell">
    <section class="dashboard-heading">
        <div>
            <p class="eyebrow-dark">BUSINESS OVERVIEW</p>
            <h1>Programmer Dashboard</h1>
            <p class="page-subtitle">Register programmers and manage the clients assigned to each programmer.</p>
        </div>
        <a class="btn btn-primary" href="#add-programmer">+ Add Programmer</a>
    </section>

    <?php if ($message != "") { ?>
        <div class="alert success"><?php echo e($message); ?></div>
    <?php } ?>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-topline">
                <span class="stat-icon icon-blue">P</span>
                <span class="stat-caption">PARENT ENTITY</span>
            </div>
            <p class="stat-label">Registered Programmers</p>
            <div class="stat-bottom">
                <strong class="stat-value"><?php echo $totalProgrammers; ?></strong>
                <span class="stat-note">programmer records</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-topline">
                <span class="stat-icon icon-green">C</span>
                <span class="stat-caption">CHILD ENTITY</span>
            </div>
            <p class="stat-label">Total Client Records</p>
            <div class="stat-bottom">
                <strong class="stat-value"><?php echo $totalClients; ?></strong>
                <span class="stat-note">client records</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-topline">
                <span class="stat-icon icon-violet">1:M</span>
                <span class="stat-caption">RELATIONSHIP</span>
            </div>
            <p class="stat-label">Programmers With Clients</p>
            <div class="stat-bottom">
                <strong class="stat-value"><?php echo $programmersWithClients; ?></strong>
                <span class="stat-note">programmers with assigned clients</span>
            </div>
        </article>
    </section>

    <div class="dashboard-grid">
        <section class="panel" id="add-programmer">
            <div class="panel-heading">
                <div>
                    <span class="section-kicker">CREATE PARENT RECORD</span>
                    <h2>Add a Programmer</h2>
                    <p>Register a programmer before adding their clients.</p>
                </div>
                <span class="entity-tag parent-tag">PARENT</span>
            </div>

            <form action="core/handleForms.php" method="POST" class="form-grid">
                <label>Username
                    <input type="text" name="username" maxlength="50" placeholder="e.g. jcarlo_dev" required>
                </label>
                <label>Specialization
                    <input type="text" name="specialization" maxlength="150" placeholder="e.g. Web Development" required>
                </label>
                <label>First Name
                    <input type="text" name="firstName" maxlength="50" required>
                </label>
                <label>Last Name
                    <input type="text" name="lastName" maxlength="50" required>
                </label>
                <label class="span-full">Date of Birth (Optional)
                    <input type="date" name="dateOfBirth">
                </label>
                <div class="form-actions span-full">
                    <button class="btn btn-primary" type="submit" name="insertProgrammerBtn">Save Programmer</button>
                    <span class="hint">Complete all required fields.</span>
                </div>
            </form>
        </section>

        <section class="panel relationship-panel" id="relationship-guide">
            <div class="panel-heading">
                <div>
                    <span class="section-kicker">DATABASE STRUCTURE</span>
                    <h2>One-to-Many Relationship</h2>
                    <p>One programmer can have many clients.</p>
                </div>
            </div>

            <div class="relationship-content">
                <div class="entity-box parent-entity-box">
                    <span class="entity-symbol">P</span>
                    <div>
                        <strong>Programmer</strong>
                        <small>Parent table · One</small>
                    </div>
                    <span class="entity-count"><?php echo $totalProgrammers; ?></span>
                </div>

                <div class="relationship-connector">
                    <span class="connector-line"></span>
                    <span class="connector-label">one to many</span>
                    <span class="connector-arrow">↓</span>
                </div>

                <div class="entity-box child-entity-box">
                    <span class="entity-symbol">C</span>
                    <div>
                        <strong>Client</strong>
                        <small>Child table · Many</small>
                    </div>
                    <span class="entity-count"><?php echo $totalClients; ?></span>
                </div>

                <div class="foreign-key-note">
                    <span class="key-symbol">FK</span>
                    <div>
                        <strong>Foreign key</strong>
                        <code>client.programmer_id</code>
                        <small>References programmer.programmer_id</small>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="panel records-panel" id="programmer-records">
        <div class="panel-heading records-heading">
            <div>
                <span class="section-kicker">DATABASE RECORDS</span>
                <h2>Programmer Directory</h2>
                <p>View, update, or delete programmer records and access their clients.</p>
            </div>
            <span class="count-badge"><?php echo $totalProgrammers; ?> total</span>
        </div>

        <?php if (count($programmers) == 0) { ?>
            <div class="empty-state">
                <span class="empty-icon">P</span>
                <h3>No programmers registered yet</h3>
                <p>Complete the form above to add your first programmer.</p>
                <a class="btn btn-primary" href="#add-programmer">Register First Programmer</a>
            </div>
        <?php } else { ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Programmer</th>
                            <th>Specialization</th>
                            <th>Clients</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($programmers as $row) { ?>
                        <tr>
                            <td>
                                <div class="person-cell">
                                    <span class="avatar avatar-blue"><?php echo e(strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1))); ?></span>
                                    <div>
                                        <strong><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                                        <small>@<?php echo e($row['username']); ?> · ID #<?php echo $row['programmer_id']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="specialization-pill"><?php echo e($row['specialization']); ?></span></td>
                            <td><span class="client-count-pill"><?php echo $row['client_count']; ?></span></td>
                            <td><?php echo e($row['date_added']); ?></td>
                            <td>
                                <div class="actions-row">
                                    <a class="btn btn-small btn-view" href="viewclients.php?programmer_id=<?php echo $row['programmer_id']; ?>">View Clients</a>
                                    <a class="icon-action" href="editprogrammer.php?programmer_id=<?php echo $row['programmer_id']; ?>">Edit</a>
                                    <a class="icon-action danger-action" href="deleteprogrammer.php?programmer_id=<?php echo $row['programmer_id']; ?>">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>

    <footer class="page-footer">
        <span>ProClient · Programmer and Client Management</span>
        <span>PHP · PDO · MySQL</span>
    </footer>
</main>

</body>
</html>
