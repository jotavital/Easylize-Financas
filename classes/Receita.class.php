<?php

include_once(__DIR__ . "/../connections/Connection.class.php");
include_once(__DIR__ . "/../connections/loginVerify.php");
include_once(__DIR__ . "/../classes/Conta.class.php");
include_once(__DIR__ . "/../classes/Categoria_Receita.class.php");

class Receita
{

    function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    function insertReceita()
    {
        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare("INSERT INTO receita (descricao_receita, data_receita, valor, data_inclusao, fk_usuario, fk_conta) VALUES (:descricao_receita, :data_receita, :valor, :data_inclusao, :fk_usuario, :fk_conta)");
        $stm->bindValue(':descricao_receita', $_POST['descReceitaInput']);
        $stm->bindValue(':data_receita', $_POST['dataReceita']);
        $stm->bindValue(':valor', $_POST['valorInput']);
        $stm->bindValue(':data_inclusao', date('Y' . '-' . 'm' . '-' . 'd'));
        $stm->bindValue(':fk_usuario', $_SESSION['userId']);
        $stm->bindValue(':fk_conta', $_POST['contaSelect']);

        try {
            $stm->execute();

            $receitaId = $conexao->lastInsertId();

            $categoriaReceitaObj = new Categoria_Receita;

            foreach ($_POST['categoriasSelect'] as $categoria) {
                $categoriaReceitaObj->relacionarCategoriaReceita($categoria, $receitaId);
            }

            $conta = new Conta;
            $conta->somarValorReceita($_POST['contaSelect'], $_POST['valorInput']);
        } catch (PDOException $th) {
            echo $th->errorInfo;
        }

        $conn->desconectar();
    }

    function insertReceitaReajuste($valor, $fk_conta)
    {
        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare("INSERT INTO receita (descricao_receita, data_receita, valor, data_inclusao, fk_usuario, fk_conta) VALUES (:descricao_receita, :data_receita, :valor, :data_inclusao, :fk_usuario, :fk_conta)");
        $stm->bindValue(':descricao_receita', "Reajuste de saldo");
        $stm->bindValue(':data_receita', date('Y' . '-' . 'm' . '-' . 'd'));
        $stm->bindValue(':valor', $valor);
        $stm->bindValue(':data_inclusao', date('Y' . '-' . 'm' . '-' . 'd'));
        $stm->bindValue(':fk_usuario', $_SESSION['userId']);
        $stm->bindValue(':fk_conta', $fk_conta);

        try {
            $stm->execute();

            $receitaId = $conexao->lastInsertId();

            $categoriaReceitaObj = new Categoria_Receita;
            $categoriaReceitaObj->relacionarCategoriaReceita(19, $receitaId);

            $conta = new Conta;
            $conta->somarValorReceita($fk_conta, $valor);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $conn->desconectar();
    }

    function insertCategoriaReceita()
    {

        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare("INSERT INTO categoria (nome_categoria, fk_tipo, fk_usuario) VALUES (:nome_categoria, :fk_tipo, :fk_usuario)");
        $stm->bindValue(":nome_categoria", $_POST['nomeCategoriaInput']);
        $stm->bindValue(":fk_tipo", 4);
        $stm->bindValue(":fk_usuario", $_SESSION['userId']);

        try {
            $stm->execute();

            $_SESSION['msg'] = "Categoria cadastrada com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['msg'] = "Erro ao cadastrar categoria!";
            echo $e->getMessage();
        }

        $conn->desconectar();
    }

    function desvincularTodasCategoriasReceita($idReceita)
    {
        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare("DELETE FROM categoria_receita WHERE fk_receita = :idReceita");
        $stm->bindValue("idReceita", $idReceita);

        try {
            $stm->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $conn->desconectar();
    }

    function deletarReceita($idReceita)
    {
        $conn = new Connection;
        $conexao = $conn->conectar();
        $deleteReceita = new Receita;

        $deleteReceita->desvincularTodasCategoriasReceita($idReceita);

        $stm2 = $conexao->prepare("DELETE FROM receita WHERE id = :idReceita");
        $stm2->bindValue("idReceita", $idReceita);

        try {
            $stm2->execute();

            $conta = new Conta;
            $conta->subtrairValorReceita($_POST['idConta'], $_POST['valorReceita']);

            header('Location: ../../pages/receitas.php');
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $conn->desconectar();
    }

    function deletarTodasReceitasConta($idConta)
    {
        $conn = new Connection;
        $conexao = $conn->conectar();
        $deleteReceita = new Receita;

        $stmSelectReceitasConta = $conexao->prepare("SELECT id as receita_id FROM receita where fk_conta = :idConta");
        $stmSelectReceitasConta->bindValue("idConta", $idConta);

        try {
            $stmSelectReceitasConta->execute();

            $resultReceitasConta = $stmSelectReceitasConta->fetchAll();

            foreach ($resultReceitasConta as $receita) {
                $deleteReceita->desvincularTodasCategoriasReceita($receita['receita_id']);
            }

            $stmReceita = $conexao->prepare("DELETE FROM receita WHERE fk_conta = :idConta");
            $stmReceita->bindValue("idConta", $idConta);

            try {
                $stmReceita->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }



        $conn->desconectar();
    }

    function selectValorTotalReceitasByMonth($mes)
    {
        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare("SELECT SUM(valor) FROM receita WHERE month(receita.data_receita) = :mes AND receita.fk_usuario = :userId");
        $stm->bindValue(":mes", $mes);
        $stm->bindValue(":userId", $_SESSION['userId']);

        try {
            $stm->execute();
            $result = $stm->fetch();

            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }

        $conn->desconectar();
    }

    function selectValorTotalReceitasTodosDiasByMonth($mes)
    {
        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare(
            "SELECT sum(valor) as valor, 
            DAY(data_receita) as dia,
            data_receita
            FROM receita 
            WHERE month(receita.data_receita) = :mes
            AND receita.fk_usuario = :userId 
            AND receita.data_receita > DATE_SUB(data_receita,INTERVAL DAYOFMONTH(data_receita)-1 DAY) 
            AND receita.data_receita < LAST_DAY(data_receita)
            GROUP BY dia"
        );

        $stm->bindValue(":mes", $mes);
        $stm->bindValue(":userId", $_SESSION['userId']);

        try {
            $stm->execute();
            $result = $stm->fetchAll();

            $array = array();


            for ($i = 0; $i < 31; $i++) {
                array_push($array, 0);
            }

            foreach ($result as $row) {
                $array[$row["dia"]] = $row["valor"];
            }

            return $array;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }

        $conn->desconectar();
    }

    function deletarTodasReceitasUsuario()
    {
        $conta = new Conta;
        $receita = new Receita;

        $todasContasUsuario = $conta->selectTodasContasUsuario();

        foreach ($todasContasUsuario as $idContaUsuario) {
            $receita->deletarTodasReceitasConta($idContaUsuario['id']);
        }
    }

    function selectFromReceita($campos = '', $condicao = '')
    {
        $conn = new Connection;
        $conexao = $conn->conectar();

        if ($campos == '') {
            $campos = '*';
        }

        if ($condicao != '') {
            $sql = "SELECT " . $campos .  " FROM receita WHERE " . $condicao . "";
        } else {
            $sql = "SELECT " . $campos .  " FROM receita ";
        }

        $stm = $conexao->prepare($sql);

        try {
            $stm->execute();

            $result = $stm->fetchAll();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function updateReceita($idReceita)
    {
        $categoriaReceitaObj = new Categoria_Receita;
        $receitaObj = new Receita;
        $conta = new Conta;

        $conn = new Connection;
        $conexao = $conn->conectar();

        $stm = $conexao->prepare("UPDATE receita SET descricao_receita = :descricao_receita, data_receita = :data_receita, valor = :valor, data_inclusao = :data_inclusao, fk_conta = :fk_conta WHERE id = :idReceita");
        $stm->bindValue(':descricao_receita', $_POST['descReceitaInput']);
        $stm->bindValue(':data_receita', $_POST['dataReceita']);
        $stm->bindValue(':valor', $_POST['valorInput']);
        $stm->bindValue(':data_inclusao', date('Y' . '-' . 'm' . '-' . 'd'));
        $stm->bindValue(':fk_conta', $_POST['contaSelect']);
        $stm->bindValue(':idReceita', $idReceita);

        $dadosReceita = $receitaObj->selectFromReceita('valor, fk_conta', 'id = ' . $idReceita);

        try {
            $stm->execute();

            $categoriaReceitaObj->desvincularTodasCategoriasReceita($idReceita);

            foreach ($_POST['categoriasSelect'] as $categoria) {
                $categoriaReceitaObj->relacionarCategoriaReceita($categoria, $idReceita);
            }

            $conta->subtrairValorReceita($dadosReceita[0]['fk_conta'], $dadosReceita[0]['valor']);
            $conta->somarValorReceita($_POST['contaSelect'], $_POST['valorInput']);
        } catch (PDOException $th) {
            echo $th->errorInfo;
        }

        $conn->desconectar();
    }
}

if (isset($_POST['deleteReceita'])) {
    $receita = new Receita;
    $receita->deletarReceita($_POST['idReceita']);
}

if (isset($_POST['editReceita'])) {
    $receita = new Receita;
    $receita->updateReceita($_POST['idReceita']);
}

if (isset($_POST['insertReceita'])) {
    $receita = new Receita;
    $receita->insertReceita();
}

if (isset($_POST['insertCategoriaReceita'])) {
    $receita = new Receita;
    $receita->insertCategoriaReceita();
}
