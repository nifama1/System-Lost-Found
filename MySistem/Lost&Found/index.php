<?php
include('config.php');
include('includes/functions.php');
session_start();

$page_title = 'Lost & Found System - All Items';

// Get items for display
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items = getItems($conn, $page);

include('includes/header.php');
?>

<section class="hero">
    <div class="hero-content">
        <h2>Welcome to Lost & Found</h2>
        <p>Find your items or help others find their</p>
    </div>
    <div class="actions">
        <a href="report_item.php" class="btn-primary">Report Item</a>
    </div>
</section>

<!-- All Items Section -->
<section class="recent-items"><br>
    <div class="items-grid" id="items-grid">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $row): ?>
                <?php 
                $image = getDefaultImage($row['img_item']);
                $itemData = [
                    'name' => $row['nm_item'],
                    'description' => $row['etc_item'],
                    'image' => $image,
                    'status' => $row['status'],
                    'reward' => $row['reward'],
                    'reporter' => $row['nm_pengguna'],
                    'date' => formatDate($row['th_item']),
                    'phone' => $row['no_pengguna']
                ];
                ?>
                <div class="item-card" onclick="showItemDetails(<?php echo e(json_encode($itemData)); ?>)">
                    <div class="item-image-container">
                        <img src="<?php echo e($image); ?>" alt="<?php echo e($row['nm_item']); ?>">
                        <div class="item-overlay">
                            <span class="view-details">Click to view details</span>
                        </div>
                    </div>
                    <div class="item-info">
                        <h4><?php echo e($row['nm_item']); ?></h4>
                        <div class="item-details">
                            <span class="item-status <?php echo strtolower($row['status']); ?>"><?php echo e($row['status']); ?></span>
                            <span class="item-date"><i class="far fa-calendar"></i> <?php echo formatDate($row['th_item']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-items">No items have been reported yet.</p>
        <?php endif; ?>
    </div>
    <div class="loading-spinner" id="loading-spinner">
        Loading more items...
    </div>
</section>

<script>
// Infinite scrolling functionality
let currentPage = 1;
let isLoading = false;
const itemsGrid = document.getElementById('items-grid');
const loadingSpinner = document.getElementById('loading-spinner');

async function loadMoreItems() {
    if (isLoading) return;
    
    isLoading = true;
    loadingSpinner.classList.add('active');
    
    try {
        const response = await fetch(`load_more_all_items.php?page=${currentPage + 1}`);
        const data = await response.text();
        
        if (data.trim()) {
            itemsGrid.insertAdjacentHTML('beforeend', data);
            currentPage++;
        }
    } catch (error) {
        console.error('Error loading more items:', error);
    } finally {
        isLoading = false;
        loadingSpinner.classList.remove('active');
    }
}

// Intersection Observer for infinite scrolling
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            loadMoreItems();
        }
    });
}, {
    rootMargin: '100px'
});

observer.observe(loadingSpinner);
</script>

<?php 
include('includes/footer.php');
mysqli_close($conn); 
?> 