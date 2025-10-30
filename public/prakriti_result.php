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
// Fetch the latest result
$stmt = $db->prepare('SELECT * FROM prakriti_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$answers = $result ? json_decode($result['answers'], true) : null;
$dominant = $result['dominant_dosha'] ?? null;
$reflection = $result['reflection'] ?? '';
$scores = null;
$advice = [
    'Vata' => [
        'diet' => 'Favor warm, moist foods, avoid cold and dry snacks. Prefer cooked grains, milk, ghee, warming spices like ginger and cinnamon.',
        'schedule' => 'Maintain routine, sleep early, gentle yoga/meditation.'
    ],
    'Pitta' => [
        'diet' => 'Favor cool, refreshing foods. Eat sweet, bitter, astringent flavors. Avoid spicy, oily, fried food.',
        'schedule' => 'Avoid overheating, schedule time to relax, cooling walks, swim.'
    ],
    'Kapha' => [
        'diet' => 'Favor light, warm, spicy foods, avoid dairy, sweets, fried food. Include legumes, barley, apples.',
        'schedule' => 'Be active, wake early, vary routine, invigorating exercise.'
    ]
];
// Reflection save
$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reflection'])) {
    $newref = trim($_POST['reflection']);
    $db->prepare('UPDATE prakriti_results SET reflection=? WHERE id=?')->execute([$newref, $result['id']]);
    $reflection = $newref;
    $saved = true;
}
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
    <title>Prakriti Analysis Result</title>
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
            <?=nav_item('Prakriti Analysis', 'bar-chart-line', 'prakriti.php', true)?><!-- Active -->
            <?=nav_item('Diet Chart', 'egg-fried', 'diet.php')?>
            <?=nav_item('Daily Schedule', 'calendar3', 'schedule.php')?>
            <?=nav_item('Feedback', 'chat-dots', 'feedback.php')?>
        </div>
        <div class="nav-bottom list-group list-group-flush">
            <?=nav_item('Settings', 'gear', '#')?>
            <a href="logout.php" class="list-group-item list-group-item-action border-0 text-danger"><i class="bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </div>
    <div class="content-main p-4">
        <div class="bg-white p-4 rounded shadow-sm" style="max-width:650px; margin:auto;">
            <h4>Prakriti Analysis Result</h4>
            <?php if ($result): ?>
            <div class="mb-4">
                <h5>Your Dominant Prakriti: <span class="text-primary"><?=htmlspecialchars($dominant)?></span></h5>
                <div class="mb-2">Analysis on: <?=htmlspecialchars($result['created_at'])?></div>
                <ul>
                    <?php foreach($answers as $q=>$a): ?>
                        <li><?=htmlspecialchars($q)?>: <strong><?=htmlspecialchars($a)?></strong></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="mb-4">
                <h6>Recommended Diet</h6>
                <div class="alert alert-info small"><?=$advice[$dominant]['diet']?></div>
                <h6>Suggested Daily Schedule</h6>
                <div class="alert alert-info small"><?=$advice[$dominant]['schedule']?></div>
            </div>
            <form method="POST">
                <div class="mb-3">
                    <label for="reflection" class="form-label">Reflection: How do these traits influence your daily life, health, and well-being?</label>
                    <textarea class="form-control" id="reflection" name="reflection" rows="3" placeholder="Write your reflection..."><?=htmlspecialchars($reflection)?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Reflection</button>
                <?php if ($saved): ?><div class="text-success mt-2">Reflection saved.</div><?php endif; ?>
            </form>
            <?php else: ?>
                <div class="alert alert-warning">Please complete the Prakriti Analysis to see your results.</div>
            <?php endif; ?>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
