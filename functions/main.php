<?php
session_start();
require_once "functions/Messages.php";
require_once "functions/OldInput.php";

$action = $_POST['action'] ?? null; // 'sendMail'
if (!empty($action)) {
    $action(); // sendMail()
}

function redirect($url)
{
    header("Location: $url");
    exit;
}


function sendMail()
{
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($message)) {
        Messages::setMessage("All fields are required", 'danger');
        OldInput::set($_POST);
        redirect("/contacts");
    }

    Messages::setMessage("Message sent successfully");
    redirect("/contacts");
}


function uploadImage()
{
    //echo "<pre>" . print_r($_FILES, true) . "</pre>";
    extract($_FILES['image']);

    if ($error) {
        Messages::setMessage("File upload error", 'danger');
        redirect("/gallery");
    }

    $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'png', 'jpeg', 'gif', 'webp', 'aviff'];

    if (!in_array($fileExt, $allowedExt)) {
        Messages::setMessage("Invalid file type", 'danger');
        redirect("/gallery");
    }

    if ($size > 2 * 1024 * 1024) {
        Messages::setMessage("File size must be less than 2MB", 'danger');
        redirect("/gallery");
    }

    if (getimagesize($tmp_name) === false) {
        Messages::setMessage("Invalid file type", 'danger');
        redirect("/gallery");
    }

    $fileName = uniqid("img_") . "." . $fileExt;

    if (!file_exists("uploads"))
        mkdir("uploads", 0755, true);

    if (move_uploaded_file($tmp_name, "uploads/$fileName")) {
        Messages::setMessage("File uploaded successfully", 'success');
        resizeImage("uploads/$fileName", 300);
        redirect("/gallery");
    }

    Messages::setMessage("File upload error", 'danger');
    redirect("/gallery");
}


function resizeImage($path, $size)
{
    $src = imagecreatefromstring(file_get_contents($path));

    list($src_width, $src_height) = getimagesize($path);

    $dest_width = $size;
    $dest_height = $size * $src_height / $src_width;

    $dest = imagecreatetruecolor($dest_width, $dest_height);

    imagecopyresampled($dest, $src, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);


    $fileExt = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    extract(pathinfo($path));
    $pathToSave = $dirname . "/medium/" . $basename;

    if (!file_exists($dirname . "/medium"))
        mkdir($dirname . "/medium", 0755, true);

    if ($fileExt === 'jpg' || $fileExt === 'jpeg')
        imagejpeg($dest, $pathToSave, 100);
    else {
        $functionSave = "image$fileExt";
        $functionSave($dest, $pathToSave);
    }
}





function sendReview()
{
    $name = $_POST['name'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($message)) {
        Messages::setMessage("All fields are required", 'danger');
        OldInput::set($_POST);
        redirect("/reviews");
    }

    $time = time(); 

    $reviews = json_decode(file_get_contents("reviews.json"), true);
    $reviews[] = compact('name', 'message', 'time');

    $f = fopen("reviews.json", "w");
    fwrite($f, json_encode($reviews));
    fclose($f);

    Messages::setMessage("Review sent successfully");
    redirect("/reviews");
}
