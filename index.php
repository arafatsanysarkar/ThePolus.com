<?php
session_start();

// Database connection
include('includes/db.php');
include('includes/functions.php');

// Count total items in cart
$_SESSION['cart_count'] = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $_SESSION['cart_count'] += $item['quantity'];
    }
}

// Fetch products and brand names using JOIN
$sql = "SELECT products.*, users.brand_name 
        FROM products 
        JOIN users ON products.seller_id = users.id";
$result = $conn->query($sql);

// Custom product display logic
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

function filter_products(&$all, $key, $order = SORT_DESC, $count = 4, &$used_ids = []) {
    $filtered = array_filter($all, function($p) use ($used_ids) {
        return !in_array($p['id'], $used_ids);
    });

    usort($filtered, function($a, $b) use ($key, $order) {
        if ($a[$key] == $b[$key]) return 0;
        return ($order == SORT_ASC) ? ($a[$key] <=> $b[$key]) : ($b[$key] <=> $a[$key]);
    });

    $selected = array_slice($filtered, 0, $count);
    foreach ($selected as $p) {
        $used_ids[] = $p['id'];
    }
    return $selected;
}

function custom_product_sequence($products) {
    $used_ids = [];
    $final = [];

    while (count($used_ids) < count($products)) {
        $final = array_merge($final, filter_products($products, 'click_count', SORT_DESC, 6, $used_ids));  // 4 most clicked
        $final = array_merge($final, filter_products($products, 'id', SORT_ASC, 4, $used_ids));           // 4 oldest
        $final = array_merge($final, filter_products($products, 'id', SORT_DESC, 4, $used_ids));          // 4 newest
        $final = array_merge($final, filter_products($products, 'click_count', SORT_ASC, 4, $used_ids));  // 4 least clicked
        $final = array_merge($final, filter_products($products, 'id', SORT_DESC, 10, $used_ids));         // 10 newest
        $final = array_merge($final, filter_products($products, 'click_count', SORT_DESC, 6, $used_ids)); // 6 most clicked
        $final = array_merge($final, filter_products($products, 'click_count', SORT_ASC, 2, $used_ids));  // 2 least clicked
        $final = array_merge($final, filter_products($products, 'id', SORT_DESC, 4, $used_ids));          // 4 newest
        $final = array_merge($final, filter_products($products, 'id', SORT_ASC, 4, $used_ids));           // 4 oldest
    }

    return $final;
}

$products = custom_product_sequence($products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <!-- Primary Meta Tags -->
    <title>The Polus</title>
    <meta name="title" content="The Polus">
    <meta name="description" content="Discover books, fashion, beauty, baby products, and more at The Polus. Trusted Bangladeshi online shopping destination.">
    <meta name="keywords" content="online shopping, Bangladesh, The Polus, buy books, buy clothes, baby products, beauty, stationery">
    <meta name="author" content="The Polus Team">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="The Polus">
    <meta property="og:url" content="https://thepolus.free.nf/"> <!-- তোমার আসল ডোমেইন বসাও -->
    <meta property="og:title" content="The Polus – Affordable Online Shopping in Bangladesh">
    <meta property="og:description" content="Discover books, fashion, beauty, baby products, and more at The Polus. Trusted Bangladeshi online shopping destination.">
    <meta property="og:image" content="https://thepolus.free.nf/image/logofav.png"> <!-- এখানে তোমার লোগো বা থাম্বনেইল URL বসাও -->

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://thepolus.free.nf/">
    <meta property="twitter:title" content="The Polus">
    <meta property="twitter:description" content="Discover books, fashion, beauty, baby products, and more at The Polus. Trusted Bangladeshi online shopping destination.">
    <meta property="twitter:image" content="https://thepolus.free.nf/image/logofav.png">

    <!-- Favicon -->
    <link rel="icon" href="https://thepolus.free.nf/image/logofav.png" sizes="48x48" type="image/png">


    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="footer.css">
    
</head>
<body>

<section id="header">
    <a href="#"><img src="image/logo.png" class="logo" alt=""></a>

    <div>
        <ul id="navbar">
            <li><a class="active" href="index.php">Home</a></li>
            <li><a href="#shop.html">Shop</a></li>
            <li><a href="#about.html">About</a></li>
            <li><a href="contact.php">Contact</a></li>

            <?php if (isLoggedIn()): ?>
                <?php if (isSeller()): ?>
                    <li><a href="seller/dashboard.php">Profile</a></li>
                <?php elseif (isCustomer()): ?>
                    <li><a href="customer/dashboard.php">Profile</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="login.php">Log in</a></li>
            <?php endif; ?>

            <li id="lg-bag">
                <a href="cart.php">
                    <i class="cart">🛒</i>
                    <?php if (!empty($_SESSION['cart_count'])): ?>
                        <span class="cart-badge"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <a href="#" id="close"><i class="fa-solid fa-xmark"></i></a>
        </ul>
    </div>

    <div id="mobile">
        <a href="cart.php" class="mobile-cart">
            <i class="cart">🛒</i>
            <?php if (!empty($_SESSION['cart_count'])): ?>
                <span class="cart-badge"><?php echo $_SESSION['cart_count']; ?></span>
            <?php endif; ?>
        </a>
        <i id="bar" class="fas fa-outdent"></i>
    </div>
</section>

<!-- Category Section -->
<section id="feature" class="section-p1">
    <div class="fe-box">
        <img src="image/Books.jpg" alt="Books" width="150" height="150" loading="lazy">
        <h6>Book</h6>
    </div>
    <div class="fe-box">
        <img src="image/Fashion.jpg" alt="Clothing" width="150" height="150" loading="lazy">
        <h6>Clothing</h6>
    </div>
    <div class="fe-box">
        <img src="image/Beauty.jpg" alt="Beauty" width="150" height="150" loading="lazy">
        <h6>Beauty</h6>
    </div>
    <div class="fe-box">
        <img src="image/Baby Products.jpg" alt="Baby Products" width="150" height="150" loading="lazy">
        <h6>Baby Products</h6>
    </div>
    <div class="fe-box">
        <img src="image/Stationery.jfif" alt="Stationery" width="150" height="150" loading="lazy">
        <h6>Stationery</h6>
    </div>
    <div class="fe-box">
        <img src="image/see-more.jpg" alt="see-more-image" width="150" height="150" loading="lazy">
        <h6>See More</h6>
    </div>
</section>

<!-- Product Section -->
<section id="product1" class="section-p1">
    <h2>Product</h2>
    <div class="pro-container">
        <?php foreach ($products as $row) { ?>
            <div class="pro">
                <a href="product.php?id=<?php echo $row['id']; ?>">
                    <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" style="margin-bottom: 5px;">
                </a>
                <div class="des">


                    <a href="seller_products.php?seller_id=<?php echo $row['seller_id']; ?>" target="_blank" style="margin: 3px 0 1px; font-size: 13px; font-weight: 500; color: #833a0a; text-decoration: none;">
    <?php echo htmlspecialchars($row['brand_name']); ?>
</a>



                    <h5 style="margin: 0;">
                        <a href="product.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: #333;">
                            <?php echo $row['title']; ?>
                        </a>
                    </h5>
                    <h4>৳ <?php echo $row['price']; ?></h4>
                    <p style="color:#555;font-size:14px;margin:5px 0;">Clicks: <?php echo $row['click_count']; ?></p>
                    <a href="product.php?id=<?php echo $row['id']; ?>"><i class="cart">🛒</i></a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>



<!-- Footer Section -->
<footer class="section-p1"> 

    <div class="col">
        <a href="#"><img src="image/logo.png" height="50px" class="logo" alt=""></a>
        <h4>Contact</h4>
        <p><strong>Address:</strong> Koasba, Dinajpur</p>
        <p><strong>Phone:</strong> +880 1766595965</p>
        <p><strong>Hours:</strong> 10:00 - 18:00</p>
    </div>

    <div class="follow">
        <h4>Follow Us</h4>
        <div>
            <a href="https://www.facebook.com/profile.php?id=61573714334614" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
    </div>

    <div class="col">
        <h4>About</h4>
        <a href="#">About Us</a>
        <a href="#">Delivery Information</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="#">Contact Us</a>
    </div>
    
    <div class="col">
        <h4>My Account</h4>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="#">Order History</a>
        <a href="cart.php">Cart</a>
    </div>

    <div class="col pay">
        <h4>Secured Payment Gateways</h4>
        <!-- Bkash logo -->
        <img src="#" alt="Bkash" height="30px">
        <!-- Nagad logo -->
        <img src="#" alt="Nagad" height="30px">
    </div>

    <div class="copyright">
        <p>@ 2025, The Polus</p>
    </div>

</footer>






<script src="script.js"></script>
<script src="https://kit.fontawesome.com/c9847c48a6.js" crossorigin="anonymous"></script>
</body>
</html>
