<?php
ob_start();
session_start();
require_once './helpers/MemberDAO.php';

// ログイン状態の確認（初期値はnullにしておくのが安全です）
$member = null;
if (isset($_SESSION['member'])) {
    $member = $_SESSION['member'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            background-color: #f8f6f7;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-white bg-white">
        <div class="container">
            <a class="navbar-brand" href="top.php">
                <img src="images/ロゴ.png" alt="ロゴ" height="50">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($member)) : ?>
                        <li class="nav-item">
                            <a href="cart.php" class="nav-link"><font color="black">カート</font></a>
                        </li>
                        <li class="nav-item">
                            <a href="mypage.php" class="nav-link"><font color="black">マイページ</font></a>
                        </li>
                        <li class="nav-item">
                            <a href="okiniiri.php" class="nav-link"><font color="black">お気に入り</font></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="login.php" class="nav-link"><font color="black">ログイン</font></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>