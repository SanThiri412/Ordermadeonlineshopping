<?php
// セッション開始
if (session_status() === PHP_SESSION_NONE) session_start();

// 出力エスケープ関数
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// セッションから会員IDを取得（複数キーに対応）
function getMemberNo() {
    if (isset($_SESSION['member'])) {
        $member = $_SESSION['member'];
        $memberArray = is_object($member) ? (array)$member : $member;
        foreach (['member_no', 'member_id', 'memberId', 'memberNo', "\0*\0member_no", "\0*\0member_id"] as $key) {
            if (isset($memberArray[$key])) return $memberArray[$key];
        }
    }
    foreach (['member_no', 'memberId'] as $key) {
        if (isset($_SESSION[$key])) return $_SESSION[$key];
    }
    return null;
}

// URLエンコード関数（日本語・スペース対応）
function url_path($p) {
    return '/' . implode('/', array_map('rawurlencode', explode('/', $p)));
}

// DAO 初期化
require_once __DIR__ . '/helpers/GoodsDAO.php';
require_once __DIR__ . '/helpers/DAO.php';
$goodsDao = new GoodsDAO();

$unit_price = 80000;
$stock = 0;
$images_raw = [
    'images/作家５(宝石)/0060.jpg',
    'images/作家５(宝石)/0061.jpg',
    'images/作家６(面白)/0067.jpg',
];
$product_name = '天然石指輪（14K Gold）';
$artist_info = 'Miyabi Handmade';
$description_text = '自然な美しさを持つローズクォーツを使用したハンドメイドリングです。金属アレルギー対応。';
$isFavorited = false;
$product = null;
$message = '';

// goodsCode パラメータを取得（大文字・小文字両対応）
$goodsCode = isset($_GET['goodsCode']) ? trim($_GET['goodsCode']) : (isset($_GET['goodscode']) ? trim($_GET['goodscode']) : '');

// 分類IDの表示用マップ
$category_display_map = [
    1 => 'ネックレス', 2 => '指輪',3 => 'ピアス',4 => 'イヤリング',
     5 => '腕輪',6 => 'バングル',7 => 'ブレスレット',8 => 'アミュレット',
    9 => '簪',10 => 'バレッタ',11 => 'ブローチ',12 => 'ネクタイピン', 13 => 'その他'
];

// サイズ選択ルール
$size_whitelist = [1, 4, 13, 15, 21, 24, 31, 34, 36, 37, 45, 47, 52, 54, 55, 60, 61, 80, 83, 84, 87, 96, 98, 99, 102];

// お気に入りトグル処理（POST）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorite'])) {
    $postGoods = $_POST['goodsCode'] ?? '';
    $memberNo = getMemberNo();
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($postGoods === '' || $memberNo === null) {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            $return = urlencode($_SERVER['REQUEST_URI']);
            echo json_encode(['status' => 'login_required', 'login_url' => 'login.php?return=' . $return]);
            exit;
        }
        if (!$memberNo) {
            header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI']));
        exit;
    }

    // お気に入りトグル処理
    try {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT COUNT(*) AS cnt FROM Favorite_goods WHERE member_id = :m AND favorite_goodsCode = :g";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':m', (int)$memberNo, PDO::PARAM_INT);
        $stmt->bindValue(':g', $postGoods, PDO::PARAM_STR);
        $stmt->execute();
        $favorited = (int)($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;

        if ($favorited) {
            // 削除
            $del = $dbh->prepare("DELETE FROM Favorite_goods WHERE member_id = :m AND favorite_goodsCode = :g");
            $del->bindValue(':m', (int)$memberNo, PDO::PARAM_INT);
            $del->bindValue(':g', $postGoods, PDO::PARAM_STR);
            $del->execute();
            $favorited = false;
        } else {
            // 追加
            $ins = $dbh->prepare("INSERT INTO Favorite_goods (member_id, favorite_goodsCode) VALUES (:m, :g)");
            $ins->bindValue(':m', (int)$memberNo, PDO::PARAM_INT);
            $ins->bindValue(':g', $postGoods, PDO::PARAM_STR);
            $ins->execute();
            $favorited = true;
        }
    } catch (Exception $e) {
        error_log('Favorite_goods toggle error: ' . $e->getMessage());
        $favorited = false;
    }

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'ok', 'favorited' => $favorited]);
        exit;
    }

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI']));
    exit;
}

// ログイン状態でお気に入り状態を取得
$memberNo = getMemberNo();
if ($memberNo !== null) {
    try {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT COUNT(*) AS cnt FROM favorite WHERE member_no = :m AND goods_code = :g";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':m', $memberNo);
        $stmt->bindValue(':g', $goodsCode);
        $stmt->execute();
        $isFavorited = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
    } catch (Exception $e) {
        error_log('favorite read error: ' . $e->getMessage());
    }
}

// DB から商品情報を取得
$artist_member_id = null;
try {
    $product = $goodsDao->findByCode($goodsCode);
    if ($product) {
        $artist_info = $product['nickName'] ?? $product['artist_nickname'] ?? $product['artist_name'] ?? $product['artist'] ?? $artist_info;
        $artist_member_id = (int)($product['member_id'] ?? $artist_member_id);
        $product_name = $product['goodsName'] ?? $product_name;
        $description_text = $product['goodsText'] ?? $description_text;
        $unit_price = (int)($product['price'] ?? $unit_price);
        $stock = isset($product['stock']) ? (int)$product['stock'] : $stock;
        $images_raw = $goodsDao->getImages($goodsCode) ?: $images_raw;
    } else {
        $message = '商品データが見つかりません。';
    }
} catch (Exception $e) {
    $message = '商品情報の取得中にエラーが発生しました。';
}

// 画像配列が空の場合はデフォルトを設定
if (empty($images_raw)) $images_raw = ['作家５(宝石)/0060.jpg'];
$images = array_map('url_path', $images_raw);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($product_name); ?> | SELARG</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/goods.css">
    <link rel="stylesheet" href="css/background.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="browser-frame">
    <div class="browser-bar"></div>
    <main id="product-root"
          class="product-container"
          data-unit-price="<?php echo (int)$unit_price; ?>"
          data-stock="<?php echo (int)$stock; ?>">

        <section class="product-image-section">
            <div class="product-image-carousel" id="carousel">
                <?php
                    // 画像の最初のものを取得
                    $baseImg = !empty($images_raw) ? $images_raw[0] : ($product['goods_image'] ?? 'no_image.png');
                    $imgUrl = 'images/' . $baseImg;
                ?>
                <img id="carousel-image"
                     src="<?php echo h($imgUrl); ?>"
                     alt="商品画像"
                     onerror="this.onerror=null; this.src='images/no_image.png';"
                     loading="lazy">

                <!-- お気に入り（画像上オーバーレイ） -->
                <div class="favorite-overlay">
                    <form method="post" action="" class="fav-form-overlay">
                        <input type="hidden" name="goodsCode" value="<?php echo h($goodsCode); ?>">
                        <input type="hidden" name="toggle_favorite" value="1">
                        <button type="submit"
                                class="favorite-overlay-btn"
                                aria-pressed="<?php echo $isFavorited ? 'true' : 'false'; ?>"
                                title="お気に入りに追加">
                            <i class="bi <?php echo $isFavorited ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                            <span class="visually-hidden">
                                <?php echo $isFavorited ? 'お気に入りを解除' : 'お気に入りに追加'; ?>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- 商品詳細 -->
        <section class="product-details-section">
            <h1 class="product-name"><?php echo h($product_name); ?></h1>

            <p class="artist-info">
                <a href="explanation.php?artist_id=<?php echo (int)$artist_member_id; ?>">
                    <?php echo h($artist_info); ?>
                </a>
            </p>

            <p class="description">
                <?php echo nl2br(h($description_text)); ?>
            </p>

            <!-- 追加情報（ダミー。必要に応じて DB から取得してもOK） -->
            <?php
            $display_category = $category_display_map[(int)($product['bunrui_id1'] ?? 0)] ?? '';
            $display_genre    = (string)($product['genre'] ?? '');
            $display_color    = (string)($product['color'] ?? '');
            ?>
            <table class="table table-sm mb-3">
                <tbody>
                <tr>
                    <th class="w-25">分類</th><td><?php echo h($display_category); ?></td>
                </tr>
                <tr>
                    <th>ジャンル</th><td><?php echo h($display_genre); ?></td>
                </tr>
                <tr>
                    <th>色</th><td><?php echo h($display_color); ?></td>
                </tr>
                <?php if (!empty($product['size']) && is_numeric($product['size'])): ?>
                <tr>
                    <th>サイズ</th>
                    <td><?php echo h($product['size']); ?></td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- 在庫表示 -->
            <div class="stock-info mb-3">
                <?php if ($stock > 0): ?>
                    <strong>在庫：</strong><?php echo (int)$stock; ?> 個
                <?php else: ?>
                    <strong>在庫：</strong><span class="text-danger">売り切れ</span>
                <?php endif; ?>
            </div>

            <!-- カート追加フォーム -->
            <form action="cart.php" method="post">
                <input type="hidden" name="goodsCode" value="<?php echo h($goodsCode); ?>">
                <input type="hidden" name="quantity"  id="quantity-input" value="1">

                <div class="options-section">

                    <?php
                    $bunrui_id = (int)($product['bunrui_id1'] ?? 0);
                    $goodsCodeInt = (int)$goodsCode;
                    $showSize = in_array($goodsCodeInt, $size_whitelist);

                    // sizeが数字ならその値だけを選択肢として表示
                    if (!empty($product['size']) && is_numeric($product['size'])): ?>
                        <div class="option-group">
                            <label for="size">サイズ選択</label>
                            <select id="size" name="size" class="form-select">
                                <option value="<?php echo h($product['size']); ?>">
                                    <?php echo h($product['size']); ?>
                                </option>
                            </select>
                        </div>
                    <?php
                    // 通常のサイズ選択ルール
                    elseif ($showSize):
                    ?>
                        <div class="option-group">
                            <label for="size">サイズ選択</label>
                            <select id="size" name="size" class="form-select">
                                <?php
                                if ($bunrui_id == 1):
                                    for ($i = 35; $i <= 55; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?>cm</option>
                                    <?php endfor;
                                elseif (in_array($bunrui_id, [5, 6])):
                                    for ($i = 14; $i <= 18; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?>cm</option>
                                    <?php endfor;
                                else:
                                    for ($i = 9; $i <= 23; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?>号</option>
                                    <?php endfor;
                                endif;
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="quantity-control">
                        <button type="button" id="decrease-btn" class="decrease-btn"> - </button>
                        <span class="quantity-display" id="quantity">1</span>
                        <button type="button" id="increase-btn" class="increase-btn"> + </button>
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

                    <button type="submit" class="btn-primary"
                        <?php if ($stock <= 0) echo ' disabled'; ?>>
                        <?php echo $stock <= 0 ? '売り切れ' : 'カートに追加'; ?>
                    </button>
                </div>
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
<script src="javascript/favorite.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
