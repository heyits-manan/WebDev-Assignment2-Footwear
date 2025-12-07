<?php 
include '../includes/header.php'; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product):
?>
    <div class="container" style="text-align: center; padding: 4rem;">
        <h2>Product not found</h2>
        <a href="products.php" class="btn" style="margin-top: 1rem;">Back to Shop</a>
    </div>
<?php else: ?>

<div class="container">
    <div class="product-details">
        <div class="details-image">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="details-info">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($product['name']); ?></h1>
            <div style="font-size: 2rem; color: var(--accent); font-weight: 700; margin-bottom: 2rem;">
                $<?php echo htmlspecialchars($product['price']); ?>
            </div>
            <p style="margin-bottom: 2rem; color: var(--text);">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
            
            <form action="cart.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="form-group" style="max-width: 100px;">
                    <label for="quantity">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" required>
                </div>
                <button type="submit" class="btn" style="width: 100%; border: none; cursor: pointer; margin-top: 1rem;">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>
<?php include '../includes/footer.php'; ?>
