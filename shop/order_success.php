<?php
require_once 'db.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - Простой Магазин</title>
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
        <h1>Спасибо за ваш заказ!</h1>

        <?php if ($order_id > 0): ?>
            <p class="message success">Ваш заказ номер <strong><?= $order_id ?></strong> успешно оформлен и оплачен.</p>
            <p>Мы скоро свяжемся с вами для подтверждения деталей доставки.</p>
        <?php else: ?>
             <p class="message error">Произошла ошибка или номер заказа не указан.</p>
        <?php endif; ?>

        <p><a href="index.php" class="button">Вернуться в магазин</a></p>

    </div>
</body>
</html>