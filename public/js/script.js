function confirmDelete(url) {
    if (confirm('確定要刪除這筆資料嗎？')) {
        location.href = url;
    }
}