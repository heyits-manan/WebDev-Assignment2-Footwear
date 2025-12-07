<?php include '../includes/header.php'; ?>

<div class="container">
    <h2 class="section-title">All Products</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        while ($product = $stmt->fetch()):
        ?>
            <div class="product-card">
                <div class="product-image" style="background-image: url('<?php echo htmlspecialchars($product['image_url']); ?>'); background-size: cover; background-position: center;"></div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-price">$<?php echo htmlspecialchars($product['price']); ?></div>
                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn" style="margin-top: 1rem; width: 100%; text-align: center; box-sizing: border-box;">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
