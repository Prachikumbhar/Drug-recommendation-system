<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "recommendation";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Join feedback.recommendation_name with treatment_recommendations.recommendation and calculate the average
$sql = "SELECT 
            tr.recommendation AS recommendation_name,
            tr.recommendation_type,
            tr.age,
            tr.symptoms,
            tr.disease,
            AVG(f.rating) AS average_rating,
            COUNT(f.id) AS total_feedbacks
        FROM treatment_recommendations tr
        JOIN feedback f
            ON f.recommendation_name = tr.recommendation
        GROUP BY 
            tr.recommendation,
            tr.recommendation_type,
            tr.age,
            tr.symptoms,
            tr.disease";


$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $filePath = __DIR__ . '/average_ratings.csv';
    $file = fopen($filePath, 'w');

    // Write CSV headers
fputcsv($file, [
    'Recommendation Name',
    'Recommendation Type',
    'Age',
    'Symptoms',
    'Disease',
    'Average Rating',
    'Total Feedbacks'
]);

// Write each row of data
while ($row = $result->fetch_assoc()) {
    fputcsv($file, [
        $row['recommendation_name'],
        $row['recommendation_type'],
        $row['age'],
        $row['symptoms'],
        $row['disease'],
        round($row['average_rating'], 2),
        $row['total_feedbacks']
    ]);
}


    fclose($file);
    echo "✅ File 'average_ratings.csv' created successfully in: $filePath";
} else {
    echo "⚠️ No matched feedback and recommendation data found.";
}

$conn->close();
?>
