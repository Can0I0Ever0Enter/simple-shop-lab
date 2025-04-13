<?php
require_once 'db.php';

$action = $_GET['action'] ?? null;
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : null; 
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; 

if ($action === 'add' && $product_id) {
    $stmt_check = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_session_id = ? AND product_id = ?");
    $stmt_check->bind_param("si", $user_session_id, $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;
        $stmt_update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt_update->bind_param("ii", $new_quantity, $row['id']);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO cart_items (user_session_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt_insert->bind_param("si", $user_session_id, $product_id);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_check->close();
    header("Location: index.php?added=1");
    exit();
}

if ($action === 'remove' && $item_id) {
    $stmt_delete = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
    $stmt_delete->bind_param("i", $item_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: cart.php?removed=1");
    exit();
}

if ($action === 'update' && isset($_POST['item_id'])) {
    $update_item_id = (int)$_POST['item_id'];
    $new_quantity = max(1, (int)$_POST['quantity']);

    $stmt_update_qty = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt_update_qty->bind_param("ii", $new_quantity, $update_item_id);
    $stmt_update_qty->execute();
    $stmt_update_qty->close();
    header("Location: cart.php?updated=1");
    exit();
}

$cart_items = [];
$total_price = 0;

$sql_cart = "SELECT ci.id as cart_item_id, ci.quantity, p.id as product_id, p.name, p.price, p.image_url
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
}
$stmt_cart->close();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Простой Магазин</title>
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
        <h1>Ваша Корзина</h1>

        <?php
        if (isset($_GET['removed'])) echo "<p class='message success'>Товар удален из корзины.</p>";
        if (isset($_GET['updated'])) echo "<p class='message success'>Количество обновлено.</p>";
        ?>

        <?php if (!empty($cart_items)): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" width="50" style="vertical-align: middle; margin-right: 10px;">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>$<?= htmlspecialchars($item['price']) ?></td>
                            <td>
                                <form action="cart.php?action=update" method="post" style="display: inline;">
                                    <input type="hidden" name="item_id" value="<?= $item['cart_item_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width: 60px;">
                                    <button type="submit" class="button" style="padding: 5px 8px;">Обновить</button>
                                </form>
                            </td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            <td class="cart-actions">
                                <a href="cart.php?action=remove&item_id=<?= $item['cart_item_id'] ?>" class="remove">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                Общая сумма: $<?= number_format($total_price, 2) ?>
            </div>
            <div style="text-align: right; margin-top: 20px;">
            <a href="checkout.php" class="button">Оформить заказ</a>
            </div>
        <?php else: ?>
            <p class="message info">Ваша корзина пуста.</p>
             <a href="index.php" class="button">К товарам</a>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>