<?php
$notif_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($notif_count);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Puna përty</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php">dboltii</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Kreu</a></li>
        <li class="nav-item"><a class="nav-link" href="jobs.php">Punët</a></li>
        <li class="nav-item"><a class="nav-link" href="quiz.php">Take Quiz</a></li>
        <li class="nav-item"><a class="nav-link" href="courses.php">Free Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="review.php">Reviews</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="post-job.php">Posto Punë</a></li>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Dil</a></li>
          <li class="nav-item"><a class="nav-link" href="company_profile.php">Kompania Ime</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Hyr</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Regjistrohu</a></li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link position-relative" href="notifications.php">
            🔔
            <?php if ($notif_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $notif_count; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="messages.php">Mesazhet</a></li>
      </ul>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
