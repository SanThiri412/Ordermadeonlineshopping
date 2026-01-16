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

        const decreaseBtn = document.getElementById('decrease-btn');
        const increaseBtn = document.getElementById('increase-btn');
        const quantityDisplay = document.getElementById('quantity');
        const totalPriceElem = document.getElementById('total-price');

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
                }
            });
        }

        if (increaseBtn) {
            increaseBtn.addEventListener('click', function () {
                quantity++;
                quantityDisplay.textContent = String(quantity);
                updatePrice();
            });
        }

        // 初期表示
        updatePrice();
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

    // ==============================
    // ② 画像スライダー（商品詳細ページ）
    // ==============================
    const imgEl   = document.getElementById('carousel-image');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const dotsWrap = document.getElementById('carousel-dots');

    // PHPから埋め込んだ画像配列を取得
    const IMAGES = Array.isArray(window.PRODUCT_IMAGES) && window.PRODUCT_IMAGES.length
        ? window.PRODUCT_IMAGES
        : (imgEl ? [imgEl.getAttribute('src')] : []);

    console.log('IMAGES:', IMAGES);

    if (!imgEl || IMAGES.length === 0) {
        console.warn('スライダー初期化失敗: 画像または要素が見つかりません');
        return; // ここで return しても上の数量処理はすでに実行済みなのでOK
    }

    let idx = 0;

    // ドット生成
    function buildDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';
        IMAGES.forEach((_, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'dot' + (i === 0 ? ' active' : '');
            b.setAttribute('aria-label', (i + 1) + '枚目へ');
            b.addEventListener('click', () => goto(i));
            dotsWrap.appendChild(b);
        });
    }

    function updateDots() {
        if (!dotsWrap) return;
        const dots = dotsWrap.querySelectorAll('.dot');
        dots.forEach((d, i) => {
            d.classList.toggle('active', i === idx);
        });
    }

    function goto(n) {
        if (!imgEl) return;
        idx = (n + IMAGES.length) % IMAGES.length;  // ループ
        imgEl.style.opacity = '0';

        setTimeout(() => {
            imgEl.src = IMAGES[idx];
            imgEl.onload = () => {
                imgEl.style.opacity = '1';
            };
            updateDots();
        }, 80);
    }

    // ボタン操作
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            goto(idx - 1);
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            goto(idx + 1);
        });
    }

    // キーボード左右
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft')  goto(idx - 1);
        if (e.key === 'ArrowRight') goto(idx + 1);
    });

    // スワイプ操作（スマホ）
    if (imgEl) {
        let startX = null;
        imgEl.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });

        imgEl.addEventListener('touchend', (e) => {
            if (startX === null) return;
            const diff = e.changedTouches[0].clientX - startX;
            if (Math.abs(diff) > 40) {
                if (diff < 0) {
                    goto(idx + 1); // 左スワイプ → 次へ
                } else {
                    goto(idx - 1); // 右スワイプ → 前へ
                }
            }
            startX = null;
        });
    }

    // 初期化
    buildDots();
    updateDots();
});
