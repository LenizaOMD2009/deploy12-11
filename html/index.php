<?php

use app\database\builder\SelectQuery;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

SelectQuery::select('id, nome_fantasia, cpf_cnpj')
    ->from('cliente')
    ->where('cliente.cpf', 'ilike', '123', 'and')
    ->where('cliente.id', '=', 1)
    ->order('nome', 'desc')
    ->limit(10)
    ->fetch();

die;
$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../app/helper/settings.php';
require __DIR__ . '/../app/route/route.php';

$app->run();
