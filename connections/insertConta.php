<?php
    include("../connections/loginVerify.php");
    include('connection.php');

    $userId = $_SESSION['userId'];
    
    $sql = $conn->prepare("INSERT INTO conta(nome_conta, saldo_atual, fk_usuario, fk_categoria) VALUES (:nome, :saldo, :userId, :categoria)");
    $sql->bindValue(":nome", $_POST['nomeConta']);
    $sql->bindValue(":saldo", $_POST['saldoConta']);
    $sql->bindValue(":userId", $userId);
    $sql->bindValue(":categoria", $_POST['categoriaSelect']);
    
    if($sql->execute()){
        $_SESSION['msg'] = "Conta adicionada!";
    }else{
        $_SESSION['msg'] = "Erro " . $sql->errorInfo();
    }

    $conn = null;
?>