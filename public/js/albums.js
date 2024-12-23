document.addEventListener("DOMContentLoaded", function () {
  const albumForm = document.getElementById("albumForm");
  const ownerNameInput = document.getElementById("owner_name");
  const descriptionInput = document.getElementById("description");
  const spotifyEmbedUrlInput = document.getElementById("spotify_embed_url");
  const submitButton = albumForm.querySelector("button[type='submit']");
  const albumTable = document
    .getElementById("albumTable")
    .querySelector("tbody");

  let editMode = false;
  let editId = null;

  // Validasi form
  function validateForm() {
    const isValid =
      ownerNameInput.value.trim() && spotifyEmbedUrlInput.value.trim();

    submitButton.disabled = !isValid;
  }

  // Tambahkan baris album ke tabel
  function addAlbumRow(album) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${album.owner_name}</td>
      <td>${album.description || "Tidak ada deskripsi"}</td>
      <td><div class="spotify-embed"></div></td>
      <td>
        <button class="edit-btn btn-green" data-id="${album.id}">Edit</button>
        <button class="delete-btn btn-red" data-id="${album.id}">Hapus</button>
      </td>
    `;

    // Decode HTML entity to render iframe properly
    const spotifyEmbedCell = row.querySelector(".spotify-embed");
    spotifyEmbedCell.innerHTML = decodeHtmlEntities(album.spotify_embed_url);

    albumTable.appendChild(row);
    attachRowEventListeners(row);
  }

  // Reset tabel
  function resetTable() {
    albumTable.innerHTML = "";
  }

  // Event listener pada tombol Edit dan Hapus
  function attachRowEventListeners(row) {
    const editBtn = row.querySelector(".edit-btn");
    const deleteBtn = row.querySelector(".delete-btn");

    editBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      fetch(`../controllers/albums_controller.php?action=fetchById&id=${id}`)
        .then((response) => response.json())
        .then((data) => {
          if (data) {
            ownerNameInput.value = data.owner_name;
            descriptionInput.value = data.description;
            spotifyEmbedUrlInput.value = data.spotify_embed_url;

            editMode = true;
            editId = id;
            submitButton.textContent = "Update Album";
          } else {
            alert("Album tidak ditemukan.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Gagal mengambil data album.");
        });
    });

    deleteBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      if (confirm("Yakin ingin menghapus album ini?")) {
        fetch(`../controllers/albums_controller.php?action=delete`, {
          method: "POST",
          body: new URLSearchParams({ id }),
        })
          .then((response) => response.json())
          .then((data) => {
            alert(data.message || data.error);
            resetTable();
            loadAlbums();
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Gagal menghapus album.");
          });
      }
    });
  }

  // Submit form
  albumForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new URLSearchParams({
      owner_name: ownerNameInput.value.trim(),
      description: descriptionInput.value.trim(),
      spotify_embed_url: spotifyEmbedUrlInput.value.trim(),
    });

    const action = editMode ? "update" : "create";
    if (editMode) {
      formData.append("id", editId);
    }

    fetch(`../controllers/albums_controller.php?action=${action}`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message || data.error);
        resetTable();
        loadAlbums();
        albumForm.reset();
        submitButton.textContent = "Tambah Album";
        editMode = false;
      })
      .catch((error) => {
        console.error("Error:", error);
        alert(
          `Terjadi kesalahan saat ${
            editMode ? "memperbarui" : "menambah"
          } album.`
        );
      });
  });

  // Muat album dari server
  function loadAlbums() {
    fetch("../controllers/albums_controller.php?action=fetch", {
      method: "GET",
    })
      .then((response) => response.json())
      .then((albums) => {
        resetTable();
        albums.forEach((album) => addAlbumRow(album));
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Gagal memuat data album.");
      });
  }

  // Inisialisasi
  validateForm();
  loadAlbums();

  ownerNameInput.addEventListener("input", validateForm);
  descriptionInput.addEventListener("input", validateForm);
  spotifyEmbedUrlInput.addEventListener("input", validateForm);
});

function decodeHtmlEntities(encodedString) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(encodedString, "text/html");
  return doc.documentElement.textContent;
}
