<?php $page = substr( $_SERVER['SCRIPT_NAME'],strrpos( $_SERVER['SCRIPT_NAME'],"/" )+1); ?>
<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">
<li class="nav-item">
    <a class="nav-link collapsed" href="../index">
      <i class="bi bi-house-fill"></i>
      <span>Visit site</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-item">
    <a class="nav-link <?= $page == 'dashboard.php' ? '' :  'collapsed'?>" href="dashboard">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-item">
    <a class="nav-link <?= $page == 'manage-users.php' || $page == 'edit-user.php' ? '' :  'collapsed'?> " href="manage-users">
      <i class="bi bi-people-fill"></i>
      <span>Manage users</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-item">
    <a class="nav-link <?= $page == 'manage-packages.php' || $page == 'add-package.php' || $page == 'edit-package.php' ? '' :  'collapsed'?> " href="manage-packages">
      <i class="bi bi-gift-fill"></i>
      <span>Manage packages</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-item">
    <a class="nav-link <?= $page == 'manage-deposits.php' || $page == 'edit-deposit.php' ? '' :  'collapsed'?> " href="manage-deposits">
      <i class="bi bi-box-arrow-down"></i>
      <span>Manage deposits</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-item">
    <a class="nav-link <?= $page == 'manage-withdrawals.php' || $page == 'edit-withdrawal.php' ? '' :  'collapsed'?> " href="manage-withdrawals">
      <i class="bi bi-box-arrow-up"></i>
      <span>Manage withdrawals</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-item">
    <a class="nav-link <?= $page == 'manage-admins.php' || $page == 'edit-admin.php' ? '' :  'collapsed'?> " href="manage-admins">
      <i class="bi bi-person-plus-fill"></i>
      <span>Manage Admins</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-item">
    <a class="nav-link <?= $page == 'region_settings.php' ? '' :  'collapsed'?> " href="region_settings">
      <i class="bi bi-gear"></i>
      <span>Region Settings</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-item">
  <form action="codes/logout.php"method="POST">
    <button class="nav-link collapsed"style="border:none;width:100%"name="logout">
      <i class="bi bi-box-arrow-left"></i>
      <span>Log out</span>
   </button>
  </form>
  </li><!-- End Dashboard Nav -->


</ul>

</aside><!-- End Sidebar-->
