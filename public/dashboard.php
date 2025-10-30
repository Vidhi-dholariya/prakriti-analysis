<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../includes/db.php';
$user_id = $_SESSION['user_id'];
$alert = '';
// Load user info
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Load or create user_health record
$stmt = $db->prepare('SELECT * FROM user_health WHERE user_id = ?');
$stmt->execute([$user_id]);
$health = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$health) {
    $db->prepare('INSERT INTO user_health (user_id) VALUES (?)')->execute([$user_id]);
    $stmt = $db->prepare('SELECT * FROM user_health WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $health = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Handle personal info submit
if (isset($_POST['save_personal'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $gender = trim($_POST['gender']);
    $dob = trim($_POST['dob']);
    $phone = trim($_POST['phone']);
    if (!$name || !$email) {
        $alert = '<div class="alert alert-danger">Name and Email are required.</div>';
    } else {
        try {
            $stmt = $db->prepare('UPDATE users SET name=?, email=?, gender=?, dob=?, phone=? WHERE id=?');
            $stmt->execute([$name, $email, $gender, $dob, $phone, $user_id]);
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $user = array_merge($user, compact('name','email','gender','dob','phone'));
            $alert = '<div class="alert alert-success">Personal information updated.</div>';
        } catch (PDOException $e) {
            $alert = '<div class="alert alert-danger">Email already exists.</div>';
        }
    }
}
// Handle health info submit
if (isset($_POST['save_health'])) {
    $height = trim($_POST['height']);
    $weight = trim($_POST['weight']);
    $blood_group = trim($_POST['blood_group']);
    $allergies = trim($_POST['allergies']);
    $diseases = trim($_POST['diseases']);
    $stmt = $db->prepare('UPDATE user_health SET height=?, weight=?, blood_group=?, allergies=?, diseases=? WHERE user_id=?');
    $stmt->execute([$height, $weight, $blood_group, $allergies, $diseases, $user_id]);
    $health = array_merge($health, compact('height','weight','blood_group','allergies','diseases'));
    $alert = '<div class="alert alert-success">Health information updated.</div>';
}
$avatar = strtoupper(substr($user['name'], 0, 1));
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
    <title>Dashboard | Prakriti Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="user-info">
            <div class="user-avatar mb-2 text-bg-primary"><span><?=$avatar?></span></div>
            <div class="fw-bold" style="letter-spacing: 0.02em;"> <?=$user['name']?> </div>
            <div class="user-email"> <?=$user['email']?> </div>
        </div>
        <div class="nav-main list-group list-group-flush flex-grow-1">
            <?=nav_item('Dashboard', 'grid-1x2-fill', 'dashboard.php', true)?><!-- Active -->
            <?=nav_item('Prakriti Analysis', 'bar-chart-line', 'prakriti.php')?>
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
        <?=$alert?>
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">Personal Information</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="health-tab" data-bs-toggle="tab" data-bs-target="#health" type="button" role="tab">Health Information</button>
            </li>
        </ul>
        <div class="tab-content bg-white p-4 rounded shadow-sm" id="myTabContent" style="min-height:320px;">
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <h5>Personal Information</h5>
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="<?=htmlspecialchars($user['name'] ?? '')?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?=htmlspecialchars($user['email'] ?? '')?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">--Select--</option>
                            <option value="Male" <?=($user['gender'] ?? '')=='Male'?'selected':''?>>Male</option>
                            <option value="Female" <?=($user['gender'] ?? '')=='Female'?'selected':''?>>Female</option>
                            <option value="Other" <?=($user['gender'] ?? '')=='Other'?'selected':''?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="dob" value="<?=htmlspecialchars($user['dob'] ?? '')?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?=htmlspecialchars($user['phone'] ?? '')?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="save_personal" class="btn btn-primary">Save Personal Info</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="health" role="tabpanel">
                <h5>Health Information</h5>
                <form method="POST" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Height</label>
                        <input type="text" class="form-control" name="height" value="<?=htmlspecialchars($health['height'] ?? '')?>" placeholder="eg. 170 cm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Weight</label>
                        <input type="text" class="form-control" name="weight" value="<?=htmlspecialchars($health['weight'] ?? '')?>" placeholder="eg. 65 kg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <input type="text" class="form-control" name="blood_group" value="<?=htmlspecialchars($health['blood_group'] ?? '')?>" placeholder="eg. A+">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Allergies</label>
                        <input type="text" class="form-control" name="allergies" value="<?=htmlspecialchars($health['allergies'] ?? '')?>" placeholder="eg. None, Pollen, Nuts...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Chronic Diseases</label>
                        <input type="text" class="form-control" name="diseases" value="<?=htmlspecialchars($health['diseases'] ?? '')?>" placeholder="eg. None, Diabetes, Asthma...">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="save_health" class="btn btn-primary">Save Health Info</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
