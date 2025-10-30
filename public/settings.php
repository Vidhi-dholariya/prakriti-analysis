<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$name = $_SESSION['user_name'];
$email = $_SESSION['user_email'];
$avatar = strtoupper(substr($name, 0, 1));
function nav_item($label, $icon, $link, $active = false) {
    $active_class = $active ? 'active' : '';
    return "<a href='$link' class='list-group-item list-group-item-action $active_class border-0'><i class='bi-$icon me-2'></i>$label</a>";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | Prakriti Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="user-info">
            <div class="user-avatar mb-2 text-bg-primary"><span><?=$avatar?></span></div>
            <div class="fw-bold" style="letter-spacing: 0.02em;"> <?=$name?> </div>
            <div class="user-email"> <?=$email?> </div>
        </div>
        <div class="nav-main list-group list-group-flush flex-grow-1">
            <?=nav_item('Dashboard', 'grid-1x2-fill', 'dashboard.php')?>
            <?=nav_item('Prakriti Analysis', 'bar-chart-line', 'prakriti.php')?>
            <?=nav_item('Diet Chart', 'egg-fried', 'diet.php')?>
            <?=nav_item('Daily Schedule', 'calendar3', 'schedule.php')?>
            <?=nav_item('Feedback', 'chat-dots', 'feedback.php')?>
        </div>
        <div class="nav-bottom list-group list-group-flush">
            <?=nav_item('Settings', 'gear', 'settings.php', true)?><!-- Active -->
            <a href="logout.php" class="list-group-item list-group-item-action border-0 text-danger"><i class="bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </div>
    <div class="content-main p-4">
        <div class="bg-white p-4 rounded shadow-sm" style="max-width:600px; margin:auto">
            <h4>Settings</h4>
            <p class="text-muted">Settings coming soon.</p>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
