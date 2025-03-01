<?php
session_start();
include('../includes/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../login/index.php");
    exit();
}

$nome = "";
if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];
    $sql = "INSERT INTO banco (nome) VALUES ('$nome')";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM banco WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nome = $row['nome'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar/Editar Banco</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar/Editar Banco</h1>
        <form method="POST">
            <label for="nome">Nome do Banco:</label>
            <input type="text" id="nome" name="nome" value="<?php echo $nome; ?>" required>
            <button type="submit" name="salvar">Salvar</button>
            <button type="button" onclick="window.location.href='index.php'">Cancelar</button>
        </form>
    </div>
</body>
</html>