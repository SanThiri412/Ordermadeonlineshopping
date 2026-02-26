document.addEventListener('DOMContentLoaded', function () {
    // 対象フォームのセレクタ
    const forms = document.querySelectorAll('.fav-form, .fav-form-overlay');

    forms.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            if (!button) return;

            // ボタンクリック直後に UI を更新（楽観的更新）
            const icon = button.querySelector('i');
            const currentlyPressed = button.getAttribute('aria-pressed') === 'true';
            // disable to prevent double clicks
            button.disabled = true;

            const formData = new FormData(form);

            fetch(form.action || window.location.pathname, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            }).then(res => {
                // If server redirects to login, fetch may return HTML; try JSON parse safely
                const ctype = res.headers.get('content-type') || '';
                if (ctype.indexOf('application/json') !== -1) {
                    return res.json();
                }
                // 非JSONはリダイレクト発生とみなす
                return res.text().then(() => ({ status: 'reload' }));
            }).then(data => {
                if (!data) return;

                if (data.status === 'ok') {
                    const favorited = !!data.favorited;
                    // update aria and icon
                    button.setAttribute('aria-pressed', favorited ? 'true' : 'false');
                    if (icon) {
                        if (favorited) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill');
                        } else {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                        }
                    }
                    button.disabled = false;
                } else if (data.status === 'login_required') {
                    // server asked to login
                    if (data.login_url) {
                        window.location.href = data.login_url;
                    } else {
                        window.location.reload();
                    }
                } else if (data.status === 'reload') {
                    window.location.reload();
                }
            }).catch(err => {
                console.error('favorite error', err);
                // on error, re-enable and revert optimistic UI
                button.disabled = false;
            });
        });
    });
});
