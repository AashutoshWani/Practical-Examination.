<?php
// backend/login.php
// Matches table: students(Student_id, Full_Name, email, College_Name,
// Location, Event, Password). Login accepts email OR Student_id.

require "config.php";

function redirect($status, $name = "") {
    $query = "status=" . urlencode($status);
    if ($name !== "") {
        $query .= "&name=" . urlencode($name);
    }
    header("Location: ../login.html?" . $query);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("error");
}

$identifier = trim($_POST["identifier"] ?? ""); // email or Student_id
$password   = $_POST["password"] ?? "";

if ($identifier === "" || $password === "") {
    redirect("error");
}

$stmt = $conn->prepare("SELECT Full_Name, Password FROM students WHERE email = ? OR Student_id = ?");
$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row["Password"])) {
        $_SESSION["student"] = $row["Full_Name"];
        $stmt->close();
        $conn->close();
        redirect("success", $row["Full_Name"]);
    }
}

$stmt->close();
$conn->close();
redirect("error");
?>
