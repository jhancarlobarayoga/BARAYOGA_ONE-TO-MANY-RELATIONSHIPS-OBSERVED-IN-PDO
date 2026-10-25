<?php
require_once 'core/dbConfig.php';

// Escape text before showing it in the page.
function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$message = '';
$messageType = 'success';

// Handle form submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // Add a programmer.
        if ($action === 'add_programmer') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');

            if ($firstName === '' || $lastName === '' || $specialization === '') {
                $message = 'Please complete all programmer fields.';
                $messageType = 'error';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO programmer (first_name, last_name, specialization)
                     VALUES (?, ?, ?)'
                );
                $stmt->execute([$firstName, $lastName, $specialization]);
                header('Location: index.php?message=programmer_added');
                exit;
            }
        }

        // Update a programmer.
        if ($action === 'update_programmer') {
            $programmerId = (int)($_POST['programmer_id'] ?? 0);
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');

            if ($programmerId < 1 || $firstName === '' || $lastName === '' || $specialization === '') {
                $message = 'Please complete all programmer fields.';
                $messageType = 'error';
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE programmer
                     SET first_name = ?, last_name = ?, specialization = ?
                     WHERE programmer_id = ?'
                );
                $stmt->execute([$firstName, $lastName, $specialization, $programmerId]);
                header('Location: index.php?message=programmer_updated');
                exit;
            }
        }

        // Delete a programmer only if no clients are assigned to them.
        if ($action === 'delete_programmer') {
            $programmerId = (int)($_POST['programmer_id'] ?? 0);

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM client WHERE programmer_id = ?');
            $stmt->execute([$programmerId]);
            $clientCount = (int)$stmt->fetchColumn();

            if ($clientCount > 0) {
                header('Location: index.php?message=programmer_has_clients');
                exit;
            }

            $stmt = $pdo->prepare('DELETE FROM programmer WHERE programmer_id = ?');
            $stmt->execute([$programmerId]);
            header('Location: index.php?message=programmer_deleted');
            exit;
        }

        // Add a client to the selected programmer.
        if ($action === 'add_client') {
            $programmerId = (int)($_POST['programmer_id'] ?? 0);
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email_address'] ?? '');
            $service = trim($_POST['service_requested'] ?? '');

            if ($programmerId < 1 || $firstName === '' || $lastName === '' || $service === '') {
                $message = 'Please complete the required client fields.';
                $messageType = 'error';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO client
                     (first_name, last_name, email_address, service_requested, programmer_id)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([$firstName, $lastName, $email, $service, $programmerId]);
                header('Location: index.php?programmer_id=' . $programmerId . '&message=client_added');
                exit;
            }
        }

        // Update a client. The assigned programmer stays the same.
        if ($action === 'update_client') {
            $clientId = (int)($_POST['client_id'] ?? 0);
            $programmerId = (int)($_POST['programmer_id'] ?? 0);
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email_address'] ?? '');
            $service = trim($_POST['service_requested'] ?? '');

            if ($clientId < 1 || $programmerId < 1 || $firstName === '' || $lastName === '' || $service === '') {
                $message = 'Please complete the required client fields.';
                $messageType = 'error';
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE client
                     SET first_name = ?, last_name = ?, email_address = ?, service_requested = ?
                     WHERE client_id = ? AND programmer_id = ?'
                );
                $stmt->execute([$firstName, $lastName, $email, $service, $clientId, $programmerId]);
                header('Location: index.php?programmer_id=' . $programmerId . '&message=client_updated');
                exit;
            }
        }

        // Delete one client.
        if ($action === 'delete_client') {
            $clientId = (int)($_POST['client_id'] ?? 0);
            $programmerId = (int)($_POST['programmer_id'] ?? 0);

            $stmt = $pdo->prepare(
                'DELETE FROM client WHERE client_id = ? AND programmer_id = ?'
            );
            $stmt->execute([$clientId, $programmerId]);
            header('Location: index.php?programmer_id=' . $programmerId . '&message=client_deleted');
            exit;
        }
    } catch (PDOException $e) {
        $message = 'The operation could not be completed. Please check the information and try again.';
        $messageType = 'error';
    }
}

// Display messages after an operation.
$messages = [
    'programmer_added' => 'Programmer added successfully.',
    'programmer_updated' => 'Programmer updated successfully.',
    'programmer_deleted' => 'Programmer deleted successfully.',
    'programmer_has_clients' => 'This programmer still has clients. Delete their clients first.',
    'client_added' => 'Client added successfully.',
    'client_updated' => 'Client updated successfully.',
    'client_deleted' => 'Client deleted successfully.'
];

if (isset($_GET['message']) && isset($messages[$_GET['message']])) {
    $message = $messages[$_GET['message']];
    $messageType = ($_GET['message'] === 'programmer_has_clients') ? 'error' : 'success';
}

// Load programmers for the main table.
$programmers = $pdo->query(
    'SELECT * FROM programmer ORDER BY programmer_id DESC'
)->fetchAll();

// Load a programmer for editing, if requested.
$editProgrammer = null;
if (isset($_GET['edit_programmer'])) {
    $stmt = $pdo->prepare('SELECT * FROM programmer WHERE programmer_id = ?');
    $stmt->execute([(int)$_GET['edit_programmer']]);
    $editProgrammer = $stmt->fetch();
}

// Load a client for editing, if requested.
$editClient = null;
if (isset($_GET['edit_client'])) {
    $stmt = $pdo->prepare('SELECT * FROM client WHERE client_id = ?');
    $stmt->execute([(int)$_GET['edit_client']]);
    $editClient = $stmt->fetch();
}

// Choose the programmer whose clients should be displayed.
$selectedProgrammerId = (int)($_GET['programmer_id'] ?? 0);
if ($editClient) {
    $selectedProgrammerId = (int)$editClient['programmer_id'];
}

$selectedProgrammer = null;
$clients = [];
if ($selectedProgrammerId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM programmer WHERE programmer_id = ?');
    $stmt->execute([$selectedProgrammerId]);
    $selectedProgrammer = $stmt->fetch();

    if ($selectedProgrammer) {
        $stmt = $pdo->prepare(
            'SELECT * FROM client WHERE programmer_id = ? ORDER BY client_id DESC'
        );
        $stmt->execute([$selectedProgrammerId]);
        $clients = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programmer and Client Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Programmer and Client Management</h1>
        <p>A simple PHP and PDO example of a one-to-many relationship.</p>
    </header>

    <main class="container">
        <?php if ($message !== '') { ?>
            <div class="message <?php echo e($messageType); ?>"><?php echo e($message); ?></div>
        <?php } ?>

        <section class="panel">
            <h2><?php echo $editProgrammer ? 'Edit Programmer' : 'Add Programmer'; ?></h2>
            <form method="post" action="index.php<?php echo $editProgrammer ? '?edit_programmer=' . e($editProgrammer['programmer_id']) : ''; ?>">
                <input type="hidden" name="action" value="<?php echo $editProgrammer ? 'update_programmer' : 'add_programmer'; ?>">
                <?php if ($editProgrammer) { ?>
                    <input type="hidden" name="programmer_id" value="<?php echo e($editProgrammer['programmer_id']); ?>">
                <?php } ?>

                <label for="programmer_first_name">First Name *</label>
                <input id="programmer_first_name" type="text" name="first_name" required value="<?php echo e($editProgrammer['first_name'] ?? ''); ?>">

                <label for="programmer_last_name">Last Name *</label>
                <input id="programmer_last_name" type="text" name="last_name" required value="<?php echo e($editProgrammer['last_name'] ?? ''); ?>">

                <label for="specialization">Specialization *</label>
                <input id="specialization" type="text" name="specialization" required value="<?php echo e($editProgrammer['specialization'] ?? ''); ?>" placeholder="Example: Web Development">

                <button type="submit"><?php echo $editProgrammer ? 'Update Programmer' : 'Add Programmer'; ?></button>
                <?php if ($editProgrammer) { ?>
                    <a class="cancel-link" href="index.php">Cancel</a>
                <?php } ?>
            </form>
        </section>

        <section class="panel">
            <h2>Programmer List</h2>
            <?php if (count($programmers) === 0) { ?>
                <p>No programmers have been added yet.</p>
            <?php } else { ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($programmers as $programmer) { ?>
                                <tr>
                                    <td><?php echo e($programmer['programmer_id']); ?></td>
                                    <td><?php echo e($programmer['first_name'] . ' ' . $programmer['last_name']); ?></td>
                                    <td><?php echo e($programmer['specialization']); ?></td>
                                    <td class="actions">
                                        <a href="index.php?programmer_id=<?php echo e($programmer['programmer_id']); ?>">View Clients</a>
                                        <a href="index.php?edit_programmer=<?php echo e($programmer['programmer_id']); ?>">Edit</a>
                                        <form method="post" action="index.php" onsubmit="return confirm('Delete this programmer?');">
                                            <input type="hidden" name="action" value="delete_programmer">
                                            <input type="hidden" name="programmer_id" value="<?php echo e($programmer['programmer_id']); ?>">
                                            <button class="delete-button" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </section>

        <?php if ($selectedProgrammer) { ?>
            <section class="panel">
                <h2>Clients of <?php echo e($selectedProgrammer['first_name'] . ' ' . $selectedProgrammer['last_name']); ?></h2>
                <p class="small-note">One programmer can have many clients. Each client below is linked using programmer_id.</p>

                <h3><?php echo $editClient ? 'Edit Client' : 'Add Client'; ?></h3>
                <form method="post" action="index.php?programmer_id=<?php echo e($selectedProgrammerId); ?><?php echo $editClient ? '&edit_client=' . e($editClient['client_id']) : ''; ?>">
                    <input type="hidden" name="action" value="<?php echo $editClient ? 'update_client' : 'add_client'; ?>">
                    <input type="hidden" name="programmer_id" value="<?php echo e($selectedProgrammerId); ?>">
                    <?php if ($editClient) { ?>
                        <input type="hidden" name="client_id" value="<?php echo e($editClient['client_id']); ?>">
                    <?php } ?>

                    <label for="client_first_name">First Name *</label>
                    <input id="client_first_name" type="text" name="first_name" required value="<?php echo e($editClient['first_name'] ?? ''); ?>">

                    <label for="client_last_name">Last Name *</label>
                    <input id="client_last_name" type="text" name="last_name" required value="<?php echo e($editClient['last_name'] ?? ''); ?>">

                    <label for="email_address">Email Address</label>
                    <input id="email_address" type="email" name="email_address" value="<?php echo e($editClient['email_address'] ?? ''); ?>">

                    <label for="service_requested">Service Requested *</label>
                    <input id="service_requested" type="text" name="service_requested" required value="<?php echo e($editClient['service_requested'] ?? ''); ?>" placeholder="Example: Website Development">

                    <button type="submit"><?php echo $editClient ? 'Update Client' : 'Add Client'; ?></button>
                    <?php if ($editClient) { ?>
                        <a class="cancel-link" href="index.php?programmer_id=<?php echo e($selectedProgrammerId); ?>">Cancel</a>
                    <?php } ?>
                </form>

                <h3>Client List</h3>
                <?php if (count($clients) === 0) { ?>
                    <p>No clients are assigned to this programmer yet.</p>
                <?php } else { ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Service</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients as $client) { ?>
                                    <tr>
                                        <td><?php echo e($client['client_id']); ?></td>
                                        <td><?php echo e($client['first_name'] . ' ' . $client['last_name']); ?></td>
                                        <td><?php echo e($client['email_address']); ?></td>
                                        <td><?php echo e($client['service_requested']); ?></td>
                                        <td class="actions">
                                            <a href="index.php?programmer_id=<?php echo e($selectedProgrammerId); ?>&edit_client=<?php echo e($client['client_id']); ?>">Edit</a>
                                            <form method="post" action="index.php?programmer_id=<?php echo e($selectedProgrammerId); ?>" onsubmit="return confirm('Delete this client?');">
                                                <input type="hidden" name="action" value="delete_client">
                                                <input type="hidden" name="programmer_id" value="<?php echo e($selectedProgrammerId); ?>">
                                                <input type="hidden" name="client_id" value="<?php echo e($client['client_id']); ?>">
                                                <button class="delete-button" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </section>
        <?php } ?>

        <section class="panel relationship">
            <h2>Relationship Summary</h2>
            <p><strong>Parent table:</strong> Programmer</p>
            <p><strong>Child table:</strong> Client</p>
            <p><strong>Foreign key:</strong> client.programmer_id references programmer.programmer_id</p>
            <p>One programmer can be linked to many clients. A client record is assigned to one programmer.</p>
        </section>
    </main>
</body>
</html>
