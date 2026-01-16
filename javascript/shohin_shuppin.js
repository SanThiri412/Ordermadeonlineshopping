// 画像プレビュー機能
document.getElementById('goods_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('imagePreview');
            preview.src = event.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// ジャンルドロップダウンの制御
const genreDropdown = document.getElementById('genreDropdown');
const genreMenu = document.getElementById('genreMenu');
const genreLabel = document.getElementById('genreLabel');
const genreCheckboxes = document.querySelectorAll('input[name="genre[]"]');

genreDropdown.addEventListener('click', function(e) {
    e.stopPropagation();
    genreMenu.classList.toggle('show');
});
document.addEventListener('click', function() {
    genreMenu.classList.remove('show');
});
genreMenu.addEventListener('click', function(e) {
    e.stopPropagation();
});
function updateGenreLabel() {
    const selected = [];
    genreCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selected.push(checkbox.value);
        }
    });
    genreLabel.textContent = selected.length > 0 ? selected.join(', ') : 'ジャンルを選択（複数可）';
}
genreCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateGenreLabel);
});