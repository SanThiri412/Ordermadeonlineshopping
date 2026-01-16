<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/helpers/GoodsDAO.php';

// エスケープ関数
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$colorOptions = [
    'silver' => 'シルバー', 'gold' => 'ゴールド',
    'blue' => '青', 'brown' => '茶', 'white' => '白', 'black' => '黒',
    'red' => '赤', 'orange' => 'オレンジ', 'green' => '緑',
    'pink' => 'ピンク','yellow' => '黄','purple' => '紫','ivory' => 'アイボリー','beige' => 'ベージュ','other' => 'その他の色'
];

$categoryMap = [
    'necklace' => 1, 'ring' => 2, 'pierce' => 3, 'earring' => 4,
    'armlet' => 5, 'bangle' => 6, 'bracelet' => 7, 'amulet' => 8,
    'kanzashi' => 9, 'barrette' => 10, 'brooch' => 11, 'necktiepin' => 12, 'others' => 13
];

// GETパラメータ取得
$search_type = $_GET['search_type'] ?? 'product';
$query = trim($_GET['query'] ?? '');
$category = $_GET['category'] ?? '';
$genres_raw = (isset($_GET['genre']) && is_array($_GET['genre'])) ? $_GET['genre'] : [];
$color = $_GET['color'] ?? '';

$category_id = '';
if ($category !== '' && isset($categoryMap[$category])) {
    $category_id = $categoryMap[$category];
}

// 検索フィルタ
$filters = [];
if ($search_type === 'product') {
    if ($query !== '') $filters['keyword'] = $query;
    if ($category !== '') $filters['category'] = $category;

    // ジャンル（「すべてを選択」や空欄は条件に含めない）
    if (!empty($genres_raw)) {
        $genres_filtered = array_filter($genres_raw, function($g) {
            return $g !== '' && $g !== 'すべてを選択' && $g !== 'all';
        });
        if (!empty($genres_filtered)) {
            $filters['genre'] = $genres_filtered;
        }
    }
    if ($color !== '' && $color !== 'all' && $color !== 'すべてを選択' && isset($colorOptions[$color])) {
        $filters['color'] = $colorOptions[$color];
    }
}

$sort = $_GET['sort'] ?? 'default';
$filters['sort'] = $sort;

// GoodsDAOで検索
$dao = new GoodsDAO();
$page_items = $dao->search($filters); // ← 取得漏れ修正

// ラベル
$page_title = '検索結果画面';
$active_filter_label = '全商品';
if ($category !== '') $active_filter_label = '分類: ' . h($category);
if ($color !== '') $active_filter_label .= ' 色: ' . h($colorOptions[$color] ?? $color);
if ($query !== '') $active_filter_label = 'キーワード: ' . h($query);
if (!empty($genres_raw)) $active_filter_label .= ' ジャンル: ' . implode(', ', array_map('h', $genres_raw));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="css/kensakukeka.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php include "header.php"; ?>

<div class="container">
    <div class="search-box d-flex align-items-center justify-content-between">
        <div class="filter-label h5 mb-0 text-start">
            <?php echo $active_filter_label; ?>
        </div>
    </div>
    <form method="get" class="mb-3 d-flex align-items-center">
        <label for="sort" class="me-2 fw-bold">並び順：</label>
        <select name="sort" id="sort" class="form-select d-inline-block w-auto me-2">
            <option value="default" <?php if ($sort === 'default') echo 'selected'; ?>>おすすめ順</option>
            <option value="price_asc" <?php if ($sort === 'price_asc') echo 'selected'; ?>>値段が安い順</option>
            <option value="price_desc" <?php if ($sort === 'price_desc') echo 'selected'; ?>>値段が高い順</option>
        </select>
        <button type="submit" class="btn btn-outline-primary btn-sm">並び替え</button>
        <!-- 検索条件hidden -->
        <input type="hidden" name="search_type" value="<?php echo h($search_type); ?>">
        <input type="hidden" name="category" value="<?php echo h($category); ?>">
        <input type="hidden" name="query" value="<?php echo h($query); ?>">
        <?php foreach ($genres_raw as $g): ?>
            <input type="hidden" name="genre[]" value="<?php echo h($g); ?>">
        <?php endforeach; ?>
        <input type="hidden" name="color" value="<?php echo h($color); ?>">
    </form>

    <div class="results-grid">
        <?php if (empty($page_items)): ?>
            <p>該当する商品がありませんでした。</p>
        <?php else: ?>
            <?php foreach ($page_items as $product): ?>
                <a href="goods.php?goodsCode=<?php echo urlencode($product['goodsCode']); ?>" class="result-link">
                    <div class="result-item">
                        <div class="image-area">
                            <?php
                                $imgRaw = $product['goods_image'] ?? 'no_image.png';
                                $imgUrl = 'images/' . $imgRaw;
                            ?>
                            <img src="<?php echo h($imgUrl); ?>"
                                 alt="<?php echo h($product['goodsName'] ?? ''); ?>"
                                 class="product-icon"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='images/no_image.png';">
                        </div>
                        <div class="result-text">
                            <span class="fw-bold"><?php echo h($product['goodsName'] ?? ''); ?></span>
                            <br>
                            <span style="font-size:0.9em;">¥<?php echo number_format($product['price'] ?? 0); ?></span>
                            <?php if (!empty($product['recommend'])): ?>
                                <div class="badge bg-warning text-dark mt-1" style="font-size:0.85em;">おすすめ</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

