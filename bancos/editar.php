<?php
session_start();
include('../includes/db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: ../login/index.php");
    exit();
}

// Verifica se o ID_BANCO foi passado pela URL
if (!isset($_GET['id'])) {
    echo "ID do banco não especificado!";
    exit();
}

$id_banco = $_GET['id'];

// Inicializa as variáveis
$id_produto = '';
$id_empresa = '';
$validade = '';
$tipo_licenca = '';
$ultima_verificacao = '';
$descricao = '';

// Consulta os dados do banco com o ID_BANCO especificado
$sql_banco = "SELECT * FROM BANCO WHERE ID_BANCO = ?";
$stmt_banco = $conn->prepare($sql_banco);
$stmt_banco->bind_param("i", $id_banco);
$stmt_banco->execute();
$result_banco = $stmt_banco->get_result();

if ($result_banco->num_rows > 0) {
    $row_banco = $result_banco->fetch_assoc();
    // Preenche as variáveis com os dados do banco
    $id_produto = $row_banco['ID_PRODUTO'];
    $id_empresa = $row_banco['ID_EMPRESA'];
    $validade = $row_banco['VALIDADE'];
    $tipo_licenca = $row_banco['TIPO_LICENCA'];
    $ultima_verificacao = $row_banco['ULTIMA_VERIFICACAO'];
    $descricao = $row_banco['DESCRICAO'];
} else {
    echo "Banco não encontrado!";
    exit();
}

// Consulta os produtos para o campo ID_PRODUTO
$sql_produtos = "SELECT ID_PRODUTO, DESCRICAO FROM PRODUTOS";
$result_produtos = $conn->query($sql_produtos);

// Consulta as empresas para o campo ID_EMPRESA
$sql_empresas = "SELECT ID_PESSOA, NOME_FANTASIA FROM PESSOA";
$result_empresas = $conn->query($sql_empresas);

// Verifica se o formulário foi enviado para atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $id_produto = $_POST['id_produto'];
    $id_empresa = $_POST['id_empresa'];
    $validade = $_POST['validade'];
    $tipo_licenca = $_POST['tipo_licenca'];
    $ultima_verificacao = $_POST['ultima_verificacao'];
    $descricao = $_POST['descricao'];

    // Valida a data de validade (adapte para o seu SGBD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $validade)) {
        echo "Formato de data inválido. Use YYYY-MM-DD.";
        exit();
    }

    // Formata a data para o formato 'YYYY-MM-DD' (se necessário)
     $validade_formatada = date('Y-m-d', strtotime($validade));

    // Atualiza os dados no banco de dados
    $sql_atualizar = "UPDATE BANCO SET ID_PRODUTO = ?, ID_EMPRESA = ?, VALIDADE = ?, TIPO_LICENCA = ?, ULTIMA_VERIFICACAO = ?, DESCRICAO = ? WHERE ID_BANCO = ?";
    $stmt_atualizar = $conn->prepare($sql_atualizar);
    $stmt_atualizar->bind_param("iiisssi", $id_produto, $id_empresa, $validade, $tipo_licenca, $ultima_verificacao, $descricao, $id_banco);

    if ($stmt_atualizar->execute()) {
        echo "Banco atualizado com sucesso!";
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao atualizar banco: " . $conn->error;
    }

    $stmt_atualizar->close();
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM-IDEAL BRASIL</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Banco</h1>
        
        <form method="POST">
            <!-- ID_BANCO -->
            <label for="id_banco">ID Banco:</label>
            <input type="text" id="id_banco" name="id_banco" value="<?php echo $id_banco; ?>" readonly />

            <!-- ID_PRODUTO -->
            <label for="id_produto">Produto:</label>
            <select id="id_produto" name="id_produto" required>
                <option value="">Selecione um Produto</option>
                <?php while ($row_produto = $result_produtos->fetch_assoc()) { ?>
                    <option value="<?php echo $row_produto['ID_PRODUTO']; ?>" <?php echo ($row_produto['ID_PRODUTO'] == $id_produto) ? 'selected' : ''; ?>><?php echo $row_produto['DESCRICAO']; ?></option>
                <?php } ?>
            </select>
            <!-- ID_EMPRESA -->
            <label for="id_empresa">Empresa:</label>
            <select id="id_empresa" name="id_empresa" required>
                <option value="">Selecione uma Empresa</option>
                <?php while ($row_empresa = $result_empresas->fetch_assoc()) { ?>
                    <option value="<?php echo $row_empresa['ID_PESSOA']; ?>" <?php echo ($row_empresa['ID_PESSOA'] == $id_empresa) ? 'selected' : ''; ?>><?php echo $row_empresa['NOME_FANTASIA']; ?></option>
                <?php } ?>
            </select>
            <!-- VALIDADE -->
            <label for="validade">Validade:</label>
			    <input type="date" id="validade" name="validade" value="<?php echo $validade; ?>" required />
            <!--<input type="date" id="validade" name="validade" value="<?php echo $validade; ?>" required />-->
            <!-- TIPO_LICENCA -->
            <label for="tipo_licenca">Tipo de Licença:</label>
            <select id="tipo_licenca" name="tipo_licenca" required>
                <option value="0" <?php echo ($tipo_licenca == 0) ? 'selected' : ''; ?>>Vitalicia</option>
                <option value="1" <?php echo ($tipo_licenca == 1) ? 'selected' : ''; ?>>Periodo</option>
            </select>
            <!-- ULTIMA_VERIFICACAO -->
            <label for="ultima_verificacao">Última Verificação:</label>
            <input type="datetime" id="ultima_verificacao" name="ultima_verificacao" value="<?php echo $ultima_verificacao; ?>" />
            <!-- DESCRICAO -->
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="2" required><?php echo $descricao; ?></textarea>
            <div class="form-buttons">
                <button type="submit">Salvar</button>
	<br>
	<br>
                <a href="index.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>
</body>
</html>