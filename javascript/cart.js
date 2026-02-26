
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