<?php
session_start();
include('../includes/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../login/index.php");
    exit();
}

if (isset($_POST['pesquisar'])) {
    $pesquisa = $_POST['pesquisa'];
    $sql = "SELECT * FROM banco WHERE nome LIKE '%$pesquisa%'";
} else {
    $sql = "SELECT * FROM banco";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Bancos</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Lista de Bancos</h1>

        <!-- Botões de ação -->
        <form method="POST">
            <input type="text" name="pesquisa" placeholder="Pesquisar banco..." />
            <button type="submit" name="pesquisar">Pesquisar</button>
        </form>

        <table>
            <tr>
                <th>Nome</th>
                <th>Editar</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['nome']; ?></td>
                    <td><a href="editar.php?id=<?php echo $row['id']; ?>">Editar</a></td>
                </tr>
            <?php } ?>
        </table>
        
        <a href="adicionar.php">Adicionar Novo Banco</a>
    </div>
</body>
</html>