<?php
$recommend = 0; // ← 追加
// POST処理例（実際のDB登録処理は別途実装してください）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recommend = (isset($_POST['recommend']) && $_POST['recommend'] === '1') ? 1 : 0;

    $data = [
        'goodsName' => $_POST['goodsName'],
        'bunrui_id1' => $_POST['bunrui_id'],
        'genre' => isset($_POST['genre']) ? implode(',', $_POST['genre']) : '',
        'color' => $_POST['color'],
        'size' => $_POST['size'],
        'stock' => $_POST['stock'],
        'goodsText' => $_POST['goodsText'],
        'price' => $_POST['price'],
        'recommend' => $_POST['recommend'],
        // 他の必要項目
    ];

    // DB登録処理
    $data['recommend'] = $recommend;
    $shuppinDAO->insert($data);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品出品</title>
    <link rel="stylesheet" href="css/shohinshupin.css">
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/title.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
<?php 
// カテゴリオプション（search.phpと同じに統一）
$categoryOptions = [
    'necklace' => 'ネックレス', 'ring' => '指輪', 'pierce' => 'ピアス',
    'earring' => 'イヤリング', 'armlet' => '腕輪', 'bangle' => 'バングル',
    'bracelet' => 'ブレスレット', 'amulet' => 'アミュレット', 'kanzashi' => '簪',
    'barrette' => 'バレッタ', 'brooch' => 'ブローチ', 'necktiepin' => 'ネクタイピン', 'others' => 'その他'
];
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
include "header.php"; 
?>
<div class="container mt-4 p-4 w-50 border rounded">
    <div class="hero-section text-center">
        <div class="container"></div>
    </div>
    <form action="shuppin_shohinhyoji.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="mb-3">
            <label for="goods_image" class="form-label">商品画像：</label>
            <input type="file" name="goods_image" id="goods_image" class="form-control" accept="image/*" required>
            <small class="form-text text-muted">JPG、PNG形式、最大5MBまで</small>
            <div id="preview" class="mt-2">
                <img id="imagePreview" src="" alt="プレビュー" width="120px" height="120px" style="display:none;" class="product-icon">
            </div>
        </div>
        <div class="mb-3">
            <label for="goodsName" class="form-label">商品名：</label>
            <input type="text" name="goodsName" class="form-control" placeholder="Name" required>
        </div>
        <div class="mb-3">
            <label for="bunrui_id" class="form-label">商品分類：</label>
            <select name="bunrui_id" class="form-control" required>
                <option value="">選択してください</option>
                <?php foreach ($categoryOptions as $key => $label): ?>
                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">ジャンル（素材）：</label>
            <div class="dropdown-multiselect">
                <div class="dropdown-toggle" id="genreDropdown">
                    <span class="dropdown-label-text" id="genreLabel">ジャンルを選択（複数可）</span>
                    <span class="dropdown-icon">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                </div>
                <div class="dropdown-menu" id="genreMenu">
                    <div class="checkbox-grid" id="material-checkbox-grid">
                        <?php foreach ($materialOptions as $mat): ?>
                            <label class="material-item">
                                <input type="checkbox" name="genre[]" value="<?php echo htmlspecialchars($mat); ?>">
                                <span><?php echo htmlspecialchars($mat); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="color" class="form-label">色：</label>
            <select name="color" class="form-control" required>
                <option value="">選択してください</option>
                <?php foreach ($colorOptions as $key => $label): ?>
                    <option value="<?php echo htmlspecialchars($label); ?>"><?php echo htmlspecialchars($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="size" class="form-label">サイズ：</label>
            <input type="text" name="size" class="form-control" placeholder="例: 16">
        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">在庫数：</label>
            <input type="number" name="stock" class="form-control" placeholder="Stock Quantity" min="1" value="1" required>
        </div>
        <div class="mb-3">
            <label for="goodsText" class="form-label">商品情報：</label>
            <input type="text" name="goodsText" class="form-control" placeholder="Information">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">値段：</label>
            <input type="number" name="price" class="form-control" placeholder="Price" min="0" required>
        </div>
        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" name="recommend" id="recommend" value="1" <?php if($recommend == 1) echo 'checked'; ?>>
            <label class="form-check-label" for="recommend">おすすめに設定する</label>
        </div>
        <div class="d-grid gap-2 col-6 mx-auto">
            <button type="submit" class="container btn btn-primary">出品</button>
            <a href="shuppin_shohinhyoji.php" class="container btn btn-secondary mt-2">キャンセル</a>
        </div>
    </form>
</div>
</body>
</html>
<script src="javascript/shohin_shuppin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>