<?php

// This function safely displays text inside HTML.
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// PARENT ENTITY: PROGRAMMER

function insertProgrammer($pdo, $username, $first_name, $last_name, $date_of_birth, $specialization) {
    $sql = "INSERT INTO programmer (username, first_name, last_name, date_of_birth, specialization)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$username, $first_name, $last_name, $date_of_birth ?: null, $specialization]);

    if ($executeQuery) {
        return true;
    }

    return false;
}

function updateProgrammer($pdo, $username, $first_name, $last_name, $date_of_birth, $specialization, $programmer_id) {
    $sql = "UPDATE programmer
            SET username = ?,
                first_name = ?,
                last_name = ?,
                date_of_birth = ?,
                specialization = ?
            WHERE programmer_id = ?";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$username, $first_name, $last_name, $date_of_birth ?: null, $specialization, $programmer_id]);

    if ($executeQuery) {
        return true;
    }

    return false;
}

function deleteProgrammer($pdo, $programmer_id) {
    // Delete the child records first because each client uses programmer_id as a foreign key.
    $sql = "DELETE FROM client WHERE programmer_id = ?";
    $stmt = $pdo->prepare($sql);
    $deleteClients = $stmt->execute([$programmer_id]);

    if ($deleteClients) {
        $sql = "DELETE FROM programmer WHERE programmer_id = ?";
        $stmt = $pdo->prepare($sql);
        $deleteProgrammer = $stmt->execute([$programmer_id]);

        if ($deleteProgrammer) {
            return true;
        }
    }

    return false;
}

function getAllProgrammers($pdo) {
    $sql = "SELECT programmer.*, COUNT(client.client_id) AS client_count
            FROM programmer
            LEFT JOIN client ON programmer.programmer_id = client.programmer_id
            GROUP BY programmer.programmer_id
            ORDER BY programmer.programmer_id DESC";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute();

    if ($executeQuery) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return [];
}

function getProgrammerByID($pdo, $programmer_id) {
    $sql = "SELECT * FROM programmer WHERE programmer_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$programmer_id]);

    if ($executeQuery) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return false;
}

// CHILD ENTITY: CLIENT

function insertClient($pdo, $first_name, $last_name, $company_name, $email_address, $contact_number, $service_requested, $project_details, $programmer_id) {
    $sql = "INSERT INTO client
            (first_name, last_name, company_name, email_address, contact_number, service_requested, project_details, programmer_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$first_name, $last_name, $company_name, $email_address, $contact_number, $service_requested, $project_details, $programmer_id]);

    if ($executeQuery) {
        return true;
    }

    return false;
}

function updateClient($pdo, $first_name, $last_name, $company_name, $email_address, $contact_number, $service_requested, $project_details, $client_id, $programmer_id) {
    $sql = "UPDATE client
            SET first_name = ?,
                last_name = ?,
                company_name = ?,
                email_address = ?,
                contact_number = ?,
                service_requested = ?,
                project_details = ?
            WHERE client_id = ? AND programmer_id = ?";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$first_name, $last_name, $company_name, $email_address, $contact_number, $service_requested, $project_details, $client_id, $programmer_id]);

    if ($executeQuery) {
        return true;
    }

    return false;
}

function deleteClient($pdo, $client_id, $programmer_id) {
    $sql = "DELETE FROM client WHERE client_id = ? AND programmer_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$client_id, $programmer_id]);

    if ($executeQuery) {
        return true;
    }

    return false;
}

function getClientsByProgrammer($pdo, $programmer_id) {
    $sql = "SELECT * FROM client WHERE programmer_id = ? ORDER BY client_id DESC";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$programmer_id]);

    if ($executeQuery) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return [];
}

function getClientByID($pdo, $client_id, $programmer_id) {
    $sql = "SELECT * FROM client WHERE client_id = ? AND programmer_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$client_id, $programmer_id]);

    if ($executeQuery) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return false;
}

?>
