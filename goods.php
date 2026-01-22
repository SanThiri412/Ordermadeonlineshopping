<?php
// 単価
$unit_price = 80000;

// 商品画像（元のファイルパス）※スラッシュ / を使用
$images_raw = [
    'images/作家５(宝石)/0060.jpg',
    'images/作家５(宝石)/0061.jpg',
    'images/作家６(面白)/0067.jpg',
];

// 日本語・スペースなどを含むパスをURLエンコード
function url_path($p) {
    $parts = explode('/', $p);
    $parts = array_map('rawurlencode', $parts);
    return implode('/', $parts);
}
$images = array_map('url_path', $images_raw);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>天然石リング | SELARG</title>

    <!-- Bootstrap / アイコン -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- 自作CSS -->
    <link rel="stylesheet" href="css/goods.css">
    <link rel="stylesheet" href="css/background.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="browser-frame">
    <div class="browser-bar"></div>

    <main id="product-root" class="product-container" data-unit-price="<?php echo (int)$unit_price; ?>">

        <!-- 画像スライダー -->
        <section class="product-image-section">
            <div class="product-image-carousel" id="carousel">
                <!-- 画像（常にここ1枚。JSで差し替え） -->
                <img id="carousel-image"
                     src="<?php echo htmlspecialchars($images[0], ENT_QUOTES, 'UTF-8'); ?>"
                     alt="商品画像">

                <!-- 矢印ナビ（画像の上に重ねる） -->
                <div class="carousel-nav">
                    <button id="prev-btn" type="button" aria-label="前の画像へ">&lt;</button>
                    <button id="next-btn" type="button" aria-label="次の画像へ">&gt;</button>
                </div>

                <!-- ドットナビ -->
                <div class="carousel-dots" id="carousel-dots"></div>
            </div>
        </section>

        <!-- 商品詳細 -->
        <section class="product-details-section">
            <h1 class="product-name">天然石指輪（14K Gold）</h1>
            <p class="artist-info"><a href="explanation.php">Miyabi Handmade</a></p>
            <div class="rating"><span>★★★★☆</span>（4/5）</div>
            <p class="description">
                自然な美しさを持つローズクォーツを使用したハンドメイドリングです。金属アレルギー対応。
            </p>

            <!-- 追加情報 -->
            <table class="table table-sm mb-3">
                <tbody>
                <tr>
                    <th class="w-25">分類</th><td>リング</td>
                </tr>
                <tr>
                    <th>ジャンル</th><td>14K Gold</td>
                </tr>
                <tr>
                    <th>色</th><td>ローズピンク</td>
                </tr>
                </tbody>
            </table>

            <form action="#" method="post">
                <div class="options-section">

                    <div class="option-group">
                        <label for="size">サイズ選択</label>
                        <select id="size" name="size" class="form-select">
                            <?php for ($i = 1; $i <= 28; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>号</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                   <div class="quantity-control">
                         <button type="button" id="decrease-btn" class="decrease-btn"> - </button>
                         <span class="quantity-display" id="quantity"> 1</span>
                        <button type="button" id="increase-btn"class="increase-btn"> + </button>
                    </div>


                    <div class="option-group">
                        <div class="price-display">
                            合計：<span id="total-price">¥ <?php echo number_format($unit_price); ?></span>
                        </div>
                    </div>

                    <div class="option-group">
                        作家へ要望（任意）
                        <textarea id="message" name="message"
                                  placeholder="例）納期やサイズの希望などをご記入ください"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mt-3"><a href="cart.php">カートに追加</button>
            </form>
        </section>
    </main>
</div>

<!-- PHPで作った画像リストを JS に渡す（必ず goods.js より前） -->
<script>
    window.PRODUCT_IMAGES = <?php echo json_encode($images, JSON_UNESCAPED_SLASHES); ?>;
</script>

<!-- スライダー・数量計算などのJS（別ファイル） -->
<script src="javascript/goods.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
