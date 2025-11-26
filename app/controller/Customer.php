<?php

namespace app\controller;

use app\database\builder\InsertQuery;

use app\database\builder\SelectQuery;

class Customer extends Base
{

    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Lista de cliente'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listcustomer'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de cliente'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('customer'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function listcustomer($request, $response)
    {
        try {
            #Captura todas a variaveis de forma mais segura VARIAVEIS POST.
            $form = $request->getParsedBody();
            
            #Qual a coluna da tabela deve ser ordenada.
            $order = $form['order'][0]['column'] ?? 0;
            #Tipo de ordenação
            $orderType = $form['order'][0]['dir'] ?? 'asc';
            #Em qual registro se inicia o retorno dos registro, OFFSET
            $start = $form['start'] ?? 0;
            #Limite de registro a serem retornados do banco de dados LIMIT
            $length = $form['length'] ?? 10;
            
            $fields = [
                0 => 'id',
                1 => 'nome',
                2 => 'sobrenome',
                3 => 'cpf',
                4 => 'rg',
                5 => 'data_nascimento_abertura'
            ];
            
            #Capturamos o nome do campo a ser ordenado.
            $orderField = $fields[$order] ?? 'id';
            #O termo pesquisado
            $term = $form['search']['value'] ?? '';
            
            $query = SelectQuery::select('id,nome,sobrenome,cpf,rg,data_nascimento_abertura')->from('cliente');
            
            if (!is_null($term) && ($term !== '')) {
                $query->where('cliente.nome', 'ilike', "%{$term}%", 'or')
                    ->where('cliente.sobrenome', 'ilike', "%{$term}%", 'or')
                    ->where('cliente.cpf', 'ilike', "%{$term}%", 'or')
                    ->where('cliente.rg', 'ilike', "%{$term}%", 'or')
                    ->whereRaw("to_char(cliente.data_nascimento_abertura, 'YYYY-MM-DD') ILIKE '%{$term}%'");


            }

            $customer = $query
                ->order($orderField, $orderType)
                ->limit($length, $start)
                ->fetchAll();
            
            $userData = [];
            foreach ($customer as $key => $value) {
                $userData[$key] = [
                    $value['id'],
                    $value['nome'],
                    $value['sobrenome'],
                    $value['cpf'],
                    $value['rg'],
                    $value['data_nascimento_abertura'],
                    "<button class='btn btn-warning'>Editar</button>
                    <button class='btn btn-danger'>Excluir</button>"
                ];
            }
            
            $data = [
                'draw' => $form['draw'] ?? 1,
                'recordsTotal' => count($customer),
                'recordsFiltered' => count($customer),
                'data' => $userData
            ];
            
            $payload = json_encode($data);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Throwable $th) {
            $data = [
                'draw' => $form['draw'] ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $th->getMessage()
            ];
            $response->getBody()->write(json_encode($data));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }
    public function insert($request, $response)
    {
        try {
            $nome = $_POST['nome'];
            $sobrenome = $_POST['sobrenome'];
            $cpf = $_POST['cpf'];
            $rg = $_POST['rg'];
            $data_nascimento_abertura = $_POST['data_nascimento_abertura'];
            $FieldsAndValues = [
                'nome' => $nome,
                'sobrenome' => $sobrenome,
                'cpf' => $cpf,
                'rg' => $rg,
                'data_nascimento_abertura' => $data_nascimento_abertura
            ];
            $IsSave = InsertQuery::table('cliente')->save($FieldsAndValues);

            if (!$IsSave) {
                echo 'Erro ao salvar';
                die;
            }
            echo "Salvo com sucesso!";
            die;
        } catch (\Throwable $th) {
            echo "Erro: " . $th->getMessage();
            die;
        }
    }
}
