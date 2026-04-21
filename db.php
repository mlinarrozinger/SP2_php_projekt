<?php
$host = "localhost";
$user = "root";       // ali uporabniško ime na hostingu
$pass = "";           // geslo (na hostingu NI prazno)
$dbname = "php-trgovina";

$conn = new mysqli($host, $user, $pass, $dbname);

// Preveri povezavo
if ($conn->connect_error) {
    die("Povezava ni uspela: " . $conn->connect_error);
}

// Nastavi UTF-8
$conn->set_charset("utf8mb4");
?>
