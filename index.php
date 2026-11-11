<?php
require "config.php";
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "report") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $category = $_POST["category"] ?? "";
    $location = trim($_POST["location"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $allowed = ["Pothole", "Garbage", "Water Leakage", "Other"];

    if ($name === "" || $location === "" || $description === "" || !in_array($category, $allowed, true)) {
        $error = "Please fill in all required fields.";
    } else {
        $photoName = null;
        if (!empty($_FILES["photo"]["name"])) {
            $allowedTypes = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];
            $mime = mime_content_type($_FILES["photo"]["tmp_name"]);
            if (!isset($allowedTypes[$mime]) || $_FILES["photo"]["size"] > 3 * 1024 * 1024) {
                $error = "Photo must be JPG, PNG or WEBP and smaller than 3 MB.";
            } else {
                $photoName = bin2hex(random_bytes(12)) . "." . $allowedTypes[$mime];
                if (!move_uploaded_file($_FILES["photo"]["tmp_name"], __DIR__ . "/uploads/" . $photoName)) {
                    $error = "Could not upload the photo. Please try again.";
                }
            }
        }
        if ($error === "") {
            $stmt = $pdo->prepare("INSERT INTO reports (name,email,category,location,description,photo,status,created_at) VALUES (?,?,?,?,?,?, 'Submitted', NOW())");
            $stmt->execute([$name, $email, $category, $location, $description, $photoName]);
            $id = $pdo->lastInsertId();
            $success = "Report submitted successfully. Your tracking ID is SC-" . str_pad($id, 5, "0", STR_PAD_LEFT) . ". Save this ID to track your report.";
        }
    }
}

$reports = $pdo->query("SELECT id, category, location, description, status, created_at FROM reports ORDER BY created_at DESC LIMIT 30")->fetchAll();
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CityCare | Smart City Grievance Reporting</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-icon">✦</span> CityCare</a><nav><a href="#report">Report an Issue</a><a href="#track">Track Reports</a><a href="admin.php">Authority Login</a></nav></header>
<main>
<section class="hero">
 <div class="hero-copy"><span class="eyebrow">CIVIC TECH • BETTER NEIGHBOURHOODS</span>
 <h1>Make your city<br><em>better, together.</em></h1>
 <p>Report potholes, garbage and water leaks. Follow every update and help your local authority build a cleaner, safer city.</p>
 <a class="primary-btn" href="#report">Report an issue <span>→</span></a>
 <div class="hero-note"><span class="live-dot"></span> Your voice matters. Every report counts.</div></div>
 <div class="hero-art"><div class="sun"></div><div class="building b1"></div><div class="building b2"></div><div class="building b3"></div><div class="road"></div><div class="tree">♣</div><div class="pin">!</div></div>
</section>
<section class="stats">
 <div><strong><?= (int)$pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn() ?></strong><span>Reports received</span></div>
 <div><strong><?= (int)$pdo->query("SELECT COUNT(*) FROM reports WHERE status='Resolved'")->fetchColumn() ?></strong><span>Issues resolved</span></div>
 <div><strong><?= (int)$pdo->query("SELECT COUNT(*) FROM reports WHERE status IN ('In Progress','Assigned')")->fetchColumn() ?></strong><span>Being addressed</span></div>
</section>
<?php if ($success): ?><div class="notice success"><?= h($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="notice error"><?= h($error) ?></div><?php endif; ?>
<section class="section" id="report">
 <div class="section-heading"><span class="eyebrow">TAKE ACTION</span><h2>Report a city issue</h2><p>Share a few details so the right team can take action.</p></div>
 <form class="form-card" method="post" enctype="multipart/form-data">
  <input type="hidden" name="action" value="report">
  <div class="form-grid">
   <label>Your name *<input name="name" required maxlength="100" placeholder="Enter your name"></label>
   <label>Email (optional)<input type="email" name="email" maxlength="150" placeholder="you@example.com"></label>
   <label>Issue category *<select name="category" required><option value="">Choose an issue</option><option>Pothole</option><option>Garbage</option><option>Water Leakage</option><option>Other</option></select></label>
   <label>Issue location *<input name="location" required maxlength="255" placeholder="Street, area or landmark"></label>
   <label class="full">Describe the issue *<textarea name="description" required maxlength="2000" rows="4" placeholder="Tell us what happened and any useful details..."></textarea></label>
   <label class="full">Add a photo (optional)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WEBP • Maximum 3 MB</small></label>
  </div>
  <button class="primary-btn" type="submit">Submit report <span>→</span></button>
 </form>
</section>
<section class="section track-section" id="track">
 <div class="section-heading"><span class="eyebrow">TRANSPARENCY</span><h2>Recent community reports</h2><p>See the latest issues reported by citizens and their current status.</p></div>
 <div class="report-list">
 <?php if (!$reports): ?><div class="empty">No reports yet. Be the first to report an issue.</div><?php endif; ?>
 <?php foreach ($reports as $r): ?>
  <article class="report-item"><div class="issue-symbol"><?= $r["category"]==="Pothole"?"◉":($r["category"]==="Garbage"?"♻":($r["category"]==="Water Leakage"?"≈":"!")) ?></div>
   <div class="report-info"><div class="report-title"><?= h($r["category"]) ?> <span class="report-id">SC-<?= str_pad($r["id"],5,"0",STR_PAD_LEFT) ?></span></div><p><?= h($r["description"]) ?></p><small>⌖ <?= h($r["location"]) ?> · <?= date("d M Y", strtotime($r["created_at"])) ?></small></div>
   <span class="status <?= strtolower(str_replace(" ","-",$r["status"])) ?>"><?= h($r["status"]) ?></span>
  </article>
 <?php endforeach; ?>
 </div>
 <p class="admin-hint">Are you an authority official? <a href="admin.php">Open the admin dashboard →</a></p>
</section>
</main>
<footer>© <?= date("Y") ?> CityCare · A community-powered civic reporting demo</footer>
</body></html>
