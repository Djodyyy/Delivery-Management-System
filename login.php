<?php
session_start();
if (isset($_SESSION['username'])) {
  header("Location: dashboard.php");
  exit;
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - PT Haikal Karya Nursya’ban</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
    data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
      <div class="row justify-content-center w-100">
        <div class="col-md-8 col-lg-5 col-xxl-3">
          <div class="card mb-0 shadow-sm">
            <div class="card-body">
              <a href="#" class="text-center d-block py-3">
                <img src="./assets/images/logos/logo.png" alt="Logo" style="height: 230px;">
              </a>
              <!-- Pesan Error -->
              <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger text-center">
                  <?= htmlspecialchars($_GET['error']) ?>
                </div>
              <?php endif; ?>

              <form action="functions/proses_login.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="remember" checked>
                    <label class="form-check-label" for="remember">Remember me</label>
                  </div>
                  <a href="forgot_password.php" class="text-primary small">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 py-2 mb-3">Sign In</button>
              </form>

              <div class="text-center">
                <small class="text-muted">© <?= date('Y') ?> PT. Haikal Karya Nursya’ban</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>