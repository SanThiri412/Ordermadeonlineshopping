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
    <?php include "header2.php" ?>
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
    <form action="search.php" method="post">
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

    <ul class = "slideshow">
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家２(シルバー)/0022.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家４(月)/0042.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家６(面白)/0072.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家８(和風)/0087.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家１(ゴールド)/0013.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家７(天然石)/0075.jpg" alt="" height="500px"></a>
        </li>
    </ul>
    <ul class = "slideshow">
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家２(シルバー)/0022.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家４(月)/0042.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家６(面白)/0072.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家８(和風)/0087.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家１(ゴールド)/0013.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/よし.jpg" alt=""></a>
        </li>
    </ul>
    <ul class = "slideshow">
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家２(シルバー)/0022.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家４(月)/0042.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家６(面白)/0072.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家８(和風)/0087.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/作家１(ゴールド)/0013.jpg" alt="" height="500px"></a>
        </li>
        <li class="content">
            <a href="goods.php"><img class="slide" src="images/よし.jpg" alt=""></a>
        </li>
    </ul>

    </div>
    </form>
</html>