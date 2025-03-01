<?php
// Exemplo de cadastro de usuário
include('../includes/db_connect.php');

$login = 'SISTEMA';
$senha = '123456';
$nome_completo = 'SISTEMA';

$senha_criptografada = password_hash($senha, PASSWORD_DEFAULT); // Criptografa a senha

$sql = "INSERT INTO usuarios (login, senha, nome_completo) VALUES (?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $login, $senha_criptografada, $nome_completo);
    $stmt->execute();
    echo "Usuário cadastrado com sucesso!";
    $stmt->close();
}
?>