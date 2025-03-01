<?php
session_start();
include('../includes/db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: ../login/index.php");
    exit();
}

// Inicializa as variáveis
$id_banco = 0;
$id_produto = '';
$id_empresa = '';
$validade = '';
$tipo_licenca = 0;
$ultima_verificacao = null;
$descricao = '';

// Consulta o maior ID_BANCO para somar 1 e gerar o novo ID
$sql_id_banco = "SELECT MAX(ID_BANCO) AS max_id FROM BANCO";
$result_id_banco = $conn->query($sql_id_banco);
if ($result_id_banco) {
    $row = $result_id_banco->fetch_assoc();
    $id_banco = $row['max_id'] + 1; // Incrementa o ID_BANCO
}

// Consulta os produtos para o campo ID_PRODUTO
$sql_produtos = "SELECT ID_PRODUTO, DESCRICAO FROM PRODUTOS";
$result_produtos = $conn->query($sql_produtos);

// Consulta as empresas para o campo ID_EMPRESA
$sql_empresas = "SELECT ID_PESSOA, NOME_FANTASIA FROM PESSOA";
$result_empresas = $conn->query($sql_empresas);

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $id_produto = $_POST['id_produto'];
    $id_empresa = $_POST['id_empresa'];
    $validade = $_POST['validade'];
    $tipo_licenca = $_POST['tipo_licenca'];
    $ultima_verificacao = $_POST['ultima_verificacao'];
    $descricao = $_POST['descricao'];

    // Insere o novo banco na tabela BANCO
    $sql_inserir = "INSERT INTO BANCO (ID_BANCO, ID_PRODUTO, ID_EMPRESA, VALIDADE, TIPO_LICENCA, ULTIMA_VERIFICACAO, DESCRICAO) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_inserir);
    $stmt->bind_param("iiissss", $id_banco, $id_produto, $id_empresa, $validade, $tipo_licenca, $ultima_verificacao, $descricao);
    
    if ($stmt->execute()) {
        echo "Banco adicionado com sucesso!";
        header("Location: index.php"); // Redireciona para a lista de bancos
        exit();
    } else {
        echo "Erro ao adicionar banco: " . $conn->error;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Banco</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Banco</h1>
        
        <form method="POST">
            <!-- ID_BANCO -->
            <label for="id_banco">ID Banco:</label>
            <input type="text" id="id_banco" name="id_banco" value="<?php echo $id_banco; ?>" readonly />

            <!-- ID_PRODUTO -->
            <label for="id_produto">Produto:</label>
            <select id="id_produto" name="id_produto" required>
                <option value="">Selecione um Produto</option>
                <?php while ($row_produto = $result_produtos->fetch_assoc()) { ?>
                    <option value="<?php echo $row_produto['ID_PRODUTO']; ?>"><?php echo $row_produto['DESCRICAO']; ?></option>
                <?php } ?>
            </select>
<br>
<br>
            <!-- ID_EMPRESA -->
            <label for="id_empresa">Empresa:</label>
            <select id="id_empresa" name="id_empresa" required>
                <option value="">Selecione uma Empresa</option>
                <?php while ($row_empresa = $result_empresas->fetch_assoc()) { ?>
                    <option value="<?php echo $row_empresa['ID_PESSOA']; ?>"><?php echo $row_empresa['NOME_FANTASIA']; ?></option>
                <?php } ?>
            </select>
<br>
<br>
            <!-- VALIDADE -->
            <label for="validade">Validade:</label>
            <input type="date" id="validade" name="validade" value="<?php echo date('Y-m-d'); ?>" required />
<br>
<br>
            <!-- TIPO_LICENCA -->
            <label for="tipo_licenca">Tipo de Licença:</label>
            <select id="tipo_licenca" name="tipo_licenca" required>
                <option value="0">Inativo</option>
                <option value="1">Ativo</option>
            </select>
<br>
<br>
            <!-- ULTIMA_VERIFICACAO -->
            <label for="ultima_verificacao">Última Verificação:</label>
            <input type="date" id="ultima_verificacao" name="ultima_verificacao" />
<br>
<br>
            <!-- DESCRICAO -->
            <label for="descricao">Descrição:</label>
			<br>
            <textarea id="descricao" name="descricao" rows="2" required></textarea>
<br>
<br>
            <div class="form-buttons">
                <button type="submit">Salvar</button>
                <a href="index.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>
</body>
</html>