<?php
session_start();
include('../includes/db_connect.php');

// Verifica se o formulário foi enviado
if (isset($_POST['login']) && isset($_POST['senha'])) {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // Prepara a consulta para evitar SQL Injection
    $sql = "SELECT * FROM usuarios WHERE login = ?";

    // Prepara a declaração
    if ($stmt = $conn->prepare($sql)) {
        // Liga os parâmetros da consulta
        $stmt->bind_param("s", $login);

        // Executa a consulta
        $stmt->execute();

        // Obtém o resultado
        $result = $stmt->get_result();

        // Verifica se o login foi encontrado
        if ($result->num_rows > 0) {
            // Busca os dados do usuário
            $user = $result->fetch_assoc();

            // Verifica a senha
            if (password_verify($senha, $user['senha'])) {
                // A senha está correta, cria a sessão
                $_SESSION['user'] = $login;

                // Redireciona para a página de bancos
                header("Location: ../bancos/index.php");
                exit();
            } else {
                // Senha inválida
                echo "Senha inválida.";
            }
        } else {
            // Usuário não encontrado
            echo "Usuário não encontrado.";
        }

        // Fecha a declaração
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CRM</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<link rel="stylesheet" href="../assets/style.css">
    <div class="login-container">
        <form method="POST">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
<br>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>