<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Простой Магазин</title>
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

        <h1>Каталог товаров</h1>

        <?php
        if (isset($_GET['added'])) {
            echo "<p class='message success'>Товар добавлен в корзину!</p>";
        }
        if (isset($_GET['error'])) {
            echo "<p class='message error'>Ошибка: " . $_GET['error'] . "</p>";
        }
        ?>

        <div class="products">
            <?php
            $sql = "SELECT id, name, description, price, image_url FROM products";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<p class='product-price'>Цена: $" . htmlspecialchars($row['price']) . "</p>";
                    echo "<a href='cart.php?action=add&id=" . $row['id'] . "' class='button'>Добавить в корзину</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>Товаров пока нет.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
