### Thesongisyours

TheSongsIsYours adalah platform yang dirancang untuk memungkinkan pengguna mengekspresikan pesan mendalam mereka melalui musik. Platform ini mendukung manipulasi data client-side, pengelolaan server-side, dan integrasi dengan database untuk mempermudah pengelolaan pesan dan album musik.

Link Hosting http : [thesongsisyours.great-site.net](http://thesongsisyours.great-site.net/index.php)
Link Hosting https : [thesongsisyours.great-site.net](http://thesongsisyours.great-site.net/index.php)

---

### **Bagian 1: Client-side Programming (30%)**

#### 1.1 **Manipulasi DOM dengan JavaScript (15%)**

**Kriteria:**

- Membuat form input dengan minimal 4 elemen (contoh: teks, checkbox, radio).
- Menampilkan data dari server ke dalam tabel HTML.

**Penerapan dalam Repository:**

- **Form Input**: Implementasi form untuk menambahkan album musik ditemukan di file `views/dashboard-albums.php`, khususnya pada bagian:

```html
<form
  id="albumForm"
  method="POST"
  action="../controllers/albums_controller.php?action=create"
>
  <label>
    Nama:
    <input
      type="text"
      name="owner_name"
      id="owner_name"
      placeholder="Nama pemilik album"
      required
    />
  </label>
  <label>
    Pesan:
    <textarea
      name="description"
      id="description"
      placeholder="Deskripsi album"
      required
    ></textarea>
  </label>
  <label>
    Spotify Embed URL:
    <input
      type="text"
      name="spotify_embed_url"
      id="spotify_embed_url"
      placeholder="URL embed Spotify"
      required
    />
  </label>
</form>
```

- **Tabel HTML**: Data yang dimuat dari server ditampilkan dalam tabel di bagian yang sama:

```html
<table id="albumTable">
  <thead>
    <tr>
      <th>Nama Pemilik</th>
      <th>Pesan</th>
      <th>Spotify Embed</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <!-- Data album akan dimuat menggunakan JavaScript -->
  </tbody>
</table>
```

- **JavaScript**: Data dimuat dari server dan dirender menggunakan JavaScript di file `public/js/albums.js`:

```javascript
function loadAlbums() {
  fetch("../controllers/albums_controller.php?action=fetch", { method: "GET" })
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
```

---

#### 1.2 **Event Handling (15%)**

**Kriteria:**

- Tambahkan minimal 3 event untuk meng-handle form.
- Implementasi validasi input dengan JavaScript sebelum diproses oleh PHP.

**Penerapan dalam Repository:**

- **Event Handling**: File `public/js/albums.js` mengimplementasikan event handling untuk berbagai aksi:

  1. **Submit Form**: Event listener pada pengiriman form.

     ```javascript
     albumForm.addEventListener("submit", function (e) {
       e.preventDefault();
       const formData = new URLSearchParams({
         owner_name: ownerNameInput.value.trim(),
         description: descriptionInput.value.trim(),
         spotify_embed_url: spotifyEmbedUrlInput.value.trim(),
       });
       fetch(`../controllers/albums_controller.php?action=create`, {
         method: "POST",
         body: formData,
       })
         .then((response) => response.json())
         .then((data) => {
           alert(data.message || data.error);
           resetTable();
           loadAlbums();
         })
         .catch((error) => {
           console.error("Error:", error);
           alert("Gagal menambah album.");
         });
     });
     ```

  2. **Edit Data**: Event listener pada tombol edit untuk mengisi form dengan data yang diambil dari server:

     ```javascript
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
           } else {
             alert("Album tidak ditemukan.");
           }
         });
     });
     ```

  3. **Validasi Input**: Validasi form sebelum pengiriman, seperti pada fungsi berikut:
     ```javascript
     function validateForm() {
       const isValid =
         ownerNameInput.value.trim() && spotifyEmbedUrlInput.value.trim();
       submitButton.disabled = !isValid;
     }
     ownerNameInput.addEventListener("input", validateForm);
     descriptionInput.addEventListener("input", validateForm);
     spotifyEmbedUrlInput.addEventListener("input", validateForm);
     ```

Poin-poin tersebut menunjukkan implementasi manipulasi DOM dan event handling yang memenuhi kriteria.

---

### **Bagian 2: Server-side Programming (30%)**

#### 2.1 **Pengelolaan Data dengan PHP (20%)**

**Kriteria:**

- Gunakan metode POST/GET pada formulir.
- Validasi data dari variabel global di sisi server.
- Simpan data ke basis data (termasuk jenis browser dan alamat IP pengguna).

**Penerapan dalam Repository:**

- **Metode POST pada Formulir**: Pada file `controllers/albums_controller.php`, metode POST digunakan untuk menerima data dari formulir dan menyimpannya ke basis data.

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $id = generateUuid();
    $owner_name = htmlspecialchars($_POST['owner_name']);
    $description = htmlspecialchars($_POST['description']);
    $spotify_embed_url = htmlspecialchars($_POST['spotify_embed_url']);

    $stmt = $conn->prepare("INSERT INTO music_albums (id, owner_name, description, spotify_embed_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $id, $owner_name, $description, $spotify_embed_url);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Album berhasil ditambahkan!", "id" => $id]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan album."]);
    }
    $stmt->close();
    exit;
}
```

- **Validasi Data**: Variabel global `$_POST` dan `$_GET` divalidasi menggunakan fungsi `htmlspecialchars()` untuk menghindari serangan XSS:

```php
$owner_name = htmlspecialchars($_POST['owner_name']);
$description = htmlspecialchars($_POST['description']);
$spotify_embed_url = htmlspecialchars($_POST['spotify_embed_url']);
```

- **Penyimpanan Data ke Database**: Data seperti `owner_name`, `description`, dan `spotify_embed_url` disimpan ke dalam tabel `music_albums`.

- **Jenis Browser dan Alamat IP Pengguna**: Pada file `controllers/process_user.php`, data `ip_address` dan `browser` disimpan ke dalam basis data:

```php
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

// Jika IPv6 localhost (::1), ganti dengan IPv4 localhost (127.0.0.1)
if ($ip === '::1') {
    $ip = 'IP Local : (127.0.0.1)';
}

$stmt = $conn->prepare("INSERT INTO users (name, email, password, ip_address, browser) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashedPassword, $ip, $browser);
```

---

#### 2.2 **Objek PHP Berbasis OOP (10%)**

**Kriteria:**

- Buat objek PHP berbasis OOP dengan minimal dua metode.
- Gunakan objek tersebut dalam skenario tertentu.

**Penerapan dalam Repository:**

- **Objek PHP dengan Dua Metode**: Pada file `models/User.php`, terdapat sebuah kelas `User` dengan dua metode utama:
  - `findByEmail()`: Mencari pengguna berdasarkan email.
  - `verifyPassword()`: Memverifikasi kecocokan password dengan hash.

```php
class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Gagal mempersiapkan query: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null; // Jika tidak ditemukan, kembalikan null
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    public function verifyPassword($inputPassword, $storedHash)
    {
        return password_verify($inputPassword, $storedHash);
    }
}
```

- **Penggunaan Objek**: Objek kelas `User` digunakan dalam file `controllers/user_controller.php` untuk login pengguna.

```php
$userModel = new User($conn);
$user = $userModel->findByEmail($email);

if ($user && $userModel->verifyPassword($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['ip_address'] = $user['ip_address'];
    $_SESSION['browser'] = $user['browser'];
    $_SESSION['is_logged_in'] = true;

    echo json_encode(["success" => "Login berhasil.", "redirect" => "dashboard-albums.php"]);
}
```

Kode di atas memenuhi kriteria poin 2.1 dan 2.2 dengan mengelola data menggunakan metode GET/POST, validasi server-side, penyimpanan database, dan implementasi berbasis OOP.

---

### **Bagian 3: Database Management (20%)**

#### 3.1 **Pembuatan Tabel Database (5%)**

**Kriteria:**

- Buat tabel database sesuai kebutuhan aplikasi.

**Penerapan dalam Repository:**

Pada file `config/table.sql`, terdapat definisi pembuatan tabel `users` dan `music_albums` yang digunakan untuk menyimpan data pengguna dan album musik.

```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    browser VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE music_albums (
    id CHAR(36) PRIMARY KEY,
    owner_name VARCHAR(255) NOT NULL,
    description TEXT,
    spotify_embed_url TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

#### 3.2 **Konfigurasi Koneksi Database (5%)**

**Kriteria:**

- Implementasikan koneksi ke database dengan pengaturan yang benar.

**Penerapan dalam Repository:**

Pada file `config/db_config.php`, terdapat konfigurasi koneksi database dengan pengaturan yang benar, termasuk validasi koneksi untuk memastikan koneksi berhasil.

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pemweb-uas-felix";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
```

---

#### 3.3 **Manipulasi Data pada Database (10%)**

**Kriteria:**

- Implementasi CRUD (Create, Read, Update, Delete) data pada database.

**Penerapan dalam Repository:**

**CRUD** diimplementasikan pada file `controllers/albums_controller.php` untuk tabel `music_albums`:

1. **Create (Menambahkan Data Baru)**:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
       $id = generateUuid();
       $owner_name = htmlspecialchars($_POST['owner_name']);
       $description = htmlspecialchars($_POST['description']);
       $spotify_embed_url = htmlspecialchars($_POST['spotify_embed_url']);

       $stmt = $conn->prepare("INSERT INTO music_albums (id, owner_name, description, spotify_embed_url) VALUES (?, ?, ?, ?)");
       $stmt->bind_param("ssss", $id, $owner_name, $description, $spotify_embed_url);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Album berhasil ditambahkan!", "id" => $id]);
       } else {
           echo json_encode(["error" => "Gagal menambahkan album."]);
       }
       $stmt->close();
       exit;
   }
   ```

2. **Read (Membaca Data)**:

   - Semua data:
     ```php
     if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
         $query = "SELECT * FROM music_albums";
         $result = $conn->query($query);
         $albums = [];
         while ($row = $result->fetch_assoc()) {
             $albums[] = $row;
         }
         echo json_encode($albums);
         exit;
     }
     ```
   - Berdasarkan ID:

     ```php
     if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetchById') {
         $id = htmlspecialchars($_GET['id']);
         $stmt = $conn->prepare("SELECT * FROM music_albums WHERE id = ?");
         $stmt->bind_param("s", $id);
         $stmt->execute();
         $result = $stmt->get_result();

         if ($result->num_rows > 0) {
             echo json_encode($result->fetch_assoc());
         } else {
             echo json_encode(["error" => "Album tidak ditemukan."]);
         }
         $stmt->close();
         exit;
     }
     ```

3. **Update (Memperbarui Data)**:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
       $id = htmlspecialchars($_POST['id']);
       $owner_name = htmlspecialchars($_POST['owner_name']);
       $description = htmlspecialchars($_POST['description']);
       $spotify_embed_url = htmlspecialchars($_POST['spotify_embed_url']);

       $stmt = $conn->prepare("UPDATE music_albums SET owner_name = ?, description = ?, spotify_embed_url = ? WHERE id = ?");
       $stmt->bind_param("ssss", $owner_name, $description, $spotify_embed_url, $id);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Album berhasil diperbarui!"]);
       } else {
           echo json_encode(["error" => "Gagal memperbarui album."]);
       }
       $stmt->close();
       exit;
   }
   ```

4. **Delete (Menghapus Data)**:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
       $id = htmlspecialchars($_POST['id']);
       $stmt = $conn->prepare("DELETE FROM music_albums WHERE id = ?");
       $stmt->bind_param("s", $id);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Album berhasil dihapus!"]);
       } else {
           echo json_encode(["error" => "Gagal menghapus album."]);
       }
       $stmt->close();
       exit;
   }
   ```

Kode di atas memenuhi kriteria poin 3.1, 3.2, dan 3.3 dengan implementasi tabel, koneksi database, dan operasi CRUD.

---

### **Bagian 4: State Management (20%)**

#### 4.1 **State Management dengan Session (10%)**

**Kriteria:**

- Gunakan `session_start()` untuk memulai sesi.
- Simpan informasi pengguna ke dalam session.

**Penerapan dalam Repository:**

1. **Memulai Sesi**:
   Pada file `session/session.php` dan beberapa file lain seperti `views/dashboard-albums.php`, sesi dimulai dengan `session_start()`:

   ```php
   <?php
   session_start();
   if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
       header("Location: ../views/login.php");
       exit;
   }

   $userName = $_SESSION['user_name'];
   ?>
   ```

2. **Menyimpan Informasi ke dalam Session**:
   Pada file `controllers/user_controller.php`, setelah validasi login berhasil, data pengguna disimpan ke dalam session:

   ```php
   $_SESSION['user_id'] = $user['id'];
   $_SESSION['user_name'] = $user['name'];
   $_SESSION['ip_address'] = $user['ip_address'];
   $_SESSION['browser'] = $user['browser'];
   $_SESSION['is_logged_in'] = true;
   ```

3. **Menggunakan Data Session**:
   Pada file `views/dashboard-albums.php`, data dari session ditampilkan ke halaman dashboard:
   ```php
   <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>
   <p>IP Address: <strong><?php echo htmlspecialchars($_SESSION['ip_address']); ?></strong></p>
   <p>Browser: <strong><?php echo htmlspecialchars($_SESSION['browser']); ?></strong></p>
   ```

---

#### 4.2 **Pengelolaan State dengan Cookie dan Browser Storage (10%)**

**Kriteria:**

- Buat fungsi untuk menetapkan, mendapatkan, dan menghapus cookie.
- Gunakan browser storage untuk menyimpan informasi secara lokal.

**Penerapan dalam Repository:**

1. **Cookie Management**:
   Pada file `views/cookie-local-session.html`, terdapat form untuk menetapkan, mendapatkan, dan menghapus cookie:

   ```javascript
   // Menetapkan Cookie
   document.getElementById("setCookie").addEventListener("click", () => {
     const value = cookieInput.value.trim();
     if (!value) {
       alert("Masukkan nilai terlebih dahulu!");
       return;
     }
     document.cookie = `TheSongsIsYoursCookie=${value}; path=/; max-age=3600`; // Berlaku 1 jam
     updateDisplays();
   });

   // Menghapus Cookie
   document.getElementById("deleteCookie").addEventListener("click", () => {
     document.cookie = "TheSongsIsYoursCookie=; path=/; max-age=0"; // Hapus cookie
     updateDisplays();
   });

   // Memperbarui Tampilan Cookie
   function updateDisplays() {
     const cookies = document.cookie.split("; ").reduce((acc, cookie) => {
       const [key, value] = cookie.split("=");
       acc[key] = value;
       return acc;
     }, {});
     cookieDisplay.textContent =
       cookies.TheSongsIsYoursCookie || "Belum ada nilai";
   }
   ```

2. **Browser Storage Management**:
   Pada file yang sama (`views/cookie-local-session.html`), terdapat fungsi untuk mengelola `localStorage` dan `sessionStorage`:

   ```javascript
   // Menetapkan LocalStorage
   document.getElementById("setLocalStorage").addEventListener("click", () => {
     const value = localStorageInput.value.trim();
     if (!value) {
       alert("Masukkan nilai terlebih dahulu!");
       return;
     }
     localStorage.setItem("TheSongsIsYoursLocalStorage", value);
     updateDisplays();
   });

   // Menghapus LocalStorage
   document
     .getElementById("deleteLocalStorage")
     .addEventListener("click", () => {
       localStorage.removeItem("TheSongsIsYoursLocalStorage");
       updateDisplays();
     });

   // Menetapkan SessionStorage
   document
     .getElementById("setSessionStorage")
     .addEventListener("click", () => {
       const value = sessionStorageInput.value.trim();
       if (!value) {
         alert("Masukkan nilai terlebih dahulu!");
         return;
       }
       sessionStorage.setItem("TheSongsIsYoursSessionStorage", value);
       updateDisplays();
     });

   // Menghapus SessionStorage
   document
     .getElementById("deleteSessionStorage")
     .addEventListener("click", () => {
       sessionStorage.removeItem("TheSongsIsYoursSessionStorage");
       updateDisplays();
     });
   ```

3. **UI untuk Pengelolaan State**:
   Form HTML untuk menetapkan dan mengelola cookie, local storage, dan session storage:

   ```html
   <!-- Form untuk Cookie -->
   <form id="cookieForm">
     <label for="cookieInput">Masukkan Nilai Cookie:</label>
     <input
       type="text"
       id="cookieInput"
       placeholder="Masukkan nilai..."
       required
     />
     <button type="button" class="button btn-set" id="setCookie">
       Set Cookie
     </button>
     <button type="button" class="button btn-delete" id="deleteCookie">
       Hapus Cookie
     </button>
   </form>
   <p>
     <strong>Nilai Cookie:</strong>
     <span id="cookieDisplay">Belum ada nilai</span>
   </p>

   <!-- Form untuk LocalStorage -->
   <form id="localStorageForm">
     <label for="localStorageInput">Masukkan Nilai LocalStorage:</label>
     <input
       type="text"
       id="localStorageInput"
       placeholder="Masukkan nilai..."
       required
     />
     <button type="button" class="button btn-set" id="setLocalStorage">
       Set LocalStorage
     </button>
     <button type="button" class="button btn-delete" id="deleteLocalStorage">
       Hapus LocalStorage
     </button>
   </form>
   <p>
     <strong>Nilai LocalStorage:</strong>
     <span id="localStorageDisplay">Belum ada nilai</span>
   </p>

   <!-- Form untuk SessionStorage -->
   <form id="sessionStorageForm">
     <label for="sessionStorageInput">Masukkan Nilai SessionStorage:</label>
     <input
       type="text"
       id="sessionStorageInput"
       placeholder="Masukkan nilai..."
       required
     />
     <button type="button" class="button btn-set" id="setSessionStorage">
       Set SessionStorage
     </button>
     <button type="button" class="button btn-delete" id="deleteSessionStorage">
       Hapus SessionStorage
     </button>
   </form>
   <p>
     <strong>Nilai SessionStorage:</strong>
     <span id="sessionStorageDisplay">Belum ada nilai</span>
   </p>
   ```

Kode di atas memenuhi kriteria 4.1 dan 4.2 dengan pengelolaan state menggunakan session, cookie, local storage, dan session storage.

---

### **Bagian Bonus: Hosting Aplikasi Web (20%)**

# **Panduan Hosting Aplikasi Web - TheSongsIsYours**

TheSongsIsYours adalah platform berbasis PHP dan MySQL untuk menyampaikan pesan mendalam melalui musik. Panduan ini akan menjelaskan langkah-langkah untuk meng-host aplikasi web menggunakan **InfinityFree**, termasuk alasan memilih InfinityFree, keamanan aplikasi, dan konfigurasi yang diperlukan.

---

## **Bagian 1: Langkah-langkah Hosting Aplikasi Web**

Berikut langkah-langkah untuk meng-host aplikasi web ini di **InfinityFree**:

1. **Persiapkan File Aplikasi**:

   - Pastikan semua file codingan Anda siap untuk di-upload, termasuk file PHP, CSS, JavaScript, dan folder database SQL.

2. **Login ke InfinityFree**:

   - Masuk ke akun **InfinityFree** Anda melalui [https://infinityfree.net/](https://infinityfree.net/).

3. **Siapkan Database MySQL**:

   - Buka menu **Control Panel** di InfinityFree.
   - Pilih opsi **MySQL Databases**.
   - Buat database baru dengan nama yang relevan untuk aplikasi Anda.

4. **Buat Akun Pengguna Database**:

   - Catat informasi yang diberikan oleh InfinityFree, seperti nama database, username, password, dan host database. Informasi ini akan digunakan untuk mengkonfigurasi aplikasi Anda.

5. **Sesuaikan File Konfigurasi Database**:

   - Edit file `config/db_config.php` pada aplikasi Anda agar sesuai dengan informasi yang diberikan oleh InfinityFree:
     ```php
     <?php
     $servername = "sqlXXX.infinityfree.com"; // Host database dari InfinityFree
     $username = "epiz_XXXXXXXX";            // Username database dari InfinityFree
     $password = "password_anda";           // Password database yang digenerate oleh InfinityFree
     $dbname = "epiz_XXXXXXXX_database";    // Nama database yang Anda buat
     $conn = new mysqli($servername, $username, $password, $dbname);
     if ($conn->connect_error) {
         die("Koneksi gagal: " . $conn->connect_error);
     }
     ?>
     ```

6. **Upload File ke Hosting**:

   - Gunakan **File Manager** atau software FTP seperti **FileZilla**.
   - Upload seluruh file aplikasi Anda ke folder **htdocs**. Pastikan file `index.php` berada di direktori root **htdocs**.

7. **Tes Aplikasi**:
   - Buka URL aplikasi Anda (contoh: `http://yourapp.infinityfreeapp.com`) untuk memastikan semuanya berjalan dengan lancar.

---

## **Bagian 2: Mengapa Memilih InfinityFree?**

**InfinityFree** adalah platform hosting web yang ideal untuk proyek ini karena:

- **Gratis dan Cocok untuk Pemula**: InfinityFree menyediakan hosting gratis dengan fitur yang cukup untuk meng-host aplikasi berbasis PHP dan MySQL.
- **Dukungan PHP dan MySQL**: Platform ini sepenuhnya kompatibel dengan teknologi yang digunakan pada aplikasi TheSongsIsYours.
- **Ruang Penyimpanan Unlimited**: Anda tidak perlu khawatir tentang batas penyimpanan untuk file aplikasi.
- **Custom Domain**: Mendukung penggunaan domain custom atau domain gratis yang disediakan oleh InfinityFree.

---

## **Bagian 3: Keamanan Aplikasi Web yang Di-host**

InfinityFree menyediakan beberapa fitur keamanan penting:

1. **Password Database yang Aman**:

   - Password untuk database hanya bisa diakses dengan generator password acak dari InfinityFree, sehingga lebih aman dari upaya peretasan.

2. **Dukungan HTTPS**:

   - InfinityFree mendukung HTTPS gratis menggunakan **SSL Certificate**, yang memastikan bahwa data antara pengguna dan server terenkripsi dengan baik.

3. **Isolasi Aplikasi**:
   - Setiap akun hosting terisolasi dari akun lain untuk mencegah pelanggaran keamanan antar situs.

---

## **Bagian 4: Konfigurasi Server**

### **1. Setup Database yang Benar**:

- Pastikan nama database, username, password, dan host database di file `db_config.php` sesuai dengan yang disediakan oleh InfinityFree.

### **2. File `index.php` di Root Folder**:

- Semua file aplikasi harus di-upload ke folder **htdocs**, dan file utama `index.php` harus berada di direktori root agar aplikasi dapat diakses.

### **3. Pengujian Fungsi CRUD**:

- Pastikan semua fungsi seperti Create, Read, Update, dan Delete (CRUD) pada database berjalan tanpa error. Gunakan tools seperti **phpMyAdmin** untuk mengelola database.
