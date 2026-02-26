<?php
    require_once './helpers/MemberDAO.php'; // Memberクラス
    require_once './helpers/RandomDAO.php';   
  
    if(isset($_POST['buy'])){
        require_once './helpers/CartDAO-buy.php';
        require_once './helpers/MemberDAO.php';
        require_once './helpers/BuyDAO.php';

        session_start();
        $member = $_SESSION['member'];
        $CartDAO = new CartDAO();
        $cart_list = $CartDAO->get_cart_by_memberid($member->member_id);

        $buyDAO = new BuyDAO();
        foreach ($cart_list as $cart){
            $buyDAO->insert(
                $member->member_id,
                $cart->goodsCode,
                $cart->price,
                $cart->cartCount,
                $cart->request
            );
        }

        // 購入完了後にトップページなどにリダイレクト
        header('Location: top.php?buy=success');
        exit;
    }
    $random = new RandomDAO();
    $images= $random->get_random_image(6);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トップページ</title>
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <script src="jquery-3.6.0.min.js"></script>
    <script src="slideshow.js"></script>
    <link rel="stylesheet" href="css/slideshow.css"/>
</head>
<body>
    <?php include "header.php" ?>
    <style>
        .btn{
            font-weight: bold!important;
            font-size: 20px!important;
        }
        .slideshow{
            display: flex;
            animation: loop-slide 20s infinite linear 1s both;
        }
        .content {
            width: 500px;
            height: 500px;
        }
        .wrap{
            display: flex;
            align-items: center;
            height: 500px;
            overflow: hidden;
        }
        @keyframes loop-slide {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-100%);
            }
        }
        .slide-paused:hover .slideshow {
            animation-play-state: paused;
        }
        li{
            list-style: none;
        }
        .image a:hover{
                opacity: .6;
        }
    </style>
    <br>
    <form action="search.php" method="get">
    <div class="mb-3">
        <div class="rowspan-4" style="text-align: center;">
            <img src="images/ロゴ.png" alt="" width="" height="200px" style="margin: bottom 10px">
        </div>
    </div>
    <div class="d-grid gap-2 col-4 mx-auto">
        <input type="submit" class="container btn-primary btn botn-bg" value="検索">
    </div>
    <br>

    <div class="wrap image slide-paused" ontouchstart="">
        <?php for ($i=0;$i<3;$i++): ?>
            <ul class = "slideshow">
                <?php foreach ($images as $img): ?>
                    <li class="content">
                        <a href="goods.php?goodsCode=<?= $img->goodsCode ?>"><img class="slide" src="images/<?= $img->goods_image ?>" alt="" height="500px"></a>
                    </li>
                <?php endforeach ; ?>
            </ul>
        <?php endfor; ?>
    </div>
    </form>
</html>