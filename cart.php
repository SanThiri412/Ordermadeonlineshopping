<?php
$cart_items = [
    [
        'id' => 1,
        'name' => '天然石指輪',
        'price' => 80000,
        'quantity' => 2,
        'image' => 'images/作家５(宝石)/0060.jpg'
    ]
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>あなたのカート | SELARG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/cart.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>

<div class="browser-frame">
    <main class="cart-container" id="cart-root">
        
        <div class="cart-header">
            <h1>あなたのカート</h1>
            <div class="continue-shopping">
                <a href="top.php">買い物を続ける</a>
            </div>
        </div>

        <div class="cart-columns">
            <div class="col-product">商品</div>
            <div class="col-quantity">数量</div>
            <div class="col-total">合計</div>
        </div>

       <?php foreach ($cart_items as $item): ?>
<div class="cart-item-grid" data-item-id="<?php echo (int)$item['id']; ?>">

    <div class="item-image">
        <a href="goods.php?id=<?php echo (int)$item['id']; ?>">
            <img src="<?php echo h($item['image']); ?>" 
                 alt="<?php echo h($item['name']); ?>" 
                 class="product-thumbnail" loading="lazy">
        </a>
    </div>

    <div class="item-details">
        <p class="product-name"><?php echo h($item['name']); ?></p>
        <p class="price">¥ <?php echo number_format($item['price']); ?></p>
    </div>

    <form method="post"
          class="item-quantity-control"
          data-price="<?php echo (int)$item['price']; ?>">
        <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">

        <button type="button" class="delete-button" title="削除" data-action="delete" aria-label="商品を削除する">
            <i class="bi bi-trash"></i>
        </button>

        <div class="quantity-control">
            <button type="button" class="qty-btn qty-decrease">-</button>
            <span class="quantity-display">
                <?php echo (int)$item['quantity']; ?>
            </span>
            <button type="button" class="qty-btn qty-increase">+</button>
        </div>
    </form>

    <div class="total-price">
        ¥ <?php echo number_format($item['price'] * $item['quantity']); ?>
    </div>
</div>
<?php endforeach; ?>

        <div class="cart-summary">
            <div>
                <a href="buy.php" class="btn-primary">ご購入手続きへ</a>
            </div>
        </div>
    </main>
</div>

<script src="javascript/goods.js"></script>
</body>
</html>
