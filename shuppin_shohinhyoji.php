<?php
// デバッグ用：エラーを画面に表示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start() は必ずファイルの先頭、requireの前に置く（BOMや空白行も不可）
session_start();

require_once __DIR__ . '/helpers/GoodsDAO.php';
require_once __DIR__ . '/helpers/MemberDAO.php';
require_once __DIR__ . '/helpers/DAO.php';
require_once __DIR__ . '/helpers/ShuppinDAO.php';

// --------------------------------------------------------------------------------
// 🌟 修正ポイント: ログイン認証の確実性を向上
// --------------------------------------------------------------------------------
function getMemberId(): ?int {
    if (empty($_SESSION)) return null;

    // 1. 確実な固定キーを最優先でチェックする（※ログイン処理でこのキーにIDをセットすること）
    if (isset($_SESSION['MEMBER_ID']) && (int)$_SESSION['MEMBER_ID'] > 0) {
        return (int)$_SESSION['MEMBER_ID'];
    }

    // 2. 既存のフォールバックロジック
    $containers = ['member','loginMember','user','account','loginUser'];
    $idKeys = ['member_id','id','memberNo','user_id','memberNo2','memberId'];

    // コンテナオブジェクト/配列から探索
    foreach ($containers as $ck) {
        if (!isset($_SESSION[$ck])) continue;
        $m = $_SESSION[$ck];
        if (is_object($m)) {
            foreach ($idKeys as $k) {
                if (isset($m->$k) && (int)$m->$k > 0) return (int)$m->$k;
            }
        } elseif (is_array($m)) {
            foreach ($idKeys as $k) {
                if (isset($m[$k]) && (int)$m[$k] > 0) return (int)$m[$k];
            }
        }
    }
    // 直置きIDも探索
    foreach ($idKeys as $k) {
        if (isset($_SESSION[$k]) && (int)$_SESSION[$k] > 0) return (int)$_SESSION[$k];
    }
    return null;
}
// --------------------------------------------------------------------------------

$member_id = getMemberId();
if (!$member_id) {
    header('Location: login.php');
    exit;
}

$goodsDAO = new GoodsDAO();
$shuppinDAO = new ShuppinDAO();
$message = '';
$message = '';

$bunrui_map = [
    'necklace'   => 1,
    'ring'       => 2,
    'pierce'     => 3,
    'earring'    => 4,
    'armlet'     => 5,
    'bangle'     => 6,
    'bracelet'   => 7,
    'amulet'     => 8,
    'kanzashi'   => 9,
    'barrette'   => 10,
    'brooch'     => 11,
    'necktiepin' => 12,
    'others'     => 13
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goodsName'])) {
    try {
        $temp_image_path = '';
        $image_ext = '';
        
        // 画像を一時保存
        if (isset($_FILES['goods_image']) && $_FILES['goods_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/images/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $image_ext = pathinfo($_FILES['goods_image']['name'], PATHINFO_EXTENSION);
            $temp_filename = 'temp_' . uniqid() . '.' . $image_ext;
            
            if (move_uploaded_file($_FILES['goods_image']['tmp_name'], $upload_dir . $temp_filename)) {
                $temp_image_path = $upload_dir . $temp_filename;
            }
        }

        $genre = isset($_POST['genre']) ? (is_array($_POST['genre']) ? implode(',', $_POST['genre']) : $_POST['genre']) : '';
        $bunrui_id1 = $bunrui_map[$_POST['bunrui_id'] ?? ''] ?? 12;
        
        // bunrui_id2の設定: 3なら4、4なら3
        $bunrui_id2 = null;
        if ($bunrui_id1 == 3) {
            $bunrui_id2 = 4;
        } elseif ($bunrui_id1 == 4) {
            $bunrui_id2 = 3;
        }
        
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
            'goodsName' => $_POST['goodsName'],
            'price' => (int)($_POST['price'] ?? 0),
            'goods_image' => '',  // 一旦空で登録
            'genre' => $genre,
            'color' => $_POST['color'] ?? '',
            'goodsText' => $_POST['goodsText'] ?? '',
            'bunrui_id1' => $bunrui_id1,
            'bunrui_id2' => $bunrui_id2,
            'stock' => (int)($_POST['stock'] ?? 1),
            'member_id' => $member_id,
            'recommend' => isset($_POST['code']) ? 1 : 0,
            'size' => $size_value
        ];

    $newGoodsCode = $shuppinDAO->insert($data);
        
        // 商品コードで画像をリネーム (例: 商品104 → 0104.jpg)
        if ($temp_image_path && $newGoodsCode) {
            $upload_dir = __DIR__ . '/images/';
            $new_filename = sprintf('%04d', $newGoodsCode) . '.' . $image_ext;
            $new_image_path = $upload_dir . $new_filename;
            
            if (rename($temp_image_path, $new_image_path)) {
                // DBには0104.jpgだけを保存
                $shuppinDAO->updateForOwner($newGoodsCode, $member_id, ['goods_image' => $new_filename]);
            }
        }
        
        header('Location: shuppin_shohinhyoji.php?added=1');
        exit;

    } catch (Exception $e) {
        $message = '商品登録エラー: ' . $e->getMessage();
    }
}

if (isset($_GET['action'], $_GET['goodsCode']) && $_GET['action'] === 'delete') {
    try {
        $delete_code = (int)$_GET['goodsCode'];
    $item = $shuppinDAO->findOneForOwner($delete_code, $member_id);
        
        if (!$item) {
            $message = '<div class="alert alert-danger">削除権限がありません。</div>';
        } else {
            if ($shuppinDAO->delete($delete_code)) {
                $img_path = is_array($item) ? ($item['goods_image'] ?? '') : ($item->goods_image ?? '');
                if ($img_path) {
                    // DBに保存されているファイル名からフルパスを構築
                    $file = __DIR__ . '/images/' . ltrim($img_path, '/\\');
                    if (is_file($file)) @unlink($file);
                }
                $message = '<div class="alert alert-success">商品を削除しました。</div>';
            } else {
                $message = '<div class="alert alert-danger">削除に失敗しました。</div>';
            }
        }
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">削除エラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

// ログイン中ユーザーの出品を取得
$goods_list = $goodsDAO->get_goods_by_member_id($member_id, true);
$item_count = count($goods_list);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出品商品表示画面</title>
    <link href="css/shuppin_shohinhyoji.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/delete-buttons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .item-wrapper {
            position: relative;
            text-align: center;
            padding: 0;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        .item-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .image-area {
            position: relative;
        }
        .image-area img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .product-info {
            padding: 15px;
            text-align: left;
        }
        .button-group {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 10;
        }
        .btn-edit, .btn-delete {
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        .btn-edit { background: rgba(102, 126, 234, 0.9); }
        .btn-edit:hover { background: rgba(102, 126, 234, 1); transform: scale(1.05); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5); }
        .btn-delete { background: rgba(245, 87, 108, 0.9); }
        .btn-delete:hover { background: rgba(245, 87, 108, 1); transform: scale(1.05); box-shadow: 0 4px 12px rgba(245, 87, 108, 0.5); }
    </style>
</head>
<body>
<?php include "header.php"; ?>
<div class="container">
    <?php if ($message): ?>
    <div class="mt-3">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="container mt-4 p-4 border rounded">
        <h2 class="mb-4">📦 出品商品一覧</h2>
        <div class="hero-section text-center">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="fw-bold">
                    <?php if ($item_count > 0): ?>
                        出品商品数: <?php echo $item_count; ?> 個
                    <?php else: ?>
                        出品商品がありません
                    <?php endif; ?>
                </div>
                <a href="shohin_shuppin.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> 商品を出品</a>
            </div>

            <?php if ($item_count > 0): ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($goods_list as $goods): ?>
    <div class="col">
        <div class="item-wrapper">
            <div class="image-area">
                <?php
                // DBに0104.jpgのような形式で保存されている場合、表示時にimages/goods/を付ける
                $dbImagePath = $goods->goods_image;
                $fileExists = false;
                
                if ($dbImagePath) {
                    // DBに保存されているファイル名にimages/を付ける
                    $imgSrc = 'images/' . ltrim($dbImagePath, '/\\');
                    $fullPath = __DIR__ . '/images/' . ltrim($dbImagePath, '/\\');
                    $fileExists = file_exists($fullPath);
                    
                    // ファイルが存在しない場合はno_imageを使用
                    if (!$fileExists) {
                        $imgSrc = 'images/no_image.png';
                    }
                } else {
                    $imgSrc = 'images/no_image.png';
                }
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>"
                    alt="<?php echo htmlspecialchars($goods->goodsName); ?>"
                    onerror="this.onerror=null; this.src='images/no_image.png'">
                <div class="button-group">
                    <a href="shohin_hensyu.php?goodsCode=<?php echo (int)$goods->goodsCode; ?>" 
                        class="btn-edit">
                        <i class="bi bi-pencil-square"></i> 編集
                    </a>
                    <button class="btn-delete" 
                            onclick="if (confirm('<?php echo htmlspecialchars($goods->goodsName); ?> を削除しますか？')) { 
                                window.location.href='?action=delete&goodsCode=<?php echo htmlspecialchars($goods->goodsCode); ?>'; 
                            }">
                        <i class="bi bi-trash"></i> 削除
                    </button>
                </div>
            </div>
            <div class="product-info">
                <p class="mb-1"><strong><?php echo htmlspecialchars($goods->goodsName); ?></strong></p>
                <p class="text-primary fw-bold mb-1">¥<?php echo number_format($goods->price); ?></p>
                <p class="small text-secondary mb-0">在庫: <?php echo (int)$goods->stock; ?> 個</p>
            </div>
        </div>
    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">
                    まだ商品を出品していません。「商品を出品」ボタンから商品を登録してください。
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>