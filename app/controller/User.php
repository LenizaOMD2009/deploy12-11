<?php

namespace app\controller;

//use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;

class User extends Base
{

    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Lista de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listuser'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('user'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);

    }
    public function listuser($request, $response)
    {
        #Captura todas a variaveis de forma mais segura VARIAVEIS POST.
        $form = $request->getParsedBody();
        #Qual a coluna da tabela deve ser ordenada.
        $order = $form['order'][0]['column'];
        #Tipo de ordenação
        $orderType = $form['order'][0]['dir'];
        #Em qual registro se inicia o retorno dos registro, OFFSET
        $start = $form['start'];
        #Limite de registro a serem retornados do banco de dados LIMIT
        $length = $form['length'];
        #O termo pesquisado
        $term = $form['search']['value'];


        //$query = new InsertQuery();
        $query = SelectQuery::select('id,nome,sobrenome')->from('usuario');

        if (!is_null($term) && ($term !== '')) {
            $query->where('nome', 'like', "$%{term}%", 'or')
                  ->Where('sobrenome', 'like',"$%{term}%" );
        }
        $users = $query->fetchAll();
        $userData = [];
        foreach ($users as $key => $value) {
            $userData[$key] = [
                $value['id'],
                $value['nome'],
                $value['sobrenome'], 
                "<button class='btn btn-danger'>Excluir</button>
            <button class='btn btn-primary'>Editar</button>"
            ];
        }
        $data =[
            'status' => true,
            'recordsTotal' => 2,
            'recordsFiltered' => 2,
            'data' => [[
                1,
                'João',
                'Silva',
                "<button class='btn btn-danger'>Excluir</button>
                <button class='btn btn-primary'>Editar</button>"
            ]]
        ];
        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);  
    }
}
