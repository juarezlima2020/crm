<?php
session_start();
include('../includes/db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: ../login/index.php");
    exit();
}

// Inicializa a variável para pesquisa
$pesquisa = '';

// Verifica se foi feita uma pesquisa
if (isset($_POST['pesquisar'])) {
    $pesquisa = $_POST['pesquisa'];
    // Consulta com filtro de pesquisa
    $sql = "SELECT BANCO.*, PRODUTOS.DESCRICAO AS PRODUTO_DESCRICAO FROM BANCO 
            LEFT JOIN PRODUTOS ON BANCO.ID_PRODUTO = PRODUTOS.ID_PRODUTO 
            WHERE PRODUTOS.DESCRICAO LIKE ?";
    $stmt = $conn->prepare($sql);
    $pesquisa = "%$pesquisa%"; // Adiciona os '%' para o LIKE no SQL
    $stmt->bind_param("s", $pesquisa);
} else {
    // Consulta sem filtro de pesquisa
    $sql = "SELECT BANCO.*, PRODUTOS.DESCRICAO AS PRODUTO_DESCRICAO FROM BANCO 
            LEFT JOIN PRODUTOS ON BANCO.ID_PRODUTO = PRODUTOS.ID_PRODUTO";
    $stmt = $conn->prepare($sql);
}

// Executa a consulta
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se a consulta retornou algum resultado
    if ($result->num_rows > 0) {
        $bancos = $result->fetch_all(MYSQLI_ASSOC); // Obtém todos os resultados em um array associativo
    } else {
        $bancos = [];
    }
    $stmt->close(); // Fecha a declaração
} else {
    // Se a consulta falhou, exibe uma mensagem de erro
    echo "Erro na consulta SQL: " . $conn->error;
    $bancos = [];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM-IDEAL BRASIL</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Estilo da tabela */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left; /* Alinha o conteúdo à esquerda */
        }

        table th {
            background-color: #f4f4f4;
            text-align: center; /* Alinha os cabeçalhos ao centro */
        }

        table td {
            text-align: center; /* Alinha os dados ao centro */
        }

        /* Alinha os títulos e dados nas colunas de forma mais precisa */
        .col-id-banco, .col-produto, .col-id-empresa, .col-validade, .col-tipo-licenca, .col-ultima-verificacao, .col-descricao {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Bancos</h1>

        <!-- Formulário de pesquisa -->
        <form method="POST">
            <input type="text" name="pesquisa" placeholder="Pesquisar banco..." value="<?php echo htmlspecialchars($pesquisa); ?>" />
            <button type="submit" name="pesquisar">Pesquisar</button>
        </form>

        <!-- Exibe a tabela com os bancos -->
        <?php if (count($bancos) > 0) { ?>
            <table>
                <tr>
                    <th class="col-id-banco">ID_BANCO</th>
                    <th class="col-produto">PRODUTO</th> <!-- Altere o cabeçalho para "Produto" -->
                    <th class="col-id-empresa">ID_EMPRESA</th>
                    <th class="col-validade">VALIDADE</th>
                    <th class="col-tipo-licenca">TIPO_LICENCA</th>
                    <th class="col-ultima-verificacao">ULTIMA_VERIFICACAO</th>
                    <th class="col-descricao">DESCRICAO</th>
                    <th>Editar</th>
                </tr>
                <?php foreach ($bancos as $banco) { ?>
                    <tr>
                        <td class="col-id-banco"><?php echo $banco['ID_BANCO']; ?></td>
                        <td class="col-produto"><?php echo $banco['PRODUTO_DESCRICAO']; ?></td> <!-- Exibe a descrição do produto -->
                        <td class="col-id-empresa"><?php echo $banco['ID_EMPRESA']; ?></td>
                        <td class="col-validade"><?php echo $banco['VALIDADE']; ?></td>

                        <!-- Exibe "Vitalício" ou "Período" dependendo do valor de TIPO_LICENCA -->
                        <td class="col-tipo-licenca">
                            <?php 
                                echo ($banco['TIPO_LICENCA'] == 0) ? 'Vitalício' : 'Período'; 
                            ?>
                        </td>

                        <td class="col-ultima-verificacao"><?php echo $banco['ULTIMA_VERIFICACAO']; ?></td>
                        <td class="col-descricao"><?php echo $banco['DESCRICAO']; ?></td>
                        <td><a href="editar.php?id=<?php echo $banco['ID_BANCO']; ?>">Editar</a></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>Nenhum banco encontrado.</p>
        <?php } ?>

        <a href="adicionar.php">Adicionar Novo Banco</a>
    </div>
</body>
</html>