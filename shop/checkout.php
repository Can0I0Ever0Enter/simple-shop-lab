<?php
require_once 'db.php';

$cart_items = [];
$total_price = 0;

$sql_cart = "SELECT ci.id as cart_item_id, ci.quantity, p.id as product_id, p.name, p.price
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_session_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("s", $user_session_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows > 0) {
    while ($row = $result_cart->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['price'] * $row['quantity'];
    }
} else {
    header("Location: cart.php");
    exit();
}
$stmt_cart->close();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - Простой Магазин</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="cart.php">Корзина</a></li>
            </ul>
        </nav>
        <h1>Оформление заказа</h1>

        <h2>Ваш заказ:</h2>
        <table class="cart-table">
             <thead>
                <tr>
                    <th>Товар</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                </tr>
            </thead>
             <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
         <div class="total">
            Итого к оплате: $<?= number_format($total_price, 2) ?>
        </div>

        <h2>Данные для оплаты (Тестовые)</h2>
        <form action="process_payment.php" method="post" class="checkout-form">
            <input type="hidden" name="expected_total" value="<?= number_format($total_price, 2) ?>">
            <div>
                <label for="card_number">Номер карты:</label>
                <input type="text" id="card_number" name="card_number" placeholder="4444-4444-4444-4444" required>
            </div>
            <div>
                <label for="card_expiry">Срок действия:</label>
                <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" required>
            </div>
             <div>
                <label for="card_cvc">CVC:</label>
                <input type="text" id="card_cvc" name="card_cvc" placeholder="123" required>
            </div>
            <div>
                <label for="promo_code">Промокод (необязательно):</label>
                <input type="text" id="promo_code" name="promo_code" placeholder="SUMMER25">
            </div>
             <div>
                <label for="payment_notes">Комментарий к платежу:</label>
                <textarea id="payment_notes" name="payment_notes" rows="3" placeholder="Оставьте комментарий здесь..."></textarea>
            </div>
            <button type="submit" class="button">Оплатить $<?= number_format($total_price, 2) ?></button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
