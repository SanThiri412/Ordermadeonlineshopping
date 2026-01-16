<?php
// 1. セッションの開始
session_start();

// 2. 初期データの定義
$initial_product_ids = ['0012', '0011', '0010', '0009', '0008', '0007'];
$base_path = 'images/作家１(ゴールド)/';

// 3. セッション変数の初期化
// 初めてページをロードしたとき、またはリセットしたいときに実行
if (!isset($_SESSION['current_products'])) {
    $_SESSION['current_products'] = $initial_product_ids;
}

// 4. 削除処理のシミュレーション（shohin_sakujo.php の役割）
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = htmlspecialchars($_GET['id']);
    
    // 現在の配列から該当IDを削除
    $_SESSION['current_products'] = array_diff($_SESSION['current_products'], [$delete_id]);
    
    // URLからGETパラメータ（actionとid）を削除し、同じページにリダイレクトして再表示
    header('Location: ' . basename(__FILE__));
    exit;
}

// 5. 現在表示する商品リストの取得
$product_ids = $_SESSION['current_products'];

// 6. 削除後の商品数が5個になっているかチェック（動作確認用）
$item_count = count($product_ids);
// 最初の削除が完了した後、このメッセージが表示されます
$status_message = ($item_count < 6) ? "✅ 削除が完了しました。商品数は {$item_count} 個です。" : "現在の商品数: {$item_count} 個";
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出品商品表示画面</title>
     <link href="css/shuppin_shohinhyoji.css" rel="stylesheet">
     <link href="css/background.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* CSSで商品画像を中央揃えにし、ボタンを画像の下に配置するためのスタイル */
        .item-wrapper {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #eee; 
            border-radius: 5px;
        }
        .image-area {
            margin-bottom: 15px;
             flex-grow: 1; /* 画像エリアが可能な限りスペースを占める */
            display: flex; /* 画像とIDを中央に配置 */
             flex-direction: column;
             justify-content: center;
             align-items: center;
            }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    
     <?php include "header.php"; ?>
     
    <div class="container mt-4 p-4 border rounded">
        <div class="hero-section text-center">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="text-success fw-bold"><?php echo $status_message; ?></div>
                
                <a href="shohin_shuppin.php" class="btn btn-primary btn-sm">出品</a>
            </div>

    
            　　    <div class="row row-cols-3 g-4">
            
            <?php foreach ($product_ids as $id): ?>
                <div class="col">
                    <div class="item-wrapper">
                        <div class="image-area">
                            <img src="<?php echo $base_path . $id . '.jpg'; ?>" alt="商品ID <?php echo $id; ?>" width="200px" height="200px" class="product-icon">
                            <p class="mt-2 mb-0 small text-muted">ID: <?php echo $id; ?></p>
                        </div>
                        <div class="button-group">
                            
                            <a href="shohin_hensyu.php?id=<?php echo $id; ?>" class="btn btn-primary btn-sm">編集</a>
                            
                            <button class="btn btn-danger btn-sm" 
                                    onclick="if (confirm('ID: <?php echo $id; ?> の商品を削除しますか？')) { window.location.href='<?php echo basename(__FILE__); ?>?action=delete&id=<?php echo $id; ?>'; }">
                                削除
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
   </div> </div> </div> <div class="container mt-3 text-end">
        
    </div>

</body>
</html>
