<?php
include '../includes/header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = (int)$_POST['product_id'];

    if ($action === 'add') {
        $quantity = (int)$_POST['quantity'];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
}

$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = [
            'product' => $product,
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="container">
    <h2 class="section-title">Your Cart</h2>
    
    <?php if (empty($cart_items)): ?>
        <p style="text-align: center;">Your cart is empty. <a href="products.php" style="color: var(--accent);">Start Shopping</a></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--primary); text-align: left;">
                    <th style="padding: 1rem;">Product</th>
                    <th style="padding: 1rem;">Price</th>
                    <th style="padding: 1rem;">Quantity</th>
                    <th style="padding: 1rem;">Total</th>
                    <th style="padding: 1rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;">
                        <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; vertical-align: middle; margin-right: 10px;">
                        <?php echo htmlspecialchars($item['product']['name']); ?>
                    </td>
                    <td style="padding: 1rem;">$<?php echo htmlspecialchars($item['product']['price']); ?></td>
                    <td style="padding: 1rem;"><?php echo $item['qty']; ?></td>
                    <td style="padding: 1rem;">$<?php echo number_format($item['subtotal'], 2); ?></td>
                    <td style="padding: 1rem;">
                        <form action="cart.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                            <button type="submit" style="background: none; border: none; color: var(--accent); cursor: pointer; text-decoration: underline;">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="text-align: right; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Total: $<?php echo number_format($total, 2); ?></h3>
            <a href="checkout.php" class="btn">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
