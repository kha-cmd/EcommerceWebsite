<?php
include('../includes/db_config.php');
include '../includes/header.php';

$query = "SELECT * FROM products ORDER BY create_at DESC LIMIT 4";
$result = $conn->query($query);
?>

<section class="banner">
    <img src="/images/hero_banner.jpg" alt="Nike Banner">
</section>

<section class="products">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product">
                <img src="/uploads/product_images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                <h3><?= $product['name'] ?></h3>
                <p>$<?= $product['price'] ?></p>
                <a href="/php/product_details.php?id=<?= $product['product_id'] ?>">View Details</a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
