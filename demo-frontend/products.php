<?php include 'includes/header.php'; ?>

<h2>Our Products</h2>

<div class="product-grid">
    <?php
    // Sample product data (in a real application, this would come from a database)
    $products = [
        [
            'name' => 'Nike Air Max',
            'price' => 129.99,
            'image' => 'img/placeholder.jpg',
            'description' => 'Classic Nike Air Max sneakers with superior comfort.'
        ],
        [
            'name' => 'Adidas Ultraboost',
            'price' => 159.99,
            'image' => 'img/placeholder.jpg',
            'description' => 'Premium running shoes with responsive cushioning.'
        ],
        [
            'name' => 'Puma RS-X',
            'price' => 89.99,
            'image' => 'img/placeholder.jpg',
            'description' => 'Retro-inspired sneakers with modern technology.'
        ],
        [
            'name' => 'New Balance 574',
            'price' => 79.99,
            'image' => 'img/placeholder.jpg',
            'description' => 'Comfortable everyday sneakers with classic style.'
        ],
        [
            'name' => 'Reebok Classic',
            'price' => 69.99,
            'image' => 'img/placeholder.jpg',
            'description' => 'Timeless design with modern comfort features.'
        ],
        [
            'name' => 'Converse Chuck Taylor',
            'price' => 59.99,
            'image' => 'img/placeholder.jpg',
            'description' => 'Iconic sneakers that never go out of style.'
        ]
    ];

    foreach ($products as $product) {
        echo '<div class="product-card">';
        echo '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
        echo '<h3>' . $product['name'] . '</h3>';
        echo '<p>' . $product['description'] . '</p>';
        echo '<p class="price">$' . number_format($product['price'], 2) . '</p>';
        echo '<a href="cart.php?action=add&product=' . urlencode($product['name']) . '" class="btn">Add to Cart</a>';
        echo '</div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?> 