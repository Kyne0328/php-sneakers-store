<?php include 'includes/header.php'; ?>

<div class="hero">
    <h2>Welcome to Sneakers Store</h2>
    <p>Discover the latest and greatest in sneaker fashion</p>
</div>

<div class="featured-products">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php
        // Sample product data (in a real application, this would come from a database)
        $featured_products = [
            [
                'name' => 'Nike Air Max',
                'price' => 129.99,
                'image' => 'img/placeholder.jpg'
            ],
            [
                'name' => 'Adidas Ultraboost',
                'price' => 159.99,
                'image' => 'img/placeholder.jpg'
            ],
            [
                'name' => 'Puma RS-X',
                'price' => 89.99,
                'image' => 'img/placeholder.jpg'
            ]
        ];

        foreach ($featured_products as $product) {
            echo '<div class="product-card">';
            echo '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
            echo '<h3>' . $product['name'] . '</h3>';
            echo '<p class="price">$' . number_format($product['price'], 2) . '</p>';
            echo '<a href="cart.php?action=add&product=' . urlencode($product['name']) . '" class="btn">Add to Cart</a>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 