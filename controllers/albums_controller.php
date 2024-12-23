<?php
include '../config/db_config.php';

header('Content-Type: application/json');

// Ambil parameter 'action'
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Ambil semua album
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

// Ambil album berdasarkan ID
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

// Tambahkan album baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $id = generateUuid(); // Hasilkan UUID
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

// Perbarui album
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

// Hapus album
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

// Jika 'action' tidak valid
http_response_code(400);
echo json_encode(["error" => "Parameter 'action' tidak valid."]);
$conn->close();

// Fungsi untuk menghasilkan UUID
function generateUuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
?>