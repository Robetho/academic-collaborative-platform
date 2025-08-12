<?php
require_once('../config.php'); // Hakikisha path ni sahihi kulingana na muundo wako wa folda

// Set response header to JSON
header('Content-Type: application/json');

$response = [
    'labels' => [],
    'data' => []
];

// Get selected month and year from GET request, default to current month/year if not provided
$selected_month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

if ($selected_month < 1 || $selected_month > 12) {
    $selected_month = date('m'); // Default to current month if invalid
}
if ($selected_year < 1900 || $selected_year > date('Y') + 5) { // Adjust year range as needed
    $selected_year = date('Y'); // Default to current year if invalid
}

// Calculate the first and last day of the selected month
$first_day_of_month = date("Y-m-01 00:00:00", strtotime("$selected_year-$selected_month-01"));
$last_day_of_month = date("Y-m-t 23:59:59", strtotime("$selected_year-$selected_month-01"));


$recent_posts_by_category = $conn->query("
    SELECT
        cl.name as category_name,
        COUNT(pl.id) as post_count
    FROM category_list cl
    INNER JOIN post_list pl ON cl.id = pl.category_id
    WHERE pl.date_created >= '$first_day_of_month'
    AND pl.date_created <= '$last_day_of_month'
    AND pl.status = 1 AND pl.delete_flag = 0
    GROUP BY cl.name
    ORDER BY post_count DESC
");

while ($row = $recent_posts_by_category->fetch_assoc()) {
    $response['labels'][] = $row['category_name'];
    $response['data'][] = $row['post_count'];
}

echo json_encode($response);
$conn->close(); // Funga connection ya database
?>