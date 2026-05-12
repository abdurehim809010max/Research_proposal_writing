/* ============================================
   Live Search - AJAX/Fetch (Asynchronous)
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('liveSearch');
    var searchResults = document.getElementById('searchResults');

    if (!searchInput || !searchResults) return;

    var searchTimer;
    var siteUrl = document.querySelector('link[rel="stylesheet"]').href;
    // Extract base URL from stylesheet href
    var baseUrl = siteUrl.substring(0, siteUrl.indexOf('/css/'));

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        var query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.remove('active');
            return;
        }

        searchTimer = setTimeout(function () {
            // Asynchronous fetch call for live search
            fetch(baseUrl + '/api/search.php?q=' + encodeURIComponent(query))
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    displaySearchResults(data.results);
                })
                .catch(function (err) {
                    searchResults.innerHTML = '<div class="search-no-results">Search unavailable</div>';
                    searchResults.classList.add('active');
                });
        }, 300);
    });

    function displaySearchResults(results) {
        searchResults.innerHTML = '';

        if (results.length === 0) {
            searchResults.innerHTML = '<div class="search-no-results"><i class="fas fa-search"></i> No items found</div>';
            searchResults.classList.add('active');
            return;
        }

        results.forEach(function (item) {
            var div = document.createElement('div');
            div.className = 'search-result-item';
            div.innerHTML =
                '<div class="search-result-info">' +
                    '<h4>' + escapeHtml(item.name) + '</h4>' +
                    '<p>' + escapeHtml(item.description) + '</p>' +
                    '<span class="search-category">' + escapeHtml(item.category) + '</span>' +
                '</div>' +
                '<span class="search-result-price">' + item.price + ' ETB</span>';

            div.addEventListener('click', function () {
                window.location.href = baseUrl + '/menu.php';
            });

            searchResults.appendChild(div);
        });

        searchResults.classList.add('active');
    }

    // Close search results when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
});
