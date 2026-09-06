<?php
// backend/register.php
// Matches table: students(Student_id PK auto_increment, Full_Name,
// email, College_Name, Location, Event, Password)
// Student_id is auto-generated - NOT collected from the form.
// Password is stored HASHED (requires the Password column to be
// VARCHAR(255) - see database_alter.sql).

require "config.php";

function redirect($status, $msg = "") {
    $query = "status=" . urlencode($status);
    if ($msg !== "") {
        $query .= "&msg=" . urlencode($msg);
    }
    header("Location: ../register.html?" . $query);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("error", "Invalid request.");
}

$full_name    = trim($_POST["full_name"] ?? "");
$email        = trim($_POST["email"] ?? "");
$college_name = trim($_POST["college_name"] ?? "");
$location     = trim($_POST["location"] ?? "");
$event        = trim($_POST["event"] ?? "");
$password     = $_POST["password"] ?? "";

if ($full_name === "" || $email === "" || $college_name === "" ||
    $location === "" || $event === "" || $password === "") {
    redirect("error", "All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect("error", "Please enter a valid email address.");
}

if (strlen($email) > 20 || strlen($full_name) > 20 ||
    strlen($college_name) > 25 || strlen($location) > 20 || strlen($event) > 20) {
    redirect("error", "One or more fields exceed the allowed length.");
}

// Check for duplicate email (email is UNIQUE in the table)
$check = $conn->prepare("SELECT Student_id FROM students WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    redirect("error", "A student with this email is already registered.");
}
$check->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO students (Full_Name, email, College_Name, Location, Event, Password) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $full_name, $email, $college_name, $location, $event, $hashedPassword);

if ($stmt->execute()) {
    $newId = $conn->insert_id; // the auto-generated Student_id
    $stmt->close();
    $conn->close();
    redirect("success", "Registration successful! Your Student ID is " . $newId . " - save it to log in.");
} else {
    $err = $conn->error;
    $stmt->close();
    $conn->close();
    redirect("error", "Registration failed: " . $err);
}
?>
