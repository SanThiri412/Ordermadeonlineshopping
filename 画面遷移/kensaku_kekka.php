<?php
// PHPスクリプトの開始

// ページのタイトル
$page_title = "検索結果画面";

// ★ GETパラメータから検索クエリを取得
$search_query = isset($_GET['query']) ? $_GET['query'] : "ABC";

// --- ページネーションとデータ取得のロック ---
$items_per_page = 8;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

$total_items = 80; 
$total_pages = ceil($total_items / $items_per_page); 

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// 検索結果のダミーデータ（全80件分を生成する）
$all_results = [];
for ($i = 1; $i <= $total_items; $i++) {
    $page_num = ceil($i / $items_per_page);
    $item_on_page = $i - ($page_num - 1) * $items_per_page;
    $padded_item_num = str_pad($item_on_page, 4, '0', STR_PAD_LEFT);
    $image_path_simulated = "images/作家{$page_num}(ゴールド)/{$padded_item_num}.jpg";
    
    // ★★★ 修正点（1） ★★★
    // $item[0] ではなく、'id' と 'name' というキー（名前）を付ける
    $all_results[] = [
        'id' => $i, // 商品ID (ダミー)
        'name' => "検索結果 {$i} (P{$page_num})", // 商品名
        'image_url' => 'images\作家２(シルバー)\0014.jpg'// 画像パス
    ];
}

// 現在のページに表示する結果を抽出
$offset = ($current_page - 1) * $items_per_page;
$results = array_slice($all_results, $offset, $items_per_page);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="css/kansakukeka.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
</head>
<body>
 <?php include "header.php"; ?>
    <div class="container">
        <div class="search-box">
            <input 
                type="text" 
                class="search-input form-control" 
                value="<?php echo htmlspecialchars($search_query); ?>"
                placeholder="検索キーワード"
                disabled 
            >
        </div>

        
        <div class="results-grid">
            <?php foreach ($results as $product): ?>
            
                <a href="goods.php?id=<?php echo urlencode($product['id']); ?>" class="result-link">
                    <div class="result-item">
                        <div class="image-area">
                            
                            <img src="<?php echo htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="product-icon"> </div>
                        <div class="result-text">
                            <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <hr style="border-top: 1px solid #000; margin: 20px 0;">
        
        <div class="pagination">
            
            <?php if ($current_page > 1): ?>
                <a href="?query=<?php echo urlencode($search_query); ?>&page=<?php echo $current_page - 1; ?>" class="product-nav prev-nav">
                    <i class="bi bi-chevron-left"></i>
                </a>
            <?php else: ?>
                <span class="product-nav prev-nav disabled">
                    <i class="bi bi-chevron-left"></i>
                </span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?query=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>" 
                   class="pagination-link <?php echo ($i == $current_page) ? 'current' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?query=<?php echo urlencode($search_query); ?>&page=<?php echo $current_page + 1; ?>" class="product-nav next-nav">
                    <i class="bi bi-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="product-nav next-nav disabled">
                    <i class="bi bi-chevron-right"></i>
                </span>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>