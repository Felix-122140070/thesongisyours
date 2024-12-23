document.addEventListener("DOMContentLoaded", function () {
  const productForm = document.getElementById("productForm");
  const nameInput = document.getElementById("name");
  const descriptionInput = document.getElementById("description");
  const priceInput = document.getElementById("price");
  const imageUrlInput = document.getElementById("image_url");
  const stockInput = document.getElementById("stock");
  const submitButton = productForm.querySelector("button[type='submit']");
  const productTable = document
    .getElementById("productTable")
    .querySelector("tbody");

  let editMode = false;
  let editId = null;

  // Fungsi untuk validasi form
  function validateForm() {
    const isValid =
      nameInput.value.trim() &&
      descriptionInput.value.trim() &&
      priceInput.value.trim() &&
      imageUrlInput.value.trim() &&
      stockInput.value.trim();

    submitButton.disabled = !isValid;
  }

  // Fungsi untuk menambahkan baris produk ke tabel
  function addProductRow(product) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${product.name}</td>
      <td>${product.description}</td>
      <td>Rp ${parseFloat(product.price).toLocaleString("id-ID", {
        minimumFractionDigits: 2,
      })}</td>
      <td><a href="${product.image_url}" target="_blank">Lihat Gambar</a></td>
      <td>${product.stock}</td>
      <td>
        <button class="edit-btn" data-id="${product.id}">Edit</button>
        <button class="delete-btn" data-id="${product.id}">Delete</button>
      </td>
    `;
    productTable.appendChild(row);
    attachRowEventListeners(row);
  }

  // Fungsi untuk reset tabel
  function resetTable() {
    productTable.innerHTML = "";
  }

  // Fungsi untuk menambah event listener pada tombol Edit dan Delete
  function attachRowEventListeners(row) {
    const editBtn = row.querySelector(".edit-btn");
    const deleteBtn = row.querySelector(".delete-btn");

    editBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      // Kirim permintaan ke server untuk mendapatkan data asli produk
      fetch(`../controllers/products_controller.php?action=fetchById&id=${id}`)
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          if (data) {
            // Isi form dengan data asli dari server
            nameInput.value = data.name;
            descriptionInput.value = data.description;
            priceInput.value = data.price; // Data asli berupa angka dari server
            imageUrlInput.value = data.image_url;
            stockInput.value = data.stock;

            editMode = true;
            editId = id;
            submitButton.textContent = "Update Produk";
            validateForm();
          } else {
            alert("Data produk tidak ditemukan.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Gagal mengambil data produk.");
        });
    });

    deleteBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      if (confirm("Yakin ingin menghapus produk ini?")) {
        fetch(`../controllers/products_controller.php?action=delete`, {
          method: "POST",
          body: new URLSearchParams({ id }),
        })
          .then((response) => response.json())
          .then((message) => {
            alert(message.message || message.error);
            row.remove();
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menghapus produk.");
          });
      }
    });
  }

  // Event listener untuk submit form
  productForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new URLSearchParams({
      name: nameInput.value.trim(),
      description: descriptionInput.value.trim(),
      price: priceInput.value.trim(),
      image_url: imageUrlInput.value.trim(),
      stock: stockInput.value.trim(),
    });

    if (editMode) {
      formData.append("id", editId);
      fetch(`../controllers/products_controller.php?action=update`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message || data.error);
          resetTable();
          loadProducts();
          productForm.reset();
          submitButton.textContent = "Tambah Produk";
          editMode = false;
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat mengupdate produk.");
        });
    } else {
      fetch(`../controllers/products_controller.php?action=create`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message || data.error);
          resetTable();
          loadProducts();
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat menambahkan produk.");
        });
    }
  });

  // Fungsi untuk memuat produk dari server
  function loadProducts() {
    fetch("../controllers/products_controller.php?action=fetch")
      .then((response) => response.json())
      .then((data) => {
        resetTable();
        data.forEach((product) => addProductRow(product));
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat memuat data produk.");
      });
  }

  // Validasi form pada perubahan input
  nameInput.addEventListener("input", validateForm);
  descriptionInput.addEventListener("input", validateForm);
  priceInput.addEventListener("input", validateForm);
  imageUrlInput.addEventListener("input", validateForm);
  stockInput.addEventListener("input", validateForm);

  // Inisialisasi
  validateForm();
  loadProducts();
});
