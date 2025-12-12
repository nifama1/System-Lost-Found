<?php
include('config.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

$query = "SELECT nm_item, th_item, etc_item, img_item, status, nm_pengguna, reward 
        FROM item 
        WHERE status = 'Lost'
        ORDER BY th_item DESC 
        LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $items_per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $image = !empty($row['img_item']) ? $row['img_item'] : 'images/default-item.png';
        ?>
        <div class="item-card" onclick="showItemDetails(<?php echo htmlspecialchars(json_encode([
            'name' => $row['nm_item'],
            'description' => $row['etc_item'],
            'image' => $image,
            'status' => $row['status'],
            'reward' => $row['reward'],
            'reporter' => $row['nm_pengguna'],
            'date' => date('M d, Y', strtotime($row['th_item']))
        ])); ?>)">
            <div class="item-image-container">
                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($row['nm_item']); ?>">
                <div class="item-overlay">
                    <span class="view-details">Click to view details</span>
                </div>
            </div>
            <div class="item-info">
                <h4><?php echo htmlspecialchars($row['nm_item']); ?></h4>
                <div class="item-details">
                    <span class="item-status <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                    <span class="item-date"><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($row['th_item'])); ?></span>
                </div>
            </div>
        </div>
        <?php
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?> 