<?php
require_once __DIR__ . '/helpers/MemberDAO.php';
require_once __DIR__ . '/helpers/CartDAO.php';

// セッション開始
session_start();

// 出力エスケープ関数
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// セッションから会員IDを取得（複数キーに対応）
function getMemberNo() {
    if (isset($_SESSION['member'])) {
        $member = $_SESSION['member'];
        if (is_object($member)) {
            return $member->member_id ?? $member->memberNo ?? $member->member_no ?? null;
        }
        if (is_array($member)) {
            return $member['member_id'] ?? $member['memberNo'] ?? $member['member_no'] ?? null;
        }
    }
    return $_SESSION['member_id'] ?? $_SESSION['member_no'] ?? $_SESSION['memberId'] ?? null;
}

$memberNo = getMemberNo() ? (int)getMemberNo() : null;
$cartDAO = new CartDAO();
$cart_items = [];

// POST 処理（PRG パターン）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $memberNo) {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add' && isset($_POST['goodsCode'])) {
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $request = trim($_POST['message'] ?? '') ?: null;
        $cartDAO->addToCart($memberNo, (int)$_POST['goodsCode'], $quantity, $request);
        header('Location: cart.php');
        exit;
    }

    if ($action === 'delete' && isset($_POST['item_id'])) {
        $cartDAO->removeFromCart($memberNo, (int)$_POST['item_id']);
        header('Location: cart.php');
        exit;
    }

    if ($action === 'update' && isset($_POST['item_id'], $_POST['quantity'])) {
        $cartDAO->updateQuantity($memberNo, (int)$_POST['item_id'], 
            max(1, (int)$_POST['quantity']));
        header('Location: cart.php');
        exit;
    }
}

// カート内容を取得
if ($memberNo) {
    $cart_items = array_map(function($r) {
        $img = $r['goodsimage'] ?? $r['image'] ?? '';
        // 画像パスを正規化：goods/ プレフィックスを削除
        if (strpos($img, 'goods/') === 0) {
            $img = substr($img, 6);
        }
        return [
            'id' => $r['goodsCode'],
            'goodsName' => $r['goodsname'] ?? $r['goodsName'] ?? '',
            'price' => (int)($r['price'] ?? 0),
            'cartCount' => (int)($r['quantity'] ?? $r['cartCount'] ?? 1),
            'image' => 'images/' . (strpos($img, 'images/') === 0 ? substr($img, 7) : $img),
            'stock' => isset($r['stock']) ? (int)$r['stock'] : null,
        ];
    }, $cartDAO->getCartItems($memberNo) ?? []);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>あなたのカート | SELARG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/cart.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>

<div class="browser-frame">
    <main class="cart-container" id="cart-root">
        
        <div class="cart-header">
            <div class="continue-shopping">
                <a href="top.php">買い物を続ける</a>
            </div>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="cart-empty" style="padding: 2rem; text-align: center;">
                <?php if (!$memberNo): ?>
                    <p>カートはログイン後に利用できます。<a href="login.php">ログイン</a>してください。</p>
                <?php else: ?>
                    <p>カートに商品が入っていません。</p>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <div class="cart-columns">
                <div class="col-product">商品</div>
                <div class="col-cartCount">数量</div>
                <div class="col-total">合計</div>
            </div>

            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item-grid" data-item-id="<?php echo (int)$item['id']; ?>">

                <div class="item-image">
                    <a href="goods.php?goodsCode=<?php echo (int)$item['id']; ?>">
                        <img src="<?php echo h($item['image']); ?>" 
                            alt="<?php echo h($item['goodsName']); ?>" 
                            class="product-thumbnail" 
                            loading="lazy"
                            onerror="this.onerror=null; this.src='images/no-image.png';">
                    </a>
                </div>

                <div class="item-details">
                    <p class="product-name"><?php echo h($item['goodsName']); ?></p>
                </div>

                <form method="post"
                    class="item-quantity-control"
                    data-price="<?php echo (int)$item['price']; ?>"
                    data-stock="<?php echo isset($item['stock']) ? (int)$item['stock'] : 0; ?>">
                    <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">

                    <button type="button" class="delete-button" aria-label="商品を削除する">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="quantity-control">
                        <button type="button" class="qty-btn qty-decrease" aria-label="数量を減らす">-</button>
                        <span class="quantity-display">
                            <?php echo (int)$item['cartCount']; ?>
                        </span>
                        <button type="button" class="qty-btn qty-increase" aria-label="数量を増やす">+</button>
                    </div>
                </form>

                <div class="total-price">
                    ¥ <?php echo number_format($item['price'] * $item['cartCount']); ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <button type="submit" class="btn-primary" onclick="location.href='buy.php'">
                ご購入手続きへ
                </button>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
// カート数量ボタン動作（在庫制御 + 楽観的更新）
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.cart-item-grid').forEach(function(row){
        const form = row.querySelector('form');
        if (!form) return;
        
        const qtySpan = form.querySelector('.quantity-display');
        const btnDec = form.querySelector('.qty-decrease');
        const btnInc = form.querySelector('.qty-increase');
        const delBtn = form.querySelector('.delete-button');
        const totalEl = row.querySelector('.total-price');
        const price = parseInt(form.dataset.price || '0', 10);
        const stock = parseInt(form.dataset.stock || '0', 10);
        
        let current = parseInt(qtySpan.textContent.trim() || '1', 10);

        const updateButtons = () => {
            btnDec.disabled = current <= 1;
            btnInc.disabled = stock > 0 && current >= stock;
        };

        const updateTotal = () => {
            if (totalEl) {
                const subtotal = price * current;
                totalEl.textContent = '¥ ' + subtotal.toLocaleString('ja-JP');
            }
        };

        const postUpdate = (qty, action = 'update') => {
            const fd = new FormData(form);
            fd.set('action', action);
            fd.set('quantity', String(qty));
            fetch('cart.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            }).catch(err => console.error('Cart update failed:', err));
        };

        updateButtons();
        updateTotal();

        btnDec.addEventListener('click', () => {
            if (current > 1) {
                current--;
                qtySpan.textContent = current;
                updateButtons();
                updateTotal();
                postUpdate(current);
            }
        });

        btnInc.addEventListener('click', () => {
            if (!(stock > 0 && current >= stock)) {
                current++;
                qtySpan.textContent = current;
                updateButtons();
                updateTotal();
                postUpdate(current);
            }
        });

        if (delBtn) {
            delBtn.addEventListener('click', () => {
                if (confirm('この商品をカートから削除しますか？')) {
                    postUpdate(current, 'delete');
                    row.style.opacity = '0.5';
                    setTimeout(() => location.reload(), 300);
                }
            });
        }
    });
});
</script>
</body>
</html>
