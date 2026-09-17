<?php
require_once __DIR__ . '/includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Log Aktivitas - SALESPRO';
$conn = dbConnect();
$logs = $conn->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 200");
$conn->close();
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Log Aktivitas Admin</h2>
                </div>

                <div class="panel">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>Pengguna</th>
                                    <th>Modul</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                    <th>IP</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logs && $logs->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $logs->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_pengguna'] ?? 'System'); ?></td>
                                            <td><?php echo htmlspecialchars($row['module']); ?></td>
                                            <td><?php echo htmlspecialchars($row['action']); ?></td>
                                            <td>
                                                <span class="badge status-<?php echo htmlspecialchars($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['ip_address'] ?? '-'); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($row['details'] ?? '-')); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada log aktivitas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
