// javascript/goods.js

document.addEventListener('DOMContentLoaded', function () {
    console.log('goods.js loaded');

    const formatYen = (n) => '¥ ' + n.toLocaleString();

    // ==============================
    // ① 商品詳細ページ：数量・金額計算
    // ==============================
    (function () {
        const productRoot = document.getElementById('product-root');
        if (!productRoot) {
            // 商品ページでないときはスキップ
            return;
        }

    const unitPrice = parseInt(productRoot.dataset.unitPrice, 10) || 0;
    const stock = parseInt(productRoot.dataset.stock, 10);

        const decreaseBtn = document.getElementById('decrease-btn');
        const increaseBtn = document.getElementById('increase-btn');
        const quantityDisplay = document.getElementById('quantity');
    const totalPriceElem = document.getElementById('total-price');
    const quantityInput = document.getElementById('quantity-input'); // hidden input for form submission
    const submitButton = productRoot.querySelector('form button[type="submit"]');

        if (!quantityDisplay || !totalPriceElem) {
            console.warn('商品詳細ページ：数量または金額要素が見つかりません');
            return;
        }

    let quantity = 1;
        const q = parseInt(quantityDisplay.textContent, 10);
        if (!Number.isNaN(q) && q > 0) {
            quantity = q;
        } else {
            quantityDisplay.textContent = String(quantity);
        }

        const updatePrice = () => {
            totalPriceElem.textContent = formatYen(unitPrice * quantity);
        };

        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function () {
                if (quantity > 1) {
                    quantity--;
                    quantityDisplay.textContent = String(quantity);
                    updatePrice();
                    if (quantityInput) quantityInput.value = String(quantity);
                    // re-enable increase if it was disabled and we're below stock
                    if (increaseBtn && typeof stock === 'number' && !Number.isNaN(stock)) {
                        if (quantity < stock) increaseBtn.disabled = false;
                    }
                }
            });
        }

        if (increaseBtn) {
            increaseBtn.addEventListener('click', function () {
                // If stock is defined, do not allow exceeding it
                if (typeof stock === 'number' && !Number.isNaN(stock)) {
                    if (quantity < stock) {
                        quantity++;
                        quantityDisplay.textContent = String(quantity);
                        if (quantityInput) quantityInput.value = String(quantity);
                        updatePrice();
                        // if reached stock, disable increase
                        if (quantity >= stock) {
                            increaseBtn.disabled = true;
                        }
                    } else {
                        // optionally give feedback
                        // alert('在庫数に達しました');
                    }
                } else {
                    quantity++;
                    quantityDisplay.textContent = String(quantity);
                    if (quantityInput) quantityInput.value = String(quantity);
                    updatePrice();
                }
            });
        }

        // 初期表示
        updatePrice();
        // 初期 hidden input 同期
        if (quantityInput) quantityInput.value = String(quantity);
        
        // 在庫があるか確認。売り切れなら購入ボタンを無効化、増減ボタンも制御
        if (typeof stock === 'number' && !Number.isNaN(stock)) {
            if (stock <= 0) {
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = '売り切れ';
                }
                if (increaseBtn) increaseBtn.disabled = true;
            } else {
                // もし初期 quantity が在庫以上なら調整
                if (quantity >= stock) {
                    if (increaseBtn) increaseBtn.disabled = true;
                }
            }
        }
        
        // フォーム送信時：売り切れ判定
        const form = productRoot.closest('main').querySelector('form') || 
                     document.querySelector('form[id*="cart"]') ||
                     productRoot.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (stock <= 0) {
                    e.preventDefault();
                    alert('申し訳ありませんが、この商品は売り切れです。');
                    return false;
                }
                if (quantity > stock) {
                    e.preventDefault();
                    alert('選択数量が在庫を超えています。在庫数：' + stock);
                    return false;
                }
            });
        }
    })();

    // ==============================
    // ①’ カートページ：数量・金額計算
    // ==============================
    (function () {
        const cartRoot = document.getElementById('cart-root');
        if (!cartRoot) {
            // カートページ以外では何もしない
            return;
        }

        console.log('cart logic enabled');

        const rows = cartRoot.querySelectorAll('.cart-item-grid');

        rows.forEach((row) => {
            const form = row.querySelector('.item-quantity-control');
            if (!form) return;

            const unitPrice = parseInt(form.dataset.price, 10) || 0;
            const decBtn = row.querySelector('.qty-decrease');
            const incBtn = row.querySelector('.qty-increase');
            const qtySpan = row.querySelector('.quantity-display');
            const totalElem = row.querySelector('.total-price');

            if (!qtySpan || !totalElem) return;

            let quantity = parseInt(qtySpan.textContent, 10);
            if (Number.isNaN(quantity) || quantity <= 0) {
                quantity = 1;
                qtySpan.textContent = String(quantity);
            }

            const updatePrice = () => {
                const total = unitPrice * quantity;
                totalElem.textContent = formatYen(total);
            };

            if (decBtn) {
                decBtn.addEventListener('click', function () {
                    if (quantity > 1) {
                        quantity--;
                        qtySpan.textContent = String(quantity);
                        updatePrice();
                    }
                });
            }

            if (incBtn) {
                incBtn.addEventListener('click', function () {
                    quantity++;
                    qtySpan.textContent = String(quantity);
                    updatePrice();
                });
            }

            // 初期表示整え
            updatePrice();
        });
    })();
});

// ==============================
// カートページ：削除ボタン（JS → POST）
// ==============================
(function () {
    const cartRoot = document.getElementById('cart-root');
    if (!cartRoot) return;   // cart ページでなければ停止

    const deleteButtons = cartRoot.querySelectorAll('.delete-button');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {

            // この削除ボタンが入っている form を取得
            const form = btn.closest("form");
            if (!form) return;

            // action=delete を form に追加
            const actionInput = document.createElement("input");
            actionInput.type = "hidden";
            actionInput.name = "action";
            actionInput.value = "delete";
            form.appendChild(actionInput);

            // 送信（PHP へ）
            form.submit();
        });
    });
})();
