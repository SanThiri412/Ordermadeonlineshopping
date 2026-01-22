<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <link href="css/mypageforstyle.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>
    <main>
        <div class="card">
            <div class="card-inner">
                <div class="profile-row">
                    <div class="profile-icon"><img src="images/Icons/user.png" alt="profile"></div>
                    <div class="profile-title">自己紹介</div>
                </div>

                <nav class="list" aria-label="マイページメニュー">
                    <a class="list-item" href="cart.php">
                        <img class="list-icon" src="images/Icons/shopping-cart.png" alt="cart">
                        <div class="list-label">カート画面</div>
                    </a>
                    <a class="list-item" href="okiniiri.php">
                        <img class="list-icon" src="images/Icons/heart.png" alt="favorite">
                        <div class="list-label">お気に入り画面</div>
                    </a>
                    <a class="list-item" href="rireki.php">
                        <img class="list-icon" src="images/Icons/parchment.png" alt="history">
                        <div class="list-label">履歴・メールフォーム画面</div>
                    </a>
                    <a class="list-item" href="shuppin_shohinhyoji.php">
                        <img class="list-icon" src="images/Icons/new-product.png" alt="product">
                        <div class="list-label">出品商品表示画面</div>
                    </a>
                    <a class="list-item" href="member_henkou.php">
                        <img class="list-icon" src="images/Icons/gear.png" alt="settings">
                        <div class="list-label">会員情報編集画面</div>
                    </a>
                </nav>

                <div class="signout-wrap">
                  <form action="logout.php" method="post" style="display:inline">
                      <button type="submit" class="btn-signout">Sign Out</button>
                   </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="page-footer" aria-hidden="true"></footer>
    </body>
    </html>