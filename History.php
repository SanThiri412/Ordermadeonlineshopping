<?php
    require_once 'helpers/MemberDAO.php';
    require_once 'helpers/HistoryDAO.php';
    session_start();

    if(!isset($_SESSION['member'])){
        header('Location:login.php');
        exit;
    }
    $member=$_SESSION['member'];

    $History_buy_DAO = new HistoryDAO();
    $History_Orders_DAO = new HistoryDAO();

    $buy_list=$History_buy_DAO->get_buy_by_memberid($member->member_id);
    $Orders_list=$History_Orders_DAO->get_buyOrders_by_memberid($member->member_id);

     if($_SERVER['REQUEST_METHOD']==='POST'){
        if(isset($_POST['goods_go'])){
            $goodscode=(int)$_POST['goodscode'];
            $member_id=(int)$_POST['member_id'];
            $HistoryDAO=new HistoryDAO();
            $HistoryDAO->goods_go($member_id,$goodscode);
        }else if(isset($_POST['goods_no'])){
            $goodscode=(int)$_POST['goodscode'];
            $member_id=(int)$_POST['member_id'];
            $HistoryDAO=new HistoryDAO();
            $HistoryDAO->goods_no($member_id,$goodscode);
        }
        header("Location:".$_SERVER['PHP_SELF']);
        exit;
     }
    
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/background.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>履歴</title>
    
    <style>
        /* 簡単なリセットと基本スタイル */
        body { font-family: sans-serif; margin: 0; padding: 0;}
         .His_column {
                height: 80vh;
                width: 80%;
                }
                @media all and (min-width: 500px) {
                                .His_wrapper {
                                    display: flex;
                                }
                }
                .image a:hover{
                    opacity: .6;
                }
            .border {
                flex: 1 1 auto; /* 子要素がスクロールできるように */
                overflow: auto;
            }
        .okiniiri-container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
        }
        /* セクションタイトル */
        h1 {
                max-width: 600px;        /* 最大幅 */
                margin: 20px auto;
                padding: 10px 0;
                background-color: #f7f3e8;
                color: #a08c5c;
                border: 1px solid #a08c5c;
                text-align: center;
            }
        h2 { text-align: center; margin: 20px; padding: 10px 0; background-color: #f7f3e8; color: #a08c5c; border: 1px solid #a08c5c; }
        h4 { text-align: center; margin: 20px; padding: 10px 0; background-color: #f7f3e8; color: #a08c5c;}
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
    <div class="okiniiri-container">
        <h1>履歴</h1>
            <div class="His_wrapper">
                <div class="His_column">
                    <h2>購入</h2>
                        <div class="border rounded p-3 bg-white overflow-auto" style="max-height: 650px;">
                            <?php if (empty($buy_list)) { ?>
                                <h4>購入履歴なし。</h4>
                                 <?php } else { ?>
                                    <?php foreach ($buy_list as $buy) { ?>
                                        <div class="item-card">
                                            <figure class="image">
                                                <a href="goods.php?goodsCode=<?=$buy->goodscode ?>">
                                                <div class="item-image"><img src="images/<?php echo $buy->goods_image; ?>" width="90" height="90"></div></a>
                                            </figure>
                                            <div class="item-details">
                                                <strong><?php echo htmlspecialchars($buy->goodsname); ?></strong>
                                                <p>購入月日: <?php echo htmlspecialchars($buy->buyDatetime); ?></p>
                                                <p>作家: <?php echo htmlspecialchars($buy->nickName); ?></p>
                                            </div>
                                            <figure class="image">
                                            <a href="mail.php?goodsCode=<?=$buy->goodscode ?>&member_id=<?=$buy->goods_member_id; ?>">
                                                <img src="images/Icons/mail.png" width="50x" height="50px">
                                            </a>
                                            </figure>
                                        </div>
                                    <?php } ?>
                            <?php } ?>
                        </div>
                </div>
                <div class="His_column">
                    <h2>注文受付</h2>
                        <div class="border rounded p-3 bg-white overflow-auto" style="max-height: 650px;">
                             <?php if (empty($Orders_list)) { ?>
                                <h4>注文受付なし。</h4>
                                <?php } else { ?>
                                    <?php foreach ($Orders_list as $Orders) {?>
                                        <div class="item-card">
                                                <figure class="image">
                                                    <a href="goods.php?goodscode=<?=$Orders->goodscode ?>">
                                                    <div class="item-image"><img src="images/<?php echo $Orders->goods_image; ?>" width="90" height="90"></div></a>
                                                </figure>
                                            <div class="item-details">
                                                <strong><?php echo htmlspecialchars($Orders->goodsname); ?></strong>
                                                <p>購入月日: <?php echo htmlspecialchars($Orders->ordersDatetime); ?></p>
                                                <p>注文者: <?php echo htmlspecialchars($Orders->nickName); ?></p>
                                        <?php if($Orders->request!=null){?>
                                                <p>リクエスト:<?php echo htmlspecialchars($Orders->request);?></p>
                                        <?php }?>
                                                <form action="" method="post">
                                                    <input type="hidden" name="member_id" value="<?php echo $Orders->Orders_member_id; ?>">
                                                    <input type="hidden" name="goodscode" value="<?php echo $Orders->goodscode; ?>">
                                                    <input type="submit" class="btn btn-primary btn-sm" name="goods_go" value="納品">
                                                    <input type="submit" class="btn btn-primary btn-sm" name="goods_no" value="納品取り消し">
                                                </form>
                                            </div>
                                                <div class="status-badge">
                                                    <?= $Orders->endFlag == 1 ? '納品済' : '未納品' ?>
                                                </div>

                                            <figure class="image">
                                                <a href="mail.php?goodsCode=<?=$Orders->goodscode ?>&member_id=<?=$Orders->Orders_member_id; ?>">
                                                    <img src="images/Icons/mail.png" width="50x" height="50px">
                                                </a>
                                            </figure>
                                        </div>
                                    <?php } ?>
                         <?php } ?>
                </div>
                </div>
    </div>
</body>
</html>
