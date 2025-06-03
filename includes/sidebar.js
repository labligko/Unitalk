  function toggleSidebar() {
    const sidebar = document.getElementById('tambah');
    sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
  }

  window.onload = function () {
      if (window.location.pathname === '/index.php' || window.location.pathname === '/') {
          document.getElementById('home').classList.add('active');
      }
      else if (window.location.pathname === '/profile.php' || window.location.pathname === '/') {
          document.getElementById('profile').classList.add('active');
      }
  }
  
  document.getElementById('toggleSidebarButton').onclick = function () {
    toggleSidebar();
  };


  document.getElementById('searchButton').onclick = function () {
    var searchPopup = new bootstrap.Modal(document.getElementById('searchPopup'));
    searchPopup.show();
  };

  document.getElementById('notificationButton').onclick = function() {
    var notificationPopup = new bootstrap.Modal(document.getElementById('notificationPopup'));
    notificationPopup.show();
  };
  
  function showSearch() {
    var searchInput = document.getElementById("searchInput");
    var searchResults = document.getElementById("searchResults");

    if (!searchResults) {
      console.error("Elemen searchResults tidak ditemukan!");
      return;
    }

    if (searchInput.value.trim() !== "") {
      fetch('search.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            input_search: searchInput.value.trim()
          })
        })
        .then(response => response.json())
        .then(data => {
          searchResults.innerHTML = "";

          if (data.length > 0) {
            data.forEach(account => {
              const resultItem = `
                        <ul style="list-style: none; margin-bottom: 5px;">
                            <a href="search_profile.php?username=${account.username}" 
                               style="text-decoration: none; color: #364153;">
                                <li style="display: flex; align-items: center;">
                                    <img src="${account.foto_profil}" class="figure-img img-fluid" 
                                         alt="Gambar" style="width: 50px; height: 50px; object-fit: cover; 
                                         margin-bottom: 10px; border: 1px solid #364153; border-radius: 30px;">
                                    <h5 style="color: #364153; opacity: 0.8; margin-left: 10px;">
                                        @${account.username}
                                    </h5>
                                </li>
                            </a>
                        </ul>
                    `;
              searchResults.innerHTML += resultItem;
            });
          } else {
            searchResults.innerHTML = "<p>No results found</p>";
          }

          searchResults.style.display = "block";
        })
        .catch(error => {
          console.error("Error:", error);
          searchResults.style.display = "none";
        });
    } else {
      searchResults.style.display = "none";
    }
  }

