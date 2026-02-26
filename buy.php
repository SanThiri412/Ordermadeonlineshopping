<?php
    require_once './helpers/CartDAO-buy.php';
    require_once './helpers/MemberDAO.php';
    
    session_start();

    if(!isset($_SESSION['member'])){
        header('Location: login.php');
        exit;
    }

    $member = $_SESSION['member'];
    $CartDAO=new CartDAO();
    $cart_list = $CartDAO->get_cart_by_memberid($member->member_id);
    $cnt=0;
    $sum=0;
    $member=new Member();
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/title.css" rel="stylesheet">
    <title>購入</title>
</head>
<body>
    <style>
        .image a:hover{
            opacity: .6;
        }
        .p-small{
            font-size: 0.8em;
            color: #7c7c7cff;
        }
    </style>
    <?php include "header.php" ?>
    <form action="top.php" method="post">
    <div class="container mt-4 p-4 w-60 border rounded">
        <div class="container card-section">
            <h2 class="text-center mb-5">購入確認</h2>
            <div class="row">
                <div class="col-md-6 mb-6">
                    <h5>〇配送先　　</h5>
                    <div class="mb-3">
                        お名前：<?= $member->name ?>
                    </div>
                    <div class="mb-3">
                        フリガナ：<?= $member->kanaName ?>
                    </div>
                    <div class="mb-3">
                        電話番号：<?= $member->tel ?>
                    </div>
                    <div class="mb-3">
                        郵便番号：<?= $member->postcode ?>
                    </div>
                    <div class="mb-3">
                        住所：<?= $member->address ?>
                    </div>
                    <br>
                    <h5>〇支払い</h5>
                    <div>
                        支払用カード情報：<?= $member->cardnumber ?>
                    </div>
                    <br>
                    ※配送先やカード情報を変更する場合はマイページより行ってください。
                </div>
                
                <div class="col-md-6 mb-6 card h-100 shadow-sm">
                    <div class="overflow-auto" style="max-height: 600px;">
                    <?php foreach($cart_list as $cart) : ?>   
                    <table>
                        <tr>
                            <td rowspan="5">
                                <figure class="image"><a href="goods.php?goodsCode=<?= $cart->goodsCode ?>">
                                    <img src="images/<?= $cart->goods_image ?>" height="120px" style="padding: 10px; margin: bottom 10px;">
                                </a></figure>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">
                                <?=$cart->goodsName ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($cart->request != null): ?>
                                    要望：<?= $cart->request ?>
                                <?php else:?>
                                    要望：なし
                                <?php endif ; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                購入個数：<?= $cart->cartCount ?>
                                <?php $cnt+=$cart->cartCount ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                単価：<?= number_format($cart->price) ?>円
                                <?php $sum+=($cart->cartCount)*($cart->price) ?>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <?php endforeach; ?>
                    </div>
                    <br><hr><br>
                    <div style="text-align: right;">
                        合計購入点数：　<?= $cnt ?>点<br>
                        総合計金額　：　<?= number_format($sum) ?>円
                    </div>
                    <br>
                </div>
            </div>
        </div>
        <br>
        <div class="d-grid gap-2 col-6 mx-auto">
            <button type="submit" name="buy" class="container btn btn-primary">購入確定</button>
        </div>
    </div>
</form>
</body>
</html>