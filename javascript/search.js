document.addEventListener("DOMContentLoaded", function () {
    const toggle       = document.getElementById("genreDropdown");
    const menu         = document.getElementById("genreMenu");
    const label        = document.getElementById("genreLabel");
    const checkboxes   = menu ? menu.querySelectorAll("input[type='checkbox']") : [];
    const radioProduct = document.getElementById("search_product");
    const radioArtist  = document.getElementById("search_artist");
    const filtersField = document.getElementById("search_filters");
    const searchInput  = document.getElementById("search_query_input");
    const searchButton = document.querySelector(".search-button");

    // ドロップダウン開閉
    if (toggle && menu) {
        toggle.addEventListener("click", function (e) {
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

    // 素材ラベル更新
    function updateLabel() {
        if (!checkboxes.length || !label) return;

        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        label.textContent = selected.length
            ? selected.join(", ")
            : "素材を選択（複数可）";
    }
    checkboxes.forEach(cb => cb.addEventListener("change", updateLabel));
    updateLabel();

    // 商品検索 / 作家検索 切り替え
    function toggleFilters() {
        if (!filtersField || !searchInput || !searchButton) return;

        if (radioProduct && radioProduct.checked) {
            filtersField.disabled = false;
            searchInput.placeholder = "商品名・キーワード";
            searchButton.textContent = "検索";
        } else {
            filtersField.disabled = true;
            searchInput.placeholder = "作家名・キーワード";
            searchButton.textContent = "作家を検索";
        }
    }

    if (radioProduct && radioArtist) {
        radioProduct.addEventListener("change", toggleFilters);
        radioArtist.addEventListener("change", toggleFilters);
        toggleFilters(); // 初期反映
    }
});
