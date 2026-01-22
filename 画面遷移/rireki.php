
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/background.css" rel="stylesheet">
     <link href="css/title.css" rel="stylesheet">
    <title>履歴</title>
    <style>
        /* 簡単なリセットと基本スタイル */
        body { font-family: sans-serif; margin: 0; padding: 0;}
         .column {
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
        /* 履歴アイテム */
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
        
        /* アイテム詳細 */
        .item-details { flex-grow: 1; }
        .item-details p { margin: 3px 0; font-size: 0.9em; }
        .item-details strong { font-size: 1.1em; display: block; margin-bottom: 5px; }
        
        /* 注文受付履歴用のステータス */
        .status-badge { 
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #fff;
            border: 1px solid #333;
            border-radius: 50%;
            padding: 10px;
            font-size: 0.8em;
            text-align: center;
        }
    </style>
</head>

<body>
<?php include "header.php"; ?>
<div class="container">
    <h1>履歴</h1>
<div class="row">
    <div class="wrapper">
     <div class="column">
            <h2>購入</h2>
             <div class="border rouded p-3bg-white overflow-auto" style="max-height: 600px;">
                <?php
                // PHPのダミーデータ（実際のシステムではデータベースから取得）
                $purchase_history = [
                    ['name' => '商品名A', 'purchase_date' => '2025/10/01', 'author' => '佐藤', 'shipping_date' => '2025/10/03','img'=>'<img src="images/作家２(シルバー)/0014.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家３(シルバー)/0025.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家４(月)/0042.jpg" width="90px" height="90px">'],
                    ['name' => '商品名B', 'purchase_date' => '2025/09/25', 'author' => '田中', 'shipping_date' => '2025/09/27','img'=>'<img src="images/作家４(月)/0042.jpg" width="90px" height="90px">']
                ];

                foreach ($purchase_history as $item) {
                    // 画像アイコンのSVG（ダミー）
                    ?>
                    <div class="item-card">
                        <figure class="image">
                            <a href="goods.php"><div class="item-image"><?php echo($item['img']); ?></div></a>
                        </figure>
                        <div class="item-details">
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            <p>購入月日: <?php echo htmlspecialchars($item['purchase_date']); ?></p>
                            <p>作家: <?php echo htmlspecialchars($item['author']); ?></p>
                            <p>発送日: <?php echo htmlspecialchars($item['shipping_date']); ?></p>
                        </div>
                        <figure class="image">
                        <a href="mail.php">
                        <img src="images/Icons/mail.png" width="50x" height="50px">
                        </a>
                        </figure>
                    </div>
                <?php } ?>
            </div>
         </div>
        <div class="column">
            <h2>注文受付</h2>
                <div class="border rouded p-3bg-white overflow-auto" style="max-height: 600px;">
                    <?php

                    $order_history = [
                        ['name' => '商品名C', 'purchase_date' => '2025/10/05', 'orderer' => '山田', 'shipping_date' => '2025/10/07','img'=>'<img src="images/作家１(ゴールド)/0003.jpg" width="90px" height="90px">', 'is_delivered' => true],
                        ['name' => '商品名D', 'purchase_date' => '2025/10/02', 'orderer' => '鈴木', 'shipping_date' => '未定','img'=>'<img src="images/作家１(ゴールド)/0005.jpg" width="90px" height="90px">', 'is_delivered' => false]
                    ];
                    foreach ($order_history as $item) {
                        ?>

                        <div class="item-card">
                                <figure class="image">
                                    <a href="goods.php"><div class="item-image"><?php echo($item['img']); ?></div></a>
                                </figure>
                            <div class="item-details">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <p>購入月日: <?php echo htmlspecialchars($item['purchase_date']); ?></p>
                                <p>注文者: <?php echo htmlspecialchars($item['orderer']); ?></p>
                                <p>発送日: <?php echo htmlspecialchars($item['shipping_date']); ?></p>
                            </div>
                            
                            <div class="status-badge">
                                <?php echo $item['is_delivered'] ? '納品済' : '納品の有無'; ?>
                            </div>
                            <figure class="image">
                                <a href="mail.php">
                                    <img src="images/Icons/mail.png" width="50x" height="50px">
                                </a>
                             </figure>
                        </div>
                    <?php } ?>
                </divs>
        </div>
        
    </div>

</body>
</html>
