<?php
require_once './helpers/GoodsDAO.php';
require_once './helpers/MemberDAO.php';
require_once './helpers/DAO.php';

session_start();

$artist_id = isset($_GET['artist_id']) ? intval($_GET['artist_id']) : 1;

// DEBUG
error_log("DEBUG: artist_id = " . var_export($artist_id, true));

// お気に入り作家トグル処理（POST）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['artist'])) {
    $member = $_SESSION['member'] ?? null;
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (!$member) {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            $return = urlencode($_SERVER['REQUEST_URI']);
            echo json_encode(['status' => 'login_required', 'login_url' => 'login.php?return=' . $return]);
            exit;
        }
        header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }

    $favorite_member_id = isset($_POST['artist']) ? intval($_POST['artist']) : 0;
    
    if ($favorite_member_id > 0) {
        try {
            $dbh = DAO::get_db_connect();
            
            // 確認
            $sql = "SELECT COUNT(*) AS cnt FROM Favorite_member WHERE member_id = :m AND favorite_member_id = :fm";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':m', (int)$member->member_id, PDO::PARAM_INT);
            $stmt->bindValue(':fm', $favorite_member_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $favorited = (int)($row['cnt'] ?? 0) > 0;

            if ($favorited) {
                // 削除
                $del = $dbh->prepare("DELETE FROM Favorite_member WHERE member_id = :m AND favorite_member_id = :fm");
                $del->bindValue(':m', (int)$member->member_id, PDO::PARAM_INT);
                $del->bindValue(':fm', $favorite_member_id, PDO::PARAM_INT);
                $del->execute();
                $favorited = false;
            } else {
                // 追加
                $ins = $dbh->prepare("INSERT INTO Favorite_member (member_id, favorite_member_id) VALUES (:m, :fm)");
                $ins->bindValue(':m', (int)$member->member_id, PDO::PARAM_INT);
                $ins->bindValue(':fm', $favorite_member_id, PDO::PARAM_INT);
                $ins->execute();
                $favorited = true;
            }
        } catch (Exception $e) {
            error_log('Favorite_member toggle error: ' . $e->getMessage());
            $favorited = false;
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'ok', 'favorited' => $favorited]);
            exit;
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI']));
        exit;
    }
}

// Map artist IDs to folder names
$artistFolders = [
    1 => '作家１(ゴールド)',
    2 => '作家２(シルバー)',
    3 => '作家３(プラチナ)',
    4 => '作家４(月)',
    5 => '作家５(宝石)',
    6 => '作家６(面白)',
    7 => '作家７(天然石)',
    8 => '作家８(和風)',
];

// === DAO ===
$goodsDAO = new GoodsDAO();
$memberDAO = new MemberDAO();

// 作家情報
$artist = $memberDAO->get_member_by_id($artist_id);

// DEBUG
error_log("DEBUG: artist result = " . var_export($artist, true));

if (!$artist) {
    // 作家が見つからない場合のデフォルト
    $artist_name = "不明な作家";
    $artist_intro = "";
    $artist_folder = $artistFolders[1] ?? '作家１(ゴールド)';
    $artist_image = "user.png";
} else {
    $artist_name = $artist->nickName ?? "不明な作家";
    $artist_intro = $artist->self_introduction ?? "";
    $artist_folder = $artistFolders[$artist_id] ?? '作家１(ゴールド)';
    $artist_image = $artist->member_image ?? "user.png";
}

// 作家のおすすめ商品
$recommended_products = $goodsDAO->get_recommend_goods_by_member_id($artist_id) ?? [];

// 作家の出品商品
$exhibited_products = $goodsDAO->get_goods_by_member_id($artist_id) ?? [];

// ログイン状態でお気に入い状態を取得
$isMemberFavorited = false;
$member = $_SESSION['member'] ?? null;
if ($member && isset($member->member_id)) {
    try {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT COUNT(*) AS cnt FROM Favorite_member WHERE member_id = :m AND favorite_member_id = :fm";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':m', (int)$member->member_id, PDO::PARAM_INT);
        $stmt->bindValue(':fm', (int)$artist_id, PDO::PARAM_INT);
        $stmt->execute();
        $isMemberFavorited = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
    } catch (Exception $e) {
        error_log('favorite member read error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/background.css" rel="stylesheet">
<link href="css/Explanationforstyle.css" rel="stylesheet"> 
<title>作家詳細ページ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

<?php include "header.php"; ?>

<main class="container my-4">
    <div class="row">
        <!-- 左側プロフィール -->
        <div class="col-md-2 text-center">

            <img src="images/<?= htmlspecialchars($artist_image) ?>" 
                 onerror="this.src='images/Icons/user.png';"
                 class="profile-image">

            <h5 class="mt-2"><?= htmlspecialchars($artist_name) ?></h5>

            <form method="post" action="" class="fav-form">
                <input type="hidden" name="artist" value="<?= $artist_id ?>">
                <button type="submit" class="btn-primary"
                        aria-pressed="<?php echo $isMemberFavorited ? 'true' : 'false'; ?>"
                        title="お気に入りに追加">
                    <i class="bi <?php echo $isMemberFavorited ? 'bi-heart-fill' : 'bi-heart'; ?>"></i> お気に入り
                </button>
            </form>
        </div>

        <!-- 右側メイン -->
        <div class="col-md-10">
            
            <!-- 自己紹介 -->
            <section class="mb-4">
                <h3>自己紹介</h3>
                <div class="p-3 bg-light rounded border">
                    <?= nl2br(htmlspecialchars($artist_intro ?: "（紹介文はありません）")) ?>
                </div>
            </section>

            <!-- おすすめ商品 -->
            <section class="mb-5">
                <h3>おすすめ商品</h3>

                <?php if (!empty($recommended_products)): ?>
                <div class="horizontal-scroll">
                    <?php foreach ($recommended_products as $product): ?>
                        <?php 
                            $img = "images/" . ($product->goods_image ?? 'Icons/no-image.png');
                        ?>
                        <div class="product-card">
                            <a href="goods.php?goodsCode=<?= htmlspecialchars($product->goodsCode ?? '') ?>">
                                <img src="<?= htmlspecialchars($img) ?>"
                                     onerror="this.src='images/Icons/no-image.png';"
                                     class="product-img">
                            </a>
                            <p><?= htmlspecialchars($product->goodsName ?? '商品名なし') ?></p>
                            <?php if (isset($product->price)): ?>
                                <p class="text-muted">¥<?= number_format($product->price) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-muted">おすすめ商品はありません。</p>
                <?php endif; ?>
            </section>

            <!-- 出品商品 -->
            <section>
                <h3>出品商品</h3>

                <?php if (!empty($exhibited_products)): ?>
                <div class="horizontal-scroll">
                    <?php foreach ($exhibited_products as $product): ?>
                        <?php 
                            $img = "images/" . $product->goods_image;
                        ?>
                        <div class="product-card">
                            <a href="goods.php?goodsCode=<?= $product->goodsCode ?>">
                                <img src="<?= htmlspecialchars($img) ?>"
                                     onerror="this.src='images/Icons/no-image.png';"
                                     class="product-img">
                            </a>
                            <p><?= htmlspecialchars($product->goodsName) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-muted">現在出品商品はありません。</p>
                <?php endif; ?>
            </section>

        </div>
    </div>
</main>

<script src="javascript/favorite.js"></script>
</body>
</html>
