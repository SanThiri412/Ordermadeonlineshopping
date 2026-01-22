
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/background.css" rel="stylesheet">
    
    <title>お気に入り</title>
    <style>
        /* 簡単なリセットと基本スタイル */
        body { font-family: sans-serif; background-color: #f4f4f4;}
        .okiniiri_column {
                height: 80vh;
                width: 80%;
                }
                @media all and (min-width: 500px) {
                                .wrapper {
                                    display: flex;
                                }
                }
            .image a:hover{
                opacity: .6;
            }
        /* セクションタイトル */
        h1 { text-align: center; margin: 200px; padding: 10px 0; background-color: #f7f3e8; color: #a08c5c; border: 1px solid #a08c5c; }
        h2 { text-align: center; margin: 20px; padding: 10px 0; background-color: #f7f3e8; color: #a08c5c; border: 1px solid #a08c5c; }
        
        .item-card { 
            background-color: white; 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        .item-image { 
            width: 80px; 
            height: 80px; 
            background-color: #e0e0e0; 
            border: 1px solid #ccc; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin-right: 15px;
        }
        .item-image-maru { 
            width: 80px; 
            height: 80px; 
            background-color: #e0e0e0; 
            border: 1px solid #ccc; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin-right: 15px;
            
            border-radius: 50%;  /* 円形にする */
            position: relative;  /* 基準値とする */
            overflow: hidden;
        }
        

        .item-details { flex-grow: 1; }
        .item-details p { margin: 3px 0; font-size: 0.9em; }
        .item-details strong { font-size: 1.1em; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>
<?php include "header.php"; ?>

<div class="container">
    <h1>お気に入り</h1>
<div class="row">
   <div class="wrapper">
     <div class="okiniiri_column">
        <h2>商品</h2>
            <div class="border rouded p-3bg-white overflow-auto" style="max-height: 600px;">
                <?php
                // PHPのダミーデータ（実際のシステムではデータベースから取得）
                $purchase_history = [
                    ['name' => '商品名A', 'purchase_date' => '2025/10/01', 'author' => '佐藤', 'shipping_date' => '2025/10/03','img'=>'<img src="images/作家１(ゴールド)/0003.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家１(ゴールド)/0003.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家１(ゴールド)/0003.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家１(ゴールド)/0003.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家１(ゴールド)/0003.jpg" width="90px" height="90px">']
                ];
                foreach ($purchase_history as $item) {                
                    ?>
                    <div class="item-card">
                            <figure class="image">
                                <a href="goods.php"><div class="item-image"><?php echo($item['img']); ?></div></a>
                            </figure>
                        <div class="item-details">
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        </div>
                        <input type="submit" class="btn btn-primary btn-sm"  name="delete" value="削除">
                    </div>
                <?php } ?>
            </div>
        </div>
            <div class="okiniiri_column">
                <h2>作家</h2>
                    <div class="border rouded p-3bg-white overflow-auto" style="max-height: 400px;">
                        <?php
                            $order_history = [
                                ['name' => 'ぎん', 'img'=>'<img src="images/作家２(シルバー)/00アイコン.jpg" width="90px" height="90px">'],
                                ['name' => 'つき', 'img'=>'<img src="images/作家４(月)/00アイコン.jpg" width="90px" height="90px">']
                            ];
                            foreach ($order_history as $item) {
                                ?>
                                <div class="item-card">
                                    <figure class="image">
                                        <a href="explanation.php"><div class="item-image-maru"><?php echo($item['img']); ?></div></a>
                                    </figure>
                                    <div class="item-details">
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </div>
                                    <input type="submit" class="btn btn-primary btn-sm"  name="delete" value="削除">
                                </div>
                        <?php } ?>
                    </div>
            </div>
        </div>
    </div>

</body>
</html>
                            