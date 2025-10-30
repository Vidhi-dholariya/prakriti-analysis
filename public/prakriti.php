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
$questions = [
    'Skin Type' => ['Dry', 'Oily', 'Balanced'],
    'Body Build' => ['Thin', 'Muscular', 'Heavier'],
    'Hair Type' => ['Dry', 'Oily', 'Thick', 'Thin'],
    'Eyes Size' => ['Small', 'Medium', 'Large'],
    'Mindset' => ['Calm', 'Intense', 'Restless'],
    'Memory' => ['Good', 'Average', 'Forgetful'],
    'Emotions' => ['Anger', 'Anxiety', 'Content'],
    'Dietary Preference' => ['Hot', 'Cold', 'Spicy', 'Sweet'],
    'Sleep Pattern' => ['Deep', 'Light', 'Trouble Sleeping'],
    'Energy Level' => ['Energetic', 'Balanced', 'Fatigue'],
    'Weather Preference' => ['Warm', 'Cool', 'Moderate'],
    'Stress Response' => ['Anxious', 'Irritable', 'Calm']
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // -- Dosha logic --
    $answers = $_POST;
    $dosha_map = [
        'Vata' => [ 'Skin Type' => 'Dry','Body Build'=>'Thin','Hair Type'=>'Dry','Eyes Size'=>'Small','Mindset'=>'Restless','Memory'=>'Forgetful','Emotions'=>'Anxiety','Dietary Preference'=>'Warm','Sleep Pattern'=>'Light','Energy Level'=>'Energetic','Weather Preference'=>'Warm','Stress Response'=>'Anxious'],
        'Pitta' => [ 'Skin Type'=>'Oily','Body Build'=>'Muscular','Hair Type'=>'Oily','Eyes Size'=>'Medium','Mindset'=>'Intense','Memory'=>'Good','Emotions'=>'Anger','Dietary Preference'=>'Cool','Sleep Pattern'=>'Moderate','Energy Level'=>'Balanced','Weather Preference'=>'Cool','Stress Response'=>'Irritable'],
        'Kapha' => [ 'Skin Type'=>'Balanced','Body Build'=>'Heavier','Hair Type'=>'Thick','Eyes Size'=>'Large','Mindset'=>'Calm','Memory'=>'Average','Emotions'=>'Content','Dietary Preference'=>'Sweet','Sleep Pattern'=>'Deep','Energy Level'=>'Fatigue','Weather Preference'=>'Moderate','Stress Response'=>'Calm'],
    ];
    $scores = ['Vata'=>0, 'Pitta'=>0, 'Kapha'=>0];
    foreach ($answers as $q => $a) {
        foreach (['Vata','Pitta','Kapha'] as $dosha) {
            if (isset($dosha_map[$dosha][$q]) && $a == $dosha_map[$dosha][$q]) {
                $scores[$dosha]++;
            }
        }
    }
    arsort($scores);
    $dominant = array_key_first($scores);
    // Save to DB
    $stmt = $db->prepare('INSERT INTO prakriti_results (user_id, answers, dominant_dosha, reflection) VALUES (?, ?, ?, ?)');
    $stmt->execute([$user_id, json_encode($answers), $dominant, '']);
    header('Location: prakriti_result.php');
    exit;
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
    <title>Prakriti Analysis | Prakriti Web App</title>
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
        <div class="bg-white p-4 rounded shadow-sm" style="max-width:600px; margin:auto;">
            <h4 class="mb-4">Prakriti Analysis Questionnaire</h4>
            <form method="POST">
                <?php foreach ($questions as $label => $options): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?=$label?></label>
                    <div>
                        <?php foreach ($options as $opt): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" required name="<?=htmlspecialchars($label)?>" id="q_<?=md5($label.'_'.$opt)?>" value="<?=htmlspecialchars($opt)?>">
                            <label class="form-check-label" for="q_<?=md5($label.'_'.$opt)?>">
                                <?=htmlspecialchars($opt)?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary w-100">Submit Analysis</button>
            </form>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
