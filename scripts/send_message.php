<?php
require "connect.php";

// Make all posted data safe
$_POST["message"] = trim($_POST["message"]);
$_POST["message"] = htmlspecialchars($_POST["message"]);
$_POST["username"] = htmlspecialchars($_POST["username"]);
$_POST["password"] = htmlspecialchars($_POST["password"]);

// Disallow very long messages
if (strlen($_POST["message"]) > 10000){
    $sql->close();
    exit("No bee movie scripts!");
}

// Disallow blank messages
if ($_POST["message"] == ""){
    $sql->close();
    exit("No blank messages allowed!");
}

// Username creation / password authentication
$userid = null;
if ($_POST["username"] != "") {
    $stmt = $sql->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
	// If no user was found
    if ($result->num_rows === 0) {
        //create user
        $stmt2 = $sql->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt2->bind_param("ss", $_POST["username"], $_POST["password"]);
        $stmt2->execute();
        $userid = $sql->insert_id;
	// If user was found
    }else{
        //authenticate
        $assoc = $result->fetch_assoc();
        if ($assoc["password"] != $_POST["password"]){
            $sql->close();
            exit ("Password is incorrect!");}
        $userid = $assoc["id"];
    }
}

// Save message to database
$stmt = $sql->prepare("INSERT INTO messages (content, ip, userid) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $_POST["message"], $_SERVER['REMOTE_ADDR'], $userid);
$stmt->execute();

// Get sent message data
$msg = $sql->query("SELECT messages.content, messages.date, messages.id, users.username 
    FROM messages LEFT JOIN users ON messages.userid = users.id 
    WHERE messages.id = LAST_INSERT_ID()")->fetch_assoc();//get_result();
echo json_encode([$msg]);

$stmt->close();
$sql->close();
?>