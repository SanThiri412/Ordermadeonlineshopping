<?php
// ==========================
// 検索パラメータ取得
// ==========================
$search_type = isset($_GET['search_type']) ? htmlspecialchars($_GET['search_type'], ENT_QUOTES, 'UTF-8') : 'product';
$query       = isset($_GET['query']) ? htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8') : '';

$category = '';
$genres   = [];
$color    = '';

if ($search_type === 'product') {
    $category = isset($_GET['category']) ? htmlspecialchars($_GET['category'], ENT_QUOTES, 'UTF-8') : '';

    if (isset($_GET['genre']) && is_array($_GET['genre'])) {
        $genres = array_map(function ($g) {
            return htmlspecialchars($g, ENT_QUOTES, 'UTF-8');
        }, $_GET['genre']);
    }

    $color = isset($_GET['color']) ? htmlspecialchars($_GET['color'], ENT_QUOTES, 'UTF-8') : '';
}

// 素材一覧
$materialOptions = [
    'シルバー',
    'ゴールド',
    'プラチナ',
    'チタン',
    'ステンレス',
    '真鍮',
    'その他金属',
    'プラスチック・アクリル',
    'レザー・本革',
    'ウッド・ビーズ',
    'コットン・麻'
];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索画面</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/search.css" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>

<div class="search-container">
    <form action="kensaku_kekka.php" method="get">
        <!-- 検索種別 -->
        <div class="search-type">
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
        <div class="search-input-wrapper">
            <input
                type="text"
                name="query"
                id="search_query_input"
                placeholder="<?php echo ($search_type === 'artist') ? '作家名・キーワード' : '商品名・キーワード'; ?>"
                value="<?php echo $query; ?>"
            >
            <span class="search-icon"></span>
        </div>

        <!-- フィルター（商品検索用） -->
        <fieldset class="filters" id="search_filters">
            <!-- 分類 -->
            <select name="category">
                <option value="">すべての分類</option>
                <option value="accessory" <?php echo ($category === 'accessory') ? 'selected' : ''; ?>>ネックレス</option>
                <option value="fashion"   <?php echo ($category === 'fashion')   ? 'selected' : ''; ?>>指輪</option>
                <option value="interior"  <?php echo ($category === 'interior')  ? 'selected' : ''; ?>>ピアス・イヤリング</option>
                <option value="jewelry"   <?php echo ($category === 'jewelry')   ? 'selected' : ''; ?>>腕輪</option>
                <option value="stone"     <?php echo ($category === 'stone')     ? 'selected' : ''; ?>>ブローチ</option>
                <option value="metal"     <?php echo ($category === 'metal')     ? 'selected' : ''; ?>>金属製品</option>
                <option value="leather"   <?php echo ($category === 'leather')   ? 'selected' : ''; ?>>革製品</option>
                <option value="others"    <?php echo ($category === 'others')    ? 'selected' : ''; ?>>その他</option>
            </select>

            <!-- 素材（ドロップダウン＋複数選択：標準チェックのみ） -->
            <div class="dropdown-multiselect">
                <div class="dropdown-toggle" id="genreDropdown">
                    <span class="dropdown-label-text" id="genreLabel">
                        <?php
                        if (!empty($genres)) {
                            echo implode(', ', $genres);
                        } else {
                            echo 'ジャンルを選択（複数可）';
                        }
                        ?>
                    </span>
                    <span class="dropdown-icon">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </div>

                <div class="dropdown-menu" id="genreMenu">
                    <div class="checkbox-grid">
                        <?php foreach ($materialOptions as $mat): ?>
                            <?php $checked = in_array($mat, $genres, true) ? 'checked' : ''; ?>
                            <label class="material-item">
                                <input type="checkbox" name="genre[]" value="<?php echo $mat; ?>" <?php echo $checked; ?>>
                                <span><?php echo $mat; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 色 -->
            <select name="color">
                <option value="">すべての色</option>
                <option value="red"    <?php echo ($color === 'red')    ? 'selected' : ''; ?>>赤</option>
                <option value="blue"   <?php echo ($color === 'blue')   ? 'selected' : ''; ?>>青</option>
                <option value="green"  <?php echo ($color === 'green')  ? 'selected' : ''; ?>>緑</option>
                <option value="gold"   <?php echo ($color === 'gold')   ? 'selected' : ''; ?>>ゴールド</option>
                <option value="silver" <?php echo ($color === 'silver') ? 'selected' : ''; ?>>シルバー</option>
                <option value="white"  <?php echo ($color === 'white')  ? 'selected' : ''; ?>>白</option>
                <option value="black"  <?php echo ($color === 'black')  ? 'selected' : ''; ?>>黒</option>
            </select>
        </fieldset>

        <!-- 検索ボタン -->
        <div class="search-button-wrapper">
            <button type="submit" class="btn-primary" name="search">
                <?php echo ($search_type === 'artist') ? '作家を検索' : '検索'; ?>
            </button>
        </div>
    </form>
</div>

<script src="javascript/search.js"></script>
</body>
</html>
