<?php
session_start();
require "config.php";
$adminPassword = "citycare123"; // Change this before using beyond a classroom demo.
$error = "";
if (isset($_GET["logout"])) { session_destroy(); header("Location: admin.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    if (hash_equals($adminPassword, $_POST["password"] ?? "")) { $_SESSION["citycare_admin"] = true; }
    else { $error = "Incorrect password."; }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"]) && !empty($_SESSION["citycare_admin"])) {
    $id = (int)($_POST["id"] ?? 0);
    $status = $_POST["status"] ?? "";
    $valid = ["Submitted","Assigned","In Progress","Resolved","Rejected"];
    if ($id > 0 && in_array($status, $valid, true)) {
        $stmt = $pdo->prepare("UPDATE reports SET status=? WHERE id=?");
        $stmt->execute([$status,$id]);
    }
    header("Location: admin.php"); exit;
}
$loggedIn = !empty($_SESSION["citycare_admin"]);
$reports = $loggedIn ? $pdo->query("SELECT * FROM reports ORDER BY created_at DESC")->fetchAll() : [];
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>CityCare Admin</title><link rel="stylesheet" href="style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-icon">✦</span> CityCare</a><nav><a href="index.php">Citizen portal</a><?php if($loggedIn): ?><a href="admin.php?logout=1">Log out</a><?php endif; ?></nav></header>
<main class="admin-main">
<?php if(!$loggedIn): ?>
 <div class="login-card"><span class="eyebrow">AUTHORITY PORTAL</span><h1>Admin sign in</h1><p>Sign in to review citizen reports and update their progress.</p>
 <?php if($error): ?><div class="notice error"><?=h($error)?></div><?php endif; ?>
 <form method="post"><label>Demo admin password<input type="password" name="password" required placeholder="Enter password"></label><button class="primary-btn" name="login" value="1">Sign in →</button></form>
 <small>Classroom demo password: <b>citycare123</b></small></div>
<?php else: ?>
 <div class="section-heading"><span class="eyebrow">CITY OPERATIONS</span><h1>Grievance dashboard</h1><p>Review incoming reports and keep citizens informed.</p></div>
 <div class="admin-table-wrap"><table><thead><tr><th>ID / Date</th><th>Issue</th><th>Location & details</th><th>Photo</th><th>Update status</th></tr></thead><tbody>
 <?php foreach($reports as $r): ?><tr><td><b>SC-<?=str_pad($r["id"],5,"0",STR_PAD_LEFT)?></b><small><?=date("d M Y",strtotime($r["created_at"]))?></small></td><td><?=h($r["category"])?></td><td><b><?=h($r["location"])?></b><small><?=h($r["description"])?></small></td><td><?php if($r["photo"]): ?><a href="uploads/<?=rawurlencode($r["photo"])?>" target="_blank">View</a><?php else: ?>—<?php endif; ?></td><td><form class="status-form" method="post"><input type="hidden" name="id" value="<?=$r["id"]?>"><select name="status"><?php foreach(["Submitted","Assigned","In Progress","Resolved","Rejected"] as $s): ?><option <?= $r["status"]===$s?"selected":"" ?>><?=h($s)?></option><?php endforeach; ?></select><button name="update" value="1">Save</button></form></td></tr><?php endforeach; ?>
 <?php if(!$reports): ?><tr><td colspan="5">No citizen reports yet.</td></tr><?php endif; ?>
 </tbody></table></div>
<?php endif; ?>
</main><footer>CityCare authority dashboard · Demo project</footer></body></html>
