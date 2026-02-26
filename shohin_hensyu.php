<?php
session_start();
require_once __DIR__ . '/helpers/GoodsDAO.php';
require_once __DIR__ . '/helpers/ShuppinDAO.php';

// ログインチェック
function getMemberId(): ?int {
    if (isset($_SESSION['MEMBER_ID']) && $_SESSION['MEMBER_ID'] > 0) {
        return (int)$_SESSION['MEMBER_ID'];
    }
    
    $containers = ['member', 'loginMember', 'user'];
    $idKeys = ['member_id', 'id'];
    
    foreach ($containers as $container) {
        if (!isset($_SESSION[$container])) continue;
        $data = $_SESSION[$container];
        
        foreach ($idKeys as $key) {
            $value = is_object($data) ? ($data->$key ?? null) : ($data[$key] ?? null);
            if ($value && (int)$value > 0) return (int)$value;
        }
    }
    
    foreach ($idKeys as $key) {
        if (isset($_SESSION[$key]) && (int)$_SESSION[$key] > 0) {
            return (int)$_SESSION[$key];
        }
    }
    
    return null;
}

$member_id = getMemberId();
if (!$member_id) {
    header('Location: login.php'); 
    exit;
}

$goodsDAO = new GoodsDAO();
$shuppinDAO = new ShuppinDAO();
$goodsCode = (int)($_GET['goodsCode'] ?? 0);

if ($goodsCode <= 0) {
    header('Location: shuppin_shohinhyoji.php'); 
    exit;
}

$item = $shuppinDAO->findOneForOwner($goodsCode, $member_id);
if (!$item) {
    header('Location: shuppin_shohinhyoji.php'); 
    exit;
}

$colorOptions = [
    'silver' => 'シルバー', 'gold' => 'ゴールド',
    'blue' => '青', 'brown' => '茶', 'white' => '白', 'black' => '黒',
    'red' => '赤', 'orange' => 'オレンジ', 'green' => '緑',
    'pink' => 'ピンク', 'ivory' => 'アイボリー',
    'beige' => 'ベージュ', 'yellow' => '黄', 'purple' => '紫'
];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['goodsName'])) throw new Exception('商品名は必須です');
        if (!isset($_POST['price']) || !is_numeric($_POST['price'])) throw new Exception('価格が不正です');
        if (!isset($_POST['stock']) || !is_numeric($_POST['stock'])) throw new Exception('在庫数が不正です');

        // サイズ処理: フリーサイズや空の場合はNULL、それ以外は数字のみ
        $size_input = trim($_POST['size'] ?? '');
        $size_value = null;
        if ($size_input !== '' && !preg_match('/フリー|free/iu', $size_input)) {
            $size_numeric = preg_replace('/[^0-9.]/', '', $size_input);
            if ($size_numeric !== '') {
                $size_value = $size_numeric;
            }
        }
        
        $data = [
            'goodsName'  => $_POST['goodsName'],
            'price'      => (int)$_POST['price'],
            'goodsText'  => $_POST['goodsText'] ?? '',
            'stock'      => (int)$_POST['stock'],
            'recommend'  => isset($_POST['recommend']) ? 1 : 0, // ←ここを修正
            'size'       => $size_value,
            'sellFlag'   => isset($_POST['sellFlag']) ? (int)$_POST['sellFlag'] : 1,
            'color'      => $_POST['color'] ?? '',
        ];
        
        // 画像アップロード処理
        if (isset($_FILES['goods_image']) && $_FILES['goods_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/images/goods/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $ext = strtolower(pathinfo($_FILES['goods_image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('画像ファイルはJPG、PNG、GIFのみ対応しています');
            }
            
            $newFileName = 'goods_' . $goodsCode . '_' . time() . '.' . $ext;
            
            if (!move_uploaded_file($_FILES['goods_image']['tmp_name'], $uploadDir . $newFileName)) {
                throw new Exception('画像のアップロードに失敗しました');
            }
            
            $data['goods_image'] = $newFileName;
        }
        
        if (!$shuppinDAO->updateForOwner($goodsCode, $member_id, $data)) {
            throw new Exception('更新に失敗しました');
        }
        
        header('Location: shuppin_shohinhyoji.php?updated=1');
        exit;

    } catch (Exception $e) {
        $message = '更新エラー: ' . $e->getMessage();
        $item = $shuppinDAO->findOneForOwner($goodsCode, $member_id) ?? [];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品編集</title>
    <link href="css/background.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>
<div class="container mt-4 p-4 w-75 border rounded">
    <h2 class="mb-4"><i class="bi bi-pencil-square"></i> 商品編集</h2>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="shohin_hensyu.php?goodsCode=<?php echo (int)$goodsCode; ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">現在の画像</label>
            <?php if (!empty($item->goods_image)): ?>
                <img src="images/<?php echo htmlspecialchars($item->goods_image, ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="現在の商品画像" 
                     style="max-width: 200px; height: auto;"
                     onerror="this.onerror=null; this.src='images/no_image.png'">
            <?php else: ?>
                <p class="text-muted">画像未設定</p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">新しい画像をアップロード</label>
            <input type="file" name="goods_image" class="form-control" accept="image/jpeg,image/png,image/gif">
            <small class="form-text text-muted">JPG、PNG、GIF形式に対応（最大サイズ: 5MB）</small>
        </div>

        <div class="mb-3">
            <label class="form-label">商品名</label>
            <input type="text" name="goodsName" class="form-control" value="<?php echo htmlspecialchars($item->goodsName ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">価格</label>
            <input type="number" name="price" class="form-control" value="<?php echo (int)($item->price ?? 0); ?>" required min="1">
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">色</label>
            <select name="color" class="form-control">
                <option value="">選択してください</option>
                <?php foreach ($colorOptions as $key => $label): 
                    $selected = (isset($item->color) && $item->color == $label) ? 'selected' : '';
                ?>
                    <option value="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">商品詳細</label>
            <textarea name="goodsText" class="form-control" rows="4"><?php echo htmlspecialchars($item->goodsText ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">在庫数</label>
            <input type="number" name="stock" class="form-control" value="<?php echo (int)($item->stock ?? 0); ?>" required min="0">
        </div>

        <div class="mb-3">
            <label class="form-label">販売状態</label>
            <select name="sellFlag" class="form-select" required>
                <option value="1" <?php echo (isset($item->sellFlag) && $item->sellFlag == 1) ? 'selected' : ''; ?>>販売中</option>
                <option value="0" <?php echo (isset($item->sellFlag) && $item->sellFlag == 0) ? 'selected' : ''; ?>>販売終了</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="recommend" class="form-check-input" id="recommend" value="1" <?php echo (isset($item->recommend) && $item->recommend) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="recommend">おすすめ商品としてマークする</label>
        </div>

        <div class="mb-3">
            <label class="form-label">サイズ</label>
            <input type="text" name="size" class="form-control" value="<?php echo htmlspecialchars($item->size ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="例: 16">
        </div>

        <div class="d-grid gap-2 col-6 mx-auto mt-4">
            <button type="submit" class="btn btn-primary">更新する</button>
            <a href="shuppin_shohinhyoji.php" class="btn btn-secondary">キャンセル</a>
        </div>
    </form>
</div>
</body>
</html>