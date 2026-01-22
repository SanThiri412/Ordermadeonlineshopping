<?php
    
    $recommended_products = [
        // 実際の画像ファイルパスに置き換えてください
        ['goodsCode'=>'R001','goodsName' => 'おすすめ商品A', 'goods_image' => 'images\作家２(シルバー)\0014.jpg'], 
        ['goodsCode'=>'R002','goodsName' => 'おすすめ商品B', 'goods_image' => 'images\作家２(シルバー)\0015.jpg'],
        ['goodsCode'=>'R003','goodsName' => 'おすすめ商品C', 'goods_image' => 'images\作家２(シルバー)\0016.jpg'],
        ['goodsCode'=>'R004','goodsName' => 'おすすめ商品D', 'goods_image' => 'images\作家２(シルバー)\0017.jpg'],
        ['goodsCode'=>'R005','goodsName' => 'おすすめ商品E', 'goods_image' => 'images\作家２(シルバー)\0018.jpg'],
        ['goodsCode'=>'R006','goodsName' => 'おすすめ商品F', 'goods_image' => 'images\作家２(シルバー)\0019.jpg'],
    ];

    // 出品商品 (おすすめ商品と同じ構造を使用)
    $exhibited_products = [
        // 実際の画像ファイルパスに置き換えてください
        ['goodsCode'=>'E001','goodsName' => '出品商品1', 'goods_image' => 'images/作家３(シルバー)/0023.jpg'],
        ['goodsCode'=>'E002','goodsName' => '出品商品2', 'goods_image' => 'images/作家３(シルバー)/0024.jpg'],
        ['goodsCode'=>'E003','goodsName' => '出品商品3', 'goods_image' => 'images/作家３(シルバー)/0025.jpg'],
        ['goodsCode'=>'E004','goodsName' => '出品商品4', 'goods_image' => 'images/作家３(シルバー)/0026.jpg'],
        ['goodsCode'=>'E005','goodsName' => '出品商品5', 'goods_image' => 'images/作家３(シルバー)/0027.jpg'],
        ['goodsCode'=>'E006','goodsName' => '出品商品6', 'goods_image' => 'images/作家３(シルバー)/0028.jpg'],
    ];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/Explanationforstyle.css" rel="stylesheet"> 
    <title>会員詳細ページ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    </head>
<body>
    <?php  include "header.php";  ?>
   
    <main class="container my-4">
        <div class="main-content">
            <div class="row">
                <div class="col-md-2 d-flex justify-content-center">
                    <div class="text-center">
                        <i class="profile-icon">
                            <img src="images/Icons/user.png" alt="profile" class="img-fluid" style="width: 80px; height: 80px;">
                        </i>
                    </div>
                </div>

                <div class="col-md-10">
                    <section class="intro-box">
                        <h2>自己紹介</h2>
                    </section>

                    <section class="recommendations mb-5 position-relative">
                        <h3>おすすめ商品</h3>
                        <i class="bi bi-chevron-left product-nav prev-nav"></i>
                        <div class="row g-3">
                            <?php foreach ($recommended_products as $product): ?>
                                <div class="col-6 col-md-3">
                                    <div class="product-card">
                                        <div class="product-image-placeholder">
                                            <a href="goods.php?goodsCode=<?= urlencode($product['goodsCode']) ?>">
                                               <img src="<?php echo htmlspecialchars($product['goods_image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                                    alt="<?php echo htmlspecialchars($product['goodsName'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        class="product-img">
                                            </a>
                                        </div>
                                        <p><?php echo htmlspecialchars($product['goodsName'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <i class="bi bi-chevron-right product-nav next-nav"></i>
                    </section>
                    
                    <section class="exhibited-products position-relative">
                        <h3>出品商品</h3>
                        <i class="bi bi-chevron-left product-nav prev-nav"></i>
                        <div class="row g-3">
                            <?php foreach ($exhibited_products as $product): ?>
                                <div class="col-6 col-md-3">
                                    <div class="product-card">
                                        <div class="product-image-placeholder">
                                            <a href="goods.php?goodsCode=<?= urlencode($product['goodsCode']) ?>">
                                                <img src="<?php echo htmlspecialchars($product['goods_image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                                    alt="<?php echo htmlspecialchars($product['goodsName'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        class="product-img">
                                            </a>
                                        </div>
                                        <p><?php echo htmlspecialchars($product['goodsName'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <i class="bi bi-chevron-right product-nav next-nav"></i>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // おすすめ商品のナビゲーション設定
    const recSection = document.querySelector('.recommendations');
    const recContainer = recSection.querySelector('.row.g-3');
    const recPrev = recSection.querySelector('.prev-nav');
    const recNext = recSection.querySelector('.next-nav');

    // 出品商品のナビゲーション設定
    const exhSection = document.querySelector('.exhibited-products');
    const exhContainer = exhSection.querySelector('.row.g-3');
    const exhPrev = exhSection.querySelector('.prev-nav');
    const exhNext = exhSection.querySelector('.next-nav');
    
    // スクロール処理を実行する関数
    function attachScroll(container, prevBtn, nextBtn) {
        if (!container || !prevBtn || !nextBtn) return;

        // 矢印がクリックされたら、コンテナを1つ分の商品幅だけスクロールさせる
        const scrollAmount = container.querySelector('.col-md-3') ? 
                             container.querySelector('.col-md-3').offsetWidth * 4 : 
                             300; // 適切なスクロール量

        prevBtn.addEventListener('click', function() {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function() {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        // 商品を横スクロール可能にするCSSを適用 (後述のCSSを必ず追加)
        container.style.overflowX = 'scroll';
        container.style.flexWrap = 'nowrap';
        container.style.paddingBottom = '1rem'; // スクロールバーのためのスペース
    }

    // 両方のセクションに機能を適用
    attachScroll(recContainer, recPrev, recNext);
    attachScroll(exhContainer, exhPrev, exhNext);
});
</script>
</body>
</html>