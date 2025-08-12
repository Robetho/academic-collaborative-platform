

<h1>Welcome, <?php echo $_settings->userdata('firstname')." ".$_settings->userdata('lastname') ?>!</h1>
<hr>
<div class="row">
    <div class="col-12 col-sm-3 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-th-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Category List</span>
                <span class="info-box-number">
                    <?php
                      $category = $conn->query("SELECT * FROM category_list where delete_flag = 0 and `status` = 1")->num_rows;
                      echo format_num($category);
                    ?>
                </span>
            </div>
            </div>
        </div>
    <div class="col-12 col-sm-3 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-light border elevation-1"><i class="fas fa-users-cog"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Registered Users</span>
                <span class="info-box-number">
                    <?php
                      $user = $conn->query("SELECT * FROM users where `type` = 2 ")->num_rows; // Assuming type 2 is for regular users/students
                      echo format_num($user);
                    ?>
                </span>
            </div>
            </div>
        </div>
    <div class="col-12 col-sm-3 col-md-2">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Registered Faculty</span>
                <span class="info-box-number">
                    <?php
                      $faculty = $conn->query("SELECT * FROM faculty")->num_rows;
                      echo format_num($faculty);
                    ?>
                </span>
            </div>
            </div>
        </div>
    <div class="col-12 col-sm-3 col-md-2">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-blog"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Published Post</span>
                <span class="info-box-number">
                    <?php
                      $published_posts = $conn->query("SELECT * FROM post_list where `status` = 1 and delete_flag = 0 ")->num_rows;
                      echo format_num($published_posts);
                    ?>
                </span>
            </div>
            </div>
        </div>
    <div class="col-12 col-sm-3 col-md-2">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-blog"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Unpublished Post</span>
                <span class="info-box-number">
                    <?php
                      $unpublished_posts = $conn->query("SELECT * FROM post_list where `status` = 0 and delete_flag = 0 ")->num_rows;
                      echo format_num($unpublished_posts);
                    ?>
                </span>
            </div>
            </div>
        </div>
</div>

<hr>
<h2>Student Activity Report</h2>
<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-comments"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Comments</span>
                <span class="info-box-number">
                    <?php
                      $total_comments = $conn->query("SELECT * FROM comment_list")->num_rows;
                      echo format_num($total_comments);
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Active Posts</span>
                <span class="info-box-number">
                    <?php
                      // Total posts (published) that have at least one comment
                      $active_posts = $conn->query("SELECT DISTINCT p.id FROM post_list p INNER JOIN comment_list c ON p.id = c.post_id WHERE p.status = 1 AND p.delete_flag = 0")->num_rows;
                      echo format_num($active_posts);
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Students with Posts/Comments</span>
                <span class="info-box-number">
                    <?php
                      // Count distinct users who have either posted or commented
                      $active_users = $conn->query("SELECT DISTINCT user_id FROM post_list WHERE delete_flag = 0 UNION SELECT DISTINCT user_id FROM comment_list")->num_rows;
                      echo format_num($active_users);
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 col-md-6">
        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title">Top 5 Student Contributors (by Posts + Comments)</h3>
            </div>
            <div class="card-body">
                <?php
                // Get top 5 contributors by combining post and comment counts
                $top_contributors = $conn->query("
                    SELECT
                        u.firstname,
                        u.lastname,
                        COUNT(DISTINCT p.id) as total_posts,
                        COUNT(DISTINCT c.id) as total_comments,
                        (COUNT(DISTINCT p.id) + COUNT(DISTINCT c.id)) as activity_score
                    FROM users u
                    LEFT JOIN post_list p ON u.id = p.user_id AND p.delete_flag = 0
                    LEFT JOIN comment_list c ON u.id = c.user_id
                    WHERE u.type = 2 -- Assuming type 2 is for regular users/students
                    GROUP BY u.id
                    ORDER BY activity_score DESC
                    LIMIT 5
                ");
                ?>
                <?php if ($top_contributors->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Total Posts</th>
                                <th>Total Comments</th>
                                <th>Activity Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while($row = $top_contributors->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $row['firstname'] . " " . $row['lastname'] ?></td>
                                    <td><?= $row['total_posts'] ?></td>
                                    <td><?= $row['total_comments'] ?></td>
                                    <td><?= $row['activity_score'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No student activity to display yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card card-outline card-info shadow">
            <div class="card-header">
                <h3 class="card-title">Category Activity Breakdown</h3>
            </div>
            <div class="card-body">
                <?php
                // Get activity per category (posts + comments in those posts)
                $category_activity = $conn->query("
                    SELECT
                        cl.name as category_name,
                        COUNT(DISTINCT pl.id) as total_posts_in_category,
                        COUNT(DISTINCT cm.id) as total_comments_in_category,
                        (COUNT(DISTINCT pl.id) + COUNT(DISTINCT cm.id)) as category_activity_score
                    FROM category_list cl
                    LEFT JOIN post_list pl ON cl.id = pl.category_id AND pl.delete_flag = 0 AND pl.status = 1
                    LEFT JOIN comment_list cm ON pl.id = cm.post_id
                    WHERE cl.delete_flag = 0 AND cl.status = 1
                    GROUP BY cl.id
                    ORDER BY category_activity_score DESC
                ");
                ?>
                <?php if ($category_activity->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Total Posts</th>
                                <th>Total Comments</th>
                                <th>Activity Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while($row = $category_activity->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $row['category_name'] ?></td>
                                    <td><?= $row['total_posts_in_category'] ?></td>
                                    <td><?= $row['total_comments_in_category'] ?></td>
                                    <td><?= $row['category_activity_score'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No category activity to display yet or no active categories.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card card-outline card-warning shadow">
            <div class="card-header">
                <h3 class="card-title">Top 5 Most Commented Posts</h3>
            </div>
            <div class="card-body">
                <?php
                // Query to get top 5 posts with most comments
                $most_commented_posts = $conn->query("
                    SELECT
                        pl.title,
                        COUNT(cl.id) as comment_count
                    FROM post_list pl
                    INNER JOIN comment_list cl ON pl.id = cl.post_id
                    WHERE pl.status = 1 AND pl.delete_flag = 0
                    GROUP BY pl.id, pl.title
                    ORDER BY comment_count DESC
                    LIMIT 5
                ");
                ?>
                <?php if ($most_commented_posts->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Post Title</th>
                                <th>Total Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while($row = $most_commented_posts->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $row['title'] ?></td>
                                    <td><?= $row['comment_count'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No posts with comments to display yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card card-outline card-danger shadow">
            <div class="card-header">
                <h3 class="card-title">Top 5 Most Active Faculty/Admin (by Posts + Comments)</h3>
            </div>
            <div class="card-body">
                <?php
                // Query to get top 5 active faculty/admin members
                // Assuming 'type' 1 for Admin and 'type' 3 for Faculty in 'users' table
                $top_faculty_admin_contributors = $conn->query("
                    SELECT
                        u.firstname,
                        u.lastname,
                        COUNT(DISTINCT p.id) as total_posts,
                        COUNT(DISTINCT c.id) as total_comments,
                        (COUNT(DISTINCT p.id) + COUNT(DISTINCT c.id)) as activity_score
                    FROM users u
                    LEFT JOIN post_list p ON u.id = p.user_id AND p.delete_flag = 0
                    LEFT JOIN comment_list c ON u.id = c.user_id
                    WHERE u.type IN (1, 3) -- Adjust user types as per your database
                    GROUP BY u.id
                    ORDER BY activity_score DESC
                    LIMIT 5
                ");
                ?>
                <?php if ($top_faculty_admin_contributors->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Total Posts</th>
                                <th>Total Comments</th>
                                <th>Activity Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while($row = $top_faculty_admin_contributors->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $row['firstname'] . " " . $row['lastname'] ?></td>
                                    <td><?= $row['total_posts'] ?></td>
                                    <td><?= $row['total_comments'] ?></td>
                                    <td><?= $row['activity_score'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No faculty/admin activity to display yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card card-outline card-success shadow">
            <div class="card-header">
                <h3 class="card-title">All-Time Posts by Category</h3>
            </div>
            <div class="card-body">
                <div style="width: 100%; height: 400px;">
                    <canvas id="postsByCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// PHP data for the chart - NOW FOR ALL TIME
$chart_labels = [];
$chart_data = [];

$all_time_posts_by_category = $conn->query("
    SELECT
        cl.name as category_name,
        COUNT(pl.id) as post_count
    FROM category_list cl
    INNER JOIN post_list pl ON cl.id = pl.category_id
    WHERE pl.status = 1 AND pl.delete_flag = 0
    GROUP BY cl.name
    ORDER BY post_count DESC
");

while ($row = $all_time_posts_by_category->fetch_assoc()) {
    $chart_labels[] = $row['category_name'];
    $chart_data[] = $row['post_count'];
}

// Encode PHP arrays to JSON for JavaScript
$json_chart_labels = json_encode($chart_labels);
$json_chart_data = json_encode($chart_data);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('postsByCategoryChart').getContext('2d');

    // Decode PHP variables from JSON
    var labels = <?php echo $json_chart_labels; ?>;
    var data = <?php echo $json_chart_data; ?>;

    var postsByCategoryChart = new Chart(ctx, {
        type: 'bar', // Type of chart (bar, line, pie, etc.)
        data: {
            labels: labels, // Category names
            datasets: [{
                label: 'Number of Posts',
                data: data, // Number of posts per category
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)',
                    'rgba(83, 102, 255, 0.7)',
                    'rgba(40, 159, 64, 0.7)',
                    'rgba(210, 99, 132, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                    'rgba(83, 102, 255, 1)',
                    'rgba(40, 159, 64, 1)',
                    'rgba(210, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Posts'
                    },
                    ticks: {
                        // Ensure integers only
                        callback: function(value) {if (value % 1 === 0) {return value;}}
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Category'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // No need for legend if only one dataset
                },
                title: {
                    display: true,
                    text: 'All-Time Posts by Category' // Updated title
                }
            }
        }
    });
});
</script>