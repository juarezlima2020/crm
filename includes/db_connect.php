<?php
$host = "auth-db1612.hstgr.io";
$username = "u449585788_crm2";
$password = "Ideal@Brasil.2022";
$dbname = "u449585788_crm2";

// Cria a conexão
$conn = new mysqli($host, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>