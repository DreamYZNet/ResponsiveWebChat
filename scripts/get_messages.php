<?php
require "connect.php";

// Allow from to be unset
$from = isset($_GET['from']) ? $_GET['from'] : 0;

// Retrieve all messages
$stmt = $sql->prepare(
  "SELECT * FROM(
    SELECT messages.content, messages.date, messages.id, users.username 
    FROM messages LEFT JOIN users ON messages.userid = users.id 
    WHERE messages.id >= ? AND hidden = FALSE ORDER BY messages.id DESC LIMIT 100
  )as main ORDER BY id");
$stmt->bind_param("i", $from);
$stmt->execute() or die ('Error');
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows);

$stmt->close();
$sql->close();
?>