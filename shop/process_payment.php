<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit();
}

$card_number = $_POST['card_number'] ?? '';
$card_expiry = $_POST['card_expiry'] ?? '';
$card_cvc = $_POST['card_cvc'] ?? '';
$promo_code = $_POST['promo_code'] ?? '';
$payment_notes = $_POST['payment_notes'] ?? '';
$expected_total_from_form = $_POST['expected_total'] ?? 0;

$cart_items_data = [];
$actual_total_price = 0;
$sql_cart = "SELECT ci.quantity, p.id as product_id, p.price
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_session_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("s", $user_session_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows > 0) {
    while ($row = $result_cart->fetch_assoc()) {
        $cart_items_data[] = $row;
        $actual_total_price += $row['price'] * $row['quantity'];
    }
} else {
    header("Location: cart.php?error=empty");
    exit();
}
$stmt_cart->close();

if ($conn->query($sql_insert_order) === TRUE) {
$order_id = $conn->insert_id;

$stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
foreach ($cart_items_data as $item) {
    $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $stmt_items->execute();
}
$stmt_items->close();

$stmt_clear_cart = $conn->prepare("DELETE FROM cart_items WHERE user_session_id = ?");
$stmt_clear_cart->bind_param("s", $user_session_id);
$stmt_clear_cart->execute();
$stmt_clear_cart->close();

header("Location: order_success.php?order_id=" . $order_id);
exit();

} else {
    error_log("Error: " . $conn->error);
    die("Ошибка при создании заказа");
}

$conn->close();
?>
