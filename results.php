<?php
session_start(); // Start session

// Database connection
$connection = new mysqli("localhost", "root", "", "user_registration");

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Assume logged-in user (Replace with $_SESSION["id"])
$user_id = $_SESSION["id"] ?? 1; 

// Fetch user's answers
$sql = "SELECT ta.question_id, ta.answer, ak.correct_answer 
        FROM test_answers ta
        JOIN answers_key ak ON ta.question_id = ak.question_id
        WHERE ta.user_id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_questions = 0;
$correct_answers = 0;
$incorrect = [];
$correct = [];

while ($row = $result->fetch_assoc()) {
    $total_questions++;

    if (strtolower(trim($row["answer"])) == strtolower(trim($row["correct_answer"]))) {
        $correct_answers++;
        $correct[] = ["question_id" => $row["question_id"], "answer" => $row["answer"]];
    } else {
        $incorrect[] = [
            "question_id" => $row["question_id"],
            "your_answer" => $row["answer"],
            "correct_answer" => $row["correct_answer"]
        ];
    }
}

$score = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Results</title>
</head>
<body>
    <h2>Test Results</h2>
    <p><strong>Score:</strong> <?php echo round($score, 2); ?>%</p>
    <h3>Correct Answers:</h3>
    <ul>
        <?php foreach ($correct as $c) { ?>
            <li>Question ID: <?php echo $c["question_id"]; ?> - Answer: <?php echo $c["answer"]; ?></li>
        <?php } ?>
    </ul>

    <h3>Incorrect Answers:</h3>
    <ul>
        <?php foreach ($incorrect as $inc) { ?>
            <li>Question ID: <?php echo $inc["question_id"]; ?> - 
                Your Answer: <?php echo $inc["your_answer"]; ?> | 
                Correct Answer: <?php echo $inc["correct_answer"]; ?>
            </li>
        <?php } ?>
    </ul>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$stmt->close();
$connection->close();
?>
