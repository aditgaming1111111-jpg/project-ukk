<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
?>
<div class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar">Menu</button>
        <div>
            <p class="app-name">SALESPRO</p>
        </div>
    </div>

    <div class="topbar-user">

        <div>
            <strong><?php echo htmlspecialchars($user['nama'] ?? 'User'); ?></strong>
            <span><?php echo htmlspecialchars(ucfirst($user['role'] ?? 'user')); ?></span>
        </div>
    </div>
</div>
