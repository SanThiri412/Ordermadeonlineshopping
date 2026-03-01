<?php
// 1. まず最初にクラスの定義を読み込む（これが重要！）
require_once __DIR__ . '/helpers/MemberDAO.php'; 

// 2. その後にセッション（header_init）を開始する
require_once __DIR__ . '/header_init.php';
require_once __DIR__ . '/helpers/OkiniiriDAO.php';
// ログイン確認
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$dao = new OkiniiriDAO();

// 正しいメソッドを呼び出す
$favorite_goods = $dao->get_goods_Okiniiri_by_memberid($member->member_id);
$favorite_members = $dao->get_member_Okiniiri_by_memberid($member->member_id);

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_goods'])) {
        $favorite_goodsCode = (int)$_POST['favorite_goodsCode'];
        $dao->goods_delete($member->member_id, $favorite_goodsCode);
        header('Location: okiniiri.php');
        exit;
    } elseif (isset($_POST['delete_member'])) {
        $favorite_member_id = (int)$_POST['favorite_member_id'];
        $dao->member_delete($favorite_member_id, $member->member_id);
        header('Location: okiniiri.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り</title>
    <link href="css/okiniiri.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php include __DIR__ . '/header_display.php'; ?>

<div class="container mt-4">
    <h1>お気に入り</h1>
    <?php if (empty($favorite_goods) && empty($favorite_members)): ?>
        <p class="text-muted">お気に入り登録がまだありません。</p>
    <?php endif; ?>
    
    <!-- お気に入り商品 -->
    <?php if (!empty($favorite_goods)): ?>
        <h2>お気に入り商品</h2>
        <div class="row">
            <?php foreach ($favorite_goods as $item): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="images/<?php echo htmlspecialchars($item->goods_image ?? 'no_image.png', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="商品画像">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item->goodsName ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>
                            <a href="goods.php?goodsCode=<?php echo urlencode($item->favorite_goodsCode); ?>" class="btn btn-primary btn-sm">詳細</a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_goods" value="1">
                                <input type="hidden" name="favorite_goodsCode" value="<?php echo $item->favorite_goodsCode; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('削除しますか？');">削除</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- お気に入り作家 -->
    <?php if (!empty($favorite_members)): ?>
        <h2 class="mt-5">お気に入り作家</h2>
        <div class="row">
            <?php foreach ($favorite_members as $artist): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="images/<?php echo htmlspecialchars($artist->member_image ?? 'no_image.png', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="作家画像">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($artist->nickName ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>
                            <a href="explanation.php?artist_id=<?php echo urlencode($artist->favorite_member_id); ?>" class="btn btn-primary btn-sm">プロフィール</a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_member" value="1">
                                <input type="hidden" name="favorite_member_id" value="<?php echo $artist->favorite_member_id; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('削除しますか？');">削除</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>