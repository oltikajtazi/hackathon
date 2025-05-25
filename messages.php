<?php
include('inc/db.php');
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];

// Get chat partner
$partner_id = isset($_GET['user']) ? intval($_GET['user']) : 0;

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $partner_id && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $partner_id, $msg);
    $stmt->execute();
    $stmt->close();
}

// Mark messages as read
if ($partner_id) {
    $stmt = $conn->prepare("UPDATE messages SET is_read=1 WHERE receiver_id=? AND sender_id=?");
    $stmt->bind_param("ii", $user_id, $partner_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch chat users (all users you've chatted with)
$users = $conn->query("SELECT DISTINCT u.id, u.email FROM users u
    JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE (m.sender_id = $user_id OR m.receiver_id = $user_id) AND u.id != $user_id");

// Fetch messages with selected user
$chat = [];
if ($partner_id) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY sent_at ASC");
    $stmt->bind_param("iiii", $user_id, $partner_id, $partner_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $chat[] = $row;
    $stmt->close();
}

include('inc/header.php');
?>
<div class="container mt-5" style="max-width:900px;">
  <h2 class="mb-4">💬 Mesazhet</h2>
  <div class="row">
    <div class="col-md-4">
      <h5>Bisedat</h5>
      <ul class="list-group">
        <?php while($u = $users->fetch_assoc()): ?>
          <li class="list-group-item<?php if($partner_id == $u['id']) echo ' active'; ?>">
            <a href="messages.php?user=<?php echo $u['id']; ?>" class="text-decoration-none<?php if($partner_id == $u['id']) echo ' text-white'; ?>">
              <?php echo htmlspecialchars($u['email']); ?>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
    <div class="col-md-8">
      <?php if ($partner_id): ?>
        <div class="border rounded-4 p-3 mb-3 bg-light" style="height:350px; overflow-y:auto;">
          <?php foreach($chat as $msg): ?>
            <div class="mb-2">
              <span class="fw-bold"><?php echo $msg['sender_id'] == $user_id ? 'Ju:' : 'Ata:'; ?></span>
              <span><?php echo nl2br(htmlspecialchars($msg['message'])); ?></span>
              <span class="text-muted small ms-2"><?php echo date('d.m.Y H:i', strtotime($msg['sent_at'])); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <form method="post" class="d-flex">
          <input type="text" name="message" class="form-control me-2" placeholder="Shkruaj mesazhin..." required>
          <button type="submit" class="btn btn-primary">Dërgo</button>
        </form>
      <?php else: ?>
        <div class="alert alert-info">Zgjidh një përdorues për të biseduar.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include('inc/footer.php'); ?>