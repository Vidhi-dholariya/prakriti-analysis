<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../includes/db.php';
$user_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'];
$email = $_SESSION['user_email'];
$avatar = strtoupper(substr($name, 0, 1));
$dominant = null;
$stmt = $db->prepare('SELECT dominant_dosha FROM prakriti_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $dominant = $row['dominant_dosha'];
}
$advice = [
    'Vata' => 'Routine is vital. Aim to sleep and wake at regular times. Gentle yoga, meditation, self-massage with warm oils. Avoid overstimulation.',
    'Pitta' => 'Avoid excess heat or sun. Practice cooling activities: walking, swimming, relaxation breaks. Schedule downtime. Sleep before 11pm.',
    'Kapha' => 'Be physically active, especially in the morning. Vary your routine. Engage in stimulating activities, brisk walking, sports, and avoid oversleeping.'
];
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
    <title>Daily Schedule | Prakriti Analysis</title>
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
            <?=nav_item('Daily Schedule', 'calendar3', 'schedule.php', true)?><!-- Active -->
            <?=nav_item('Feedback', 'chat-dots', 'feedback.php')?>
        </div>
        <div class="nav-bottom list-group list-group-flush">
            <?=nav_item('Settings', 'gear', '#')?>
            <a href="logout.php" class="list-group-item list-group-item-action border-0 text-danger"><i class="bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </div>
    <div class="content-main p-4">
        <div class="bg-white p-4 rounded shadow-sm" style="max-width:650px; margin:auto;">
            <h4 class="mb-3">Your Personalized Daily Schedule Advice</h4>
            <?php if ($dominant): ?>
                <div class="alert alert-info mb-3">You are primarily <strong><?=htmlspecialchars($dominant)?></strong> prakriti.</div>
                <p><strong>Daily Schedule Advice:</strong> <?=$advice[$dominant]?></p>
            <?php else: ?>
                <div class="alert alert-warning">Please complete the Prakriti Analysis to receive your personalized daily schedule advice.</div>
            <?php endif; ?>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
