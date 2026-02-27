document.addEventListener("DOMContentLoaded", function () {
    // ページロード時にジャンルcheckboxを必ず有効化
    var genreCheckboxes = document.querySelectorAll('input[name="genre[]"]');
    genreCheckboxes.forEach(cb => cb.disabled = false);
    // 分類ごとのジャンル制御
<<<<<<< HEAD
    // カテゴリによらず常に全ジャンル表示
    // 以前のfilterGenresByCategoryは不要
=======
    const categorySelect = document.getElementById("category-select");
    function filterGenresByCategory() {
        const category = categorySelect ? categorySelect.value : "";
        const allLabels = document.querySelectorAll("#material-checkbox-grid label.material-item");
        // 全て表示
        allLabels.forEach(lbl => lbl.style.display = "block");

        if (category === "others") {
            // 分類番号13のみ表示（ウッド/レジン、天然石、ターコイズ、カボション、マーブル、レジンアート）
            const showGenres = ["ウッド/レジン","天然石","ターコイズ","カボション","マーブル","レジンアート"];
            allLabels.forEach(lbl => {
                const val = lbl.querySelector("input").value;
                lbl.style.display = showGenres.includes(val) ? "block" : "none";
            });
        } else if (category === "pierce") {
            // 分類3（ピアス）→分類4（イヤリング）も表示
            allLabels.forEach(lbl => {
                const val = lbl.querySelector("input").value;
                if (val === "イヤリング") {
                    lbl.style.display = "block";
                }
            });
        } else if (category === "earring") {
            // 分類4（イヤリング）→分類3（ピアス）も表示
            allLabels.forEach(lbl => {
                const val = lbl.querySelector("input").value;
                if (val === "ピアス") {
                    lbl.style.display = "block";
                }
            });
        }
    }
    if (categorySelect) {
        categorySelect.addEventListener("change", filterGenresByCategory);
        filterGenresByCategory();
    }
>>>>>>> 562e5a8566790922276e16ed9e5c86cbb51a7c01
    const toggle       = document.getElementById("genreDropdown");
    const menu         = document.getElementById("genreMenu");
    const label        = document.getElementById("genreLabel");
    const filtersField = document.getElementById("search_filters");
    const checkboxes   = menu ? menu.querySelectorAll("input[type='checkbox']") : [];
    const searchForm   = document.querySelector('.search-container form');
    const radioProduct = document.getElementById("search_product");
    const radioArtist  = document.getElementById("search_artist");
    const searchInput  = document.getElementById("search_query_input");
    const searchButton = document.querySelector(".search-button");

    // ==========================
    // ドロップダウン開閉
    // ==========================
    if (toggle && menu) {
        toggle.addEventListener("click", function (e) {
            // 商品検索時は必ずジャンル選択可能
            e.stopPropagation();
            menu.classList.toggle("show");
            toggle.classList.toggle("open");
        });

        document.addEventListener("click", function (e) {
            if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                menu.classList.remove("show");
                toggle.classList.remove("open");
            }
        });
    }

    // ==========================
    // 素材ラベル更新
    // ==========================
    function updateLabel() {
        if (!checkboxes.length || !label) return;

        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        label.textContent = selected.length
            ? selected.join(", ")
            : "ジャンルを選択（複数可）"; // ← 文言をPHP側と合わせました
    }

    checkboxes.forEach(cb => cb.addEventListener("change", updateLabel));
    // 「すべてを選択」機能
    const selectAllCheckbox = Array.from(checkboxes).find(cb => cb.value === "すべてを選択");
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function() {
            const checked = selectAllCheckbox.checked;
            checkboxes.forEach(cb => {
                // 「すべてを選択」自身以外
                if (cb.value !== "すべてを選択") {
                    cb.checked = checked;
                }
            });
            updateLabel();
        });
    }
    updateLabel();

    // フォームのリセット時にラベルを更新（デフォルト値へ戻す）
    if (searchForm) {
        searchForm.addEventListener('reset', function () {
            // reset イベントはデフォルト動作の前後がブラウザ差があるため、
            // setTimeout で次のイベントループに回してから DOM の状態を読み直す
            setTimeout(function () {
                updateLabel();
                if (menu && toggle) {
                    menu.classList.remove('show');
                    toggle.classList.remove('open');
                }
            }, 0);
        });
    }

    // ==========================
    // 商品検索 / 作家検索 切り替え
    // ==========================
    function toggleFilters() {
        if (!filtersField || !searchInput || !searchButton) return;

        if (radioProduct && radioProduct.checked) {
            // ★ 商品検索：フィルター有効
            filtersField.disabled = false;
            // ジャンルのcheckboxを必ず有効化
            var genreCheckboxes = document.querySelectorAll('input[name="genre[]"]');
            genreCheckboxes.forEach(cb => cb.disabled = false);
            searchInput.placeholder = "商品名・キーワード";
            searchButton.textContent = "検索";
        } else {
            // ★ 作家検索：フィルターをすべて無効にして閉じる
            filtersField.disabled = true;
            // ジャンルのcheckboxをすべて無効化
            var genreCheckboxes = document.querySelectorAll('input[name="genre[]"]');
            genreCheckboxes.forEach(cb => cb.disabled = true);

            if (menu && toggle) {
                menu.classList.remove("show");
                toggle.classList.remove("open");
            }

            searchInput.placeholder = "作家名・キーワード";
            searchButton.textContent = "作家を検索";
        }
    }

    if (radioProduct && radioArtist) {
        radioProduct.addEventListener("change", toggleFilters);
        radioArtist.addEventListener("change", toggleFilters);
        // ページ読み込み時の初期状態を反映
        toggleFilters();
    }

});

