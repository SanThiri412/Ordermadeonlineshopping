<?php
require_once 'helpers/MemberDAO.php';
session_start();
 $member=$_SESSION['member'];

// 表示名
$user_name = $member->name;

// デフォルト画像（保存先）
$default_image = 'images/Icons/user.png';
$user_image = $default_image;

// セッションに member_image（ファイル名）が入っている前提で処理
if (!empty($_SESSION['user_image'])) {
    // 安全のため basename() でディレクトリ操作を除去
    $filename = basename($_SESSION['user_image']);
    $fs_path = __DIR__ . '/images/' . $filename; // サーバ上の実ファイルパス

    if (file_exists($fs_path)) {
        // 存在すれば公開パスを設定
        $user_image = 'images/' . $filename;
    } else {
        // 見つからなければログ出力（デバッグ）
        error_log("DEBUG: user image not found: {$fs_path}");
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/mypageforstyle.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>
    <main>
        <div class="card">
            <div class="card-inner">
                <div class="profile-row">
                    <div class="profile-icon"><img src="<?= $user_image ?>" alt="profile"></div>
                    <div class="profile-title"> <?= $user_name ?></div>
                </div>

                <nav class="list" aria-label="マイページメニュー">
                    <a class="list-item" href="cart.php">
                        <img class="list-icon" src="images/Icons/shopping-cart.png" alt="cart">
                        <div class="list-label">カート</div>
                    </a>
                    <a class="list-item" href="okiniiri.php">
                        <img class="list-icon" src="images/Icons/heart.png" alt="favorite">
                        <div class="list-label">お気に入り</div>
                    </a>
                    <a class="list-item" href="History.php">
                        <img class="list-icon" src="images/Icons/parchment.png" alt="history">
                        <div class="list-label">履歴・メールフォーム</div>
                    </a>
                    <a class="list-item" href="shuppin_shohinhyoji.php">
                        <img class="list-icon" src="images/Icons/new-product.png" alt="product">
                        <div class="list-label">出品商品表示</div>
                    </a>
                    <a class="list-item" href="member_henkou.php">
                        <img class="list-icon" src="images/Icons/gear.png" alt="settings">
                        <div class="list-label">会員情報編集</div>
                    </a>
                </nav>

                <div class="signout-wrap">
                  <form action="logout.php" method="post" style="display:inline">
                      <button type="submit" class="btn-signout">ログアウト</button>
                   </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="page-footer" aria-hidden="true"></footer>
</body>
</html>