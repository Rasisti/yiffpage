<?php
// Ensure folders exist
if (!is_dir("uploads")) mkdir("uploads");
if (!is_dir("data")) mkdir("data");
if (!file_exists("data/videos.json")) file_put_contents("data/videos.json", "[]");
if (!file_exists("data/users.json")) file_put_contents("data/users.json", "{}");

$userId = $_POST['user_id'] ?? '';
$username = trim($_POST['username'] ?? '');
$title = trim($_POST['title'] ?? '');

if (!$userId || !$username || !$title || !isset($_FILES['video'])) {
  http_response_code(400);
  exit("Missing required fields.");
}

$users = json_decode(file_get_contents("data/users.json"), true);
$users[$userId] = $username;
file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));

$video = $_FILES['video'];
$ext = pathinfo($video['name'], PATHINFO_EXTENSION);
$filename = uniqid("vid_") . "." . $ext;

move_uploaded_file($video['tmp_name'], "uploads/" . $filename);

$videos = json_decode(file_get_contents("data/videos.json"), true);
$videos[] = [
  "title" => htmlspecialchars($title),
  "file" => $filename,
  "username" => htmlspecialchars($username),
  "timestamp" => time()
];
file_put_contents("data/videos.json", json_encode($videos, JSON_PRETTY_PRINT));

echo "Video uploaded successfully!";
?>
