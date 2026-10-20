<?php

require_once __DIR__ . '/dbConfig.php';
require_once __DIR__ . '/models.php';

// PARENT ENTITY: INSERT PROGRAMMER
if (isset($_POST['insertProgrammerBtn'])) {
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $specialization = $_POST['specialization'];

    $query = insertProgrammer($pdo, $username, $firstName, $lastName, $dateOfBirth, $specialization);

    if ($query) {
        header("Location: ../index.php?message=programmer_added");
        exit;
    }
    else {
        echo "Programmer insertion failed.";
    }
}

// PARENT ENTITY: UPDATE PROGRAMMER
if (isset($_POST['editProgrammerBtn'])) {
    $programmerID = $_GET['programmer_id'];
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $specialization = $_POST['specialization'];

    $query = updateProgrammer($pdo, $username, $firstName, $lastName, $dateOfBirth, $specialization, $programmerID);

    if ($query) {
        header("Location: ../index.php?message=programmer_updated");
        exit;
    }
    else {
        echo "Programmer update failed.";
    }
}

// PARENT ENTITY: DELETE PROGRAMMER
if (isset($_POST['deleteProgrammerBtn'])) {
    $programmerID = $_GET['programmer_id'];

    $query = deleteProgrammer($pdo, $programmerID);

    if ($query) {
        header("Location: ../index.php?message=programmer_deleted");
        exit;
    }
    else {
        echo "Programmer deletion failed.";
    }
}

// CHILD ENTITY: INSERT CLIENT
if (isset($_POST['insertClientBtn'])) {
    $programmerID = $_GET['programmer_id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $companyName = $_POST['companyName'];
    $emailAddress = $_POST['emailAddress'];
    $contactNumber = $_POST['contactNumber'];
    $serviceRequested = $_POST['serviceRequested'];
    $projectDetails = $_POST['projectDetails'];

    $query = insertClient($pdo, $firstName, $lastName, $companyName, $emailAddress, $contactNumber, $serviceRequested, $projectDetails, $programmerID);

    if ($query) {
        header("Location: ../viewclients.php?programmer_id=" . $programmerID . "&message=client_added");
        exit;
    }
    else {
        echo "Client insertion failed.";
    }
}

// CHILD ENTITY: UPDATE CLIENT
if (isset($_POST['editClientBtn'])) {
    $programmerID = $_GET['programmer_id'];
    $clientID = $_GET['client_id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $companyName = $_POST['companyName'];
    $emailAddress = $_POST['emailAddress'];
    $contactNumber = $_POST['contactNumber'];
    $serviceRequested = $_POST['serviceRequested'];
    $projectDetails = $_POST['projectDetails'];

    $query = updateClient($pdo, $firstName, $lastName, $companyName, $emailAddress, $contactNumber, $serviceRequested, $projectDetails, $clientID, $programmerID);

    if ($query) {
        header("Location: ../viewclients.php?programmer_id=" . $programmerID . "&message=client_updated");
        exit;
    }
    else {
        echo "Client update failed.";
    }
}

// CHILD ENTITY: DELETE CLIENT
if (isset($_POST['deleteClientBtn'])) {
    $programmerID = $_GET['programmer_id'];
    $clientID = $_GET['client_id'];

    $query = deleteClient($pdo, $clientID, $programmerID);

    if ($query) {
        header("Location: ../viewclients.php?programmer_id=" . $programmerID . "&message=client_deleted");
        exit;
    }
    else {
        echo "Client deletion failed.";
    }
}

?>
