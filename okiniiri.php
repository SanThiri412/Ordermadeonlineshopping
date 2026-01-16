<?php
    require_once './helpers/MemberDAO.php'; 
    require_once 'helpers/OkiniiriDAO.php';
    session_start();
    if(!isset($_SESSION['member'])){
        header('Location:login.php');
        exit;
    }
   $member=$_SESSION['member'];

    $member=$_SESSION['member'];
    $OkiniiriDAO = new OkiniiriDAO();
    $goods_list= $OkiniiriDAO->get_goods_Okiniiri_by_memberid($member->member_id);
    $member_list= $OkiniiriDAO->get_member_Okiniiri_by_memberid($member->member_id);

    if($_SERVER['REQUEST_METHOD']==='POST'){
        if(isset($_POST['delete_g'])){

            $goodscode=$_POST['favorite_goodsCode'];

            $OkiniiriDAO=new OkiniiriDAO();
            $OkiniiriDAO->goods_delete($member->member_id,$goodscode);
      }else if(isset($_POST['delete_m'])){
           $favorite_member_id = $_POST['favorite_member_id'];

            $OkiniiriDAO=new OkiniiriDAO();
            $OkiniiriDAO->member_delete($favorite_member_id,$member->member_id);
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
    
    
    
    <title>お気に入り</title>
    <style>
        /* 簡単なリセットと基本スタイル */
        body { font-family: sans-serif; background-color: #f4f4f4;}
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
            .border {
                flex: 1 1 auto; /* 子要素がスクロールできるように */
                overflow: auto;
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
        .okiniiri-container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
        }
/*こ*/
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
    <div class="okiniiri-container">
        <h1 >お気に入り</h1>
        <div class="wrapper">
            <div class="column">
                <h2>商品</h2>
                    <div class="border rounded p-3 bg-white overflow-auto" style="max-height: 650px;">
                        <?php if (empty($goods_list)) { ?>
                            <h4>お気に入り商品なし</h4>
                        <?php } else { ?>
                            <?php foreach ($goods_list as $goods) { ?>
                                <div class="item-card">
                                    <figure class="image">
                                        <a href="goods.php?goodscode=<?= $goods->favorite_goodsCode ?>">
                                            <div class="item-image">
                                                <img src="images/<?= $goods->goods_image ?>" width="90" height="90">
                                            </div>
                                        </a>
                                    </figure>
                                    <div class="item-details">
                                        <strong><?= $goods->goodsName ?></strong>
                                    </div>
                                    <form action="" method="post">
                                        <input type="hidden" name="favorite_goodsCode" value="<?= $goods->favorite_goodsCode ?>">
                                        <input type="submit" class="btn btn-primary btn-sm" name="delete_g" value="削除">
                                    </form>
                                </div>
                            <?php } ?>
                        <?php } ?>
                     </div>
            </div>
        <div class="column">
            <h2>作家</h2>
                <div class="border rounded p-3 bg-white overflow-auto" style="max-height: 650px;">
                    <?php if (empty($member_list)) { ?>
                        <h4>お気に入り作家なし</h4>
                    <?php } else { ?>
                        <?php foreach ($member_list as $list) {?>
                            <div class="item-card">
                                <figure class="image">
                                    <a href="explanation.php?artist_id=<?=$list->favorite_member_id; ?>">
                                        <div class="item-image-maru">
                                            <img src="images/<?php echo htmlspecialchars($list->member_image); ?>" width="90" height="90">
                                        </div>
                                    </a>
                                </figure>
                                    <div class="item-details">
                                        <strong><?php echo htmlspecialchars($list->nickName); ?></strong>
                                    </div>
                                        <form action="" method="post">
                                            <input type="hidden" name="favorite_member_id" value="<?php echo $list->favorite_member_id; ?>">
                                            <input type="submit" class="btn btn-primary btn-sm" name="delete_m" value="削除">
                                        </form>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>

</script>
</body>
</html>

 