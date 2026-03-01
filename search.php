<?php
require_once __DIR__ . '/header_init.php';

function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// GETパラメータ取得
$search_type = $_GET['search_type'] ?? 'product';
$query = trim($_GET['query'] ?? '');
$category = ($search_type === 'product') ? ($_GET['category'] ?? '') : '';
$genres = ($search_type === 'product' && isset($_GET['genre']) && is_array($_GET['genre'])) ? $_GET['genre'] : [];
$color = ($search_type === 'product') ? ($_GET['color'] ?? '') : '';

// オプション定義
$materialOptions = [
    'すべてを選択', 'シルバー', 'ゴールド', '宝石', '天然石',
    '月', '金属', '粘土', '木製', 'レジン', 'その他'
];
$colorOptions = [
    'silver' => 'シルバー', 'gold' => 'ゴールド',
    'blue' => '青', 'brown' => '茶', 'white' => '白', 'black' => '黒',
    'red' => '赤', 'orange' => 'オレンジ', 'green' => '緑',
    'pink' => 'ピンク','yellow' => '黄','purple' => '紫','ivory' => 'アイボリー','beige' => 'ベージュ','other' => 'その他の色'
];
$categoryOptions = [
    'necklace' => 'ネックレス', 'ring' => '指輪', 'pierce' => 'ピアス',
    'earring' => 'イヤリング', 'armlet' => '腕輪', 'bangle' => 'バングル',
    'bracelet' => 'ブレスレット', 'amulet' => 'アミュレット', 'kanzashi' => '簪',
    'barrette' => 'バレッタ', 'brooch' => 'ブローチ', 'necktiepin' => 'ネクタイピン', 'others' => 'その他'
];
$categoryMap = [
    'necklace' => 1, 'ring' => 2, 'pierce' => 3, 'earring' => 4,
    'armlet' => 5, 'bangle' => 6, 'bracelet' => 7, 'amulet' => 8,
    'kanzashi' => 9, 'barrette' => 10, 'brooch' => 11, 'necktiepin' => 12, 'others' => 13
];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索画面</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/search.css" rel="stylesheet">
    <style>
        .dropdown-multiselect { position: relative; margin-bottom: 1rem; }
        .dropdown-toggle { border: 1px solid #ced4da; border-radius: 0.375rem; padding: 0.375rem 0.75rem; background-color: white; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .dropdown-menu { display: none; position: absolute; background: white; border: 1px solid #ced4da; border-radius: 0.375rem; margin-top: 0.25rem; padding: 0.5rem; z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; }
        .dropdown-menu.show { display: block; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        .material-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem; cursor: pointer; }
        .material-item input[type="checkbox"] { cursor: pointer; }
    </style>
</head>
<body>
<?php include __DIR__ . '/header_display.php'; ?>

<div class="search-container">
    <form action="kensaku_kekka.php" method="get" autocomplete="off">
        <div class="search-type mb-3">
            <label>
                <input type="radio" name="search_type" value="product" id="search_product"
                    <?php echo ($search_type === 'product') ? 'checked' : ''; ?>>
                商品検索
            </label>
            <label>
                <input type="radio" name="search_type" value="artist" id="search_artist"
                    <?php echo ($search_type === 'artist') ? 'checked' : ''; ?>>
                作家検索
            </label>
        </div>

        <!-- キーワード -->
        <div class="search-input-wrapper mb-3">
            <input
                type="text"
                name="query"
                id="search_query_input"
                class="form-control"
                placeholder="<?php echo ($search_type === 'artist') ? '作家名・キーワード' : '商品名・キーワード'; ?>"
                value="<?php echo h($query); ?>"
            >
        </div>

        <!-- フィルター（商品検索用） -->
        <?php if ($search_type === 'product'): ?>
        <fieldset class="filters mb-3" id="search_filters">
            <!-- 分類 -->
            <select name="category" id="category-select" class="form-select mb-2">
                <option value="">すべての分類</option>
                <?php foreach ($categoryOptions as $key => $label): ?>
                    <option value="<?php echo h($key); ?>" <?php echo ($category === $key) ? 'selected' : ''; ?>>
                        <?php echo h($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <!-- 素材（複数選択） -->
            <div class="dropdown-multiselect">
                <div class="dropdown-toggle" id="genreDropdown">
                    <span class="dropdown-label-text" id="genreLabel">
                        <?php echo !empty($genres) ? h(implode(', ', $genres)) : 'ジャンルを選択（複数可）'; ?>
                    </span>
                    <span class="dropdown-icon">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </div>
                <div class="dropdown-menu" id="genreMenu">
                    <div class="checkbox-grid" id="material-checkbox-grid">
                        <?php foreach ($materialOptions as $mat): ?>
                            <?php $checked = (is_array($genres) && in_array($mat, $genres)) ? 'checked' : ''; ?>
                            <label class="material-item material-<?php echo h($mat); ?>">
                                <input type="checkbox" name="genre[]" value="<?php echo h($mat); ?>" <?php echo $checked; ?> autocomplete="off" />
                                <span><?php echo h($mat); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 色 -->
            <select name="color" class="form-select mb-2">
                <option value="">すべての色</option>
                <?php foreach ($colorOptions as $key => $label): ?>
                    <option value="<?php echo h($key); ?>" <?php echo ($color === $key) ? 'selected' : ''; ?>>
                        <?php echo h($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </fieldset>
        <?php endif; ?>

        <!-- 検索・リセットボタン -->
        <div class="search-button-wrapper mb-3">
            <button type="submit" class="btn btn-primary search-button" name="search">
                <?php echo ($search_type === 'artist') ? '作家���検索' : '検索'; ?>
            </button>
            <button type="reset" class="btn btn-secondary reset-button ms-2">
                リセット
            </button>
        </div>
    </form>
</div>
</body>
<script src="javascript/search.js"></script>
</html>