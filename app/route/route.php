<?php

use app\controller\User;
use app\controller\Home;
use app\controller\cliente;
use app\controller\Empresas;
use app\controller\Fornecedor;

use Slim\Routing\RouteCollectorProxy;

$app->get('/', Home::class . ':home');

$app->get('/home', Home::class . ':home');

$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', User::class . ':lista');
    $group->get('/cadastro', User::class . ':cadastro');
    $group->post('/listuser', User::class . ':listuser');
    $group->post('/insert', User::class . ':insert');
});
$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/lista', cliente::class . ':lista');
    $group->get('/cadastro', cliente::class . ':cadastro');
    $group->post('/listcliente', cliente::class . ':listcliente');
    $group->post('/insert', cliente::class . ':insert');
});
$app->group('/empresas', function (RouteCollectorProxy $group) {
    $group->get('/lista', Empresas::class . ':lista');
    $group->get('/cadastro', Empresas::class . ':cadastro');
    $group->post('/listempresas', Empresas::class . ':listempresas');
    $group->post('/insert', Empresas::class . ':insert');
});
$app->group('/fornecedor', function (RouteCollectorProxy $group) {
    $group->get('/lista', Fornecedor::class . ':lista');
    $group->get('/cadastro', Fornecedor::class . ':cadastro');
    $group->post('/listfornecedor', Fornecedor::class . ':listfornecedor');
    $group->post('/insert', Fornecedor::class . ':insert');
});
