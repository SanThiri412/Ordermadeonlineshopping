<?php
require_once __DIR__ . '/header_init.php';
require_once __DIR__ . '/helpers/OkiniiriDAO.php';

// ログイン確認
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$dao = new OkiniiriDAO();
$favorites = $dao->get_favorites($member->member_id);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り</title>
    <link href="css/okiniiri.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php include __DIR__ . '/header_display.php'; ?>

<div class="container mt-4">
    <h1>お気に入り</h1>
    
    <?php if (empty($favorites)): ?>
        <p>お気に入りの商品はありません。</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($favorites as $item): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="images/<?php echo htmlspecialchars($item['goods_image'] ?? 'no_image.png', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="商品画像">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['goodsName'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="card-text">¥<?php echo number_format($item['price'] ?? 0); ?></p>
                            <a href="goods.php?goodsCode=<?php echo urlencode($item['goodsCode']); ?>" class="btn btn-primary">詳細</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>