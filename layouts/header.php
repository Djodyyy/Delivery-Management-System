<?php
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard | PT Haikal Karya Nursya’ban</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!--  App Topstrip -->
    <div class="app-topstrip bg-dark py-6 px-3 w-100 d-lg-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center justify-content-center gap-5 mb-2 mb-lg-0">
        <div class="d-flex justify-content-center text-white fw-bold text-center w-100" id="tanggalJamRealTime">
          <!-- konten akan diganti via JS -->
        </div>

        <script>
          function updateTanggalJam() {
            const now = new Date();
            const options = {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            };
            const hariTanggal = now.toLocaleDateString('id-ID', options);

            const jam = now.getHours().toString().padStart(2, '0');
            const menit = now.getMinutes().toString().padStart(2, '0');
            const detik = now.getSeconds().toString().padStart(2, '0');

            document.getElementById('tanggalJamRealTime').textContent = `${hariTanggal} | ${jam}:${menit}:${detik}`;
          }

          setInterval(updateTanggalJam, 1000);
          updateTanggalJam(); // panggil langsung pas load
        </script>




      </div>

      <div class="d-lg-flex align-items-center gap-2">
        <div class="d-flex align-items-center justify-content-center gap-2">
          <div class="dropdown d-flex">
            <a class="btn btn-primary d-flex align-items-center gap-1" href="pengiriman.php" id="drop4">
              <i class="ti ti-truck fs-5"></i>
              Pengiriman Hari Ini
              <i class="ti ti-chevron-right fs-5"></i>
            </a>

          </div>
        </div>
      </div>

    </div>

    <!-- Sidebar -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo text-center py-2">
          <a href="dashboard.php" class="d-block">
            <img src="assets/images/logos/logo.png" alt="Logo PT Haikal" style="max-width: 150px; height: auto;" />
          </a>
        </div>
        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <!-- MAIN MENU -->
            <li class="nav-small-cap">
              <span class="hide-menu">MAIN MENU</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="dashboard.php">
                <i class="ti ti-home"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="pengiriman.php">
                <i class="ti ti-truck-delivery"></i>
                <span class="hide-menu">Pengiriman</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="detail_invoice.php">
                <i class="ti ti-link"></i>
                <span class="hide-menu">Detail Invoice</span>
              </a>
              <!-- MASTER DATA -->
            <li class="nav-small-cap">
              <span class="hide-menu">MASTER DATA</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="perusahaan.php">
                <i class="ti ti-building-skyscraper"></i>
                <span class="hide-menu">Data Perusahaan</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="mobil.php">
                <i class="ti ti-car"></i>
                <span class="hide-menu">Data Mobil</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="rute.php">
                <i class="ti ti-map-pin"></i>
                <span class="hide-menu">Data Rute</span>
              </a>
            </li>

            <!-- MANAJEMEN USER (opsional) -->
            <li class="nav-small-cap">
              <span class="hide-menu">MANAJEMEN USER</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="users.php">
                <i class="ti ti-users"></i>
                <span class="hide-menu">Data User / Admin</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="body-wrapper">
      <!-- Header Navbar -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:void(0)" id="dropProfile" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="./assets/images/profile/user-1.jpg" alt="user" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="dropProfile">
                  <div class="message-body">
                    <span class="dropdown-item d-flex align-items-center gap-2">
                      <i class="ti ti-user fs-6"></i>
                      <?= htmlspecialchars($_SESSION['username']) ?>
                    </span>
                    <a href="/Delivery-Management-System/functions/logout.php" class="dropdown-item d-flex align-items-center gap-2">
                      <i class="ti ti-logout fs-6"></i> Logout
                    </a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>



      <!-- Body content start -->
      <div class="body-wrapper-inner">
        <div class="container-fluid">