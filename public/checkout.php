<?php
include '../includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$success = false;
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $total += $product['price'] * $qty;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();
        
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($products as $product) {
            $qty = $_SESSION['cart'][$product['id']];
            $stmt_item->execute([$order_id, $product['id'], $qty, $product['price']]);
        }
        
        $pdo->commit();
        
        unset($_SESSION['cart']);
        $success = true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <div class="form-container" style="max-width: 600px;">
        <h2 class="section-title">Checkout</h2>
        
        <?php if ($success): ?>
            <div style="text-align: center;">
                <h3 style="color: green; margin-bottom: 1rem;">Order Placed Successfully!</h3>
                <p>Thank you for your purchase.</p>
                <a href="index.php" class="btn" style="margin-top: 2rem;">Return Home</a>
            </div>
        <?php elseif (empty($_SESSION['cart'])): ?>
            <p style="text-align: center;">Your cart is empty.</p>
        <?php else: ?>
            <h3 style="margin-bottom: 1rem;">Order Summary</h3>
            <p style="margin-bottom: 0.5rem;">Total Items: <?php echo array_sum($_SESSION['cart']); ?></p>
            <p style="font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem;">Total Amount: $<?php echo number_format($total, 2); ?></p>
            
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea required placeholder="Enter your full address" style="width: 100%; height: 100px; padding: 0.8rem;"></textarea>
                </div>
                <div class="form-group">
                    <label>Card Number (Mock)</label>
                    <input type="text" value="**** **** **** 1234" disabled style="background: #eee;">
                </div>
                
                <button type="submit" class="btn" style="width: 100%; border: none; cursor: pointer;">Place Order</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
