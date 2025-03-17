<?php
session_start(); // Start session

// Database connection
$connection = new mysqli("localhost", "root", "", "user_registration");

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id']; // In real applications, get this from $_SESSION["id"]
    $question_ids = $_POST['question_id'];
    $answers = $_POST['answer'];

    // Check if data is valid
    if (empty($user_id) || empty($question_ids) || empty($answers)) {
        die("Invalid data submission.");
    }

    // Prepare SQL statement
    $sql = "INSERT INTO test_answers (user_id, question_id, answer) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($sql);

    foreach ($question_ids as $index => $question_id) {
        $answer = $answers[$index];
        $stmt->bind_param("iis", $user_id, $question_id, $answer);
        $stmt->execute();
    }

    $stmt->close();
    // echo "Test submitted successfully!";
    header("Location: results.php"); // Redirect to results page    

}

$connection->close();
?>
