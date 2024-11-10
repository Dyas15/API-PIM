<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$tipos = ['clientes', 'colaboradores', 'logins', 'familias', 'pagamentos', 'estoque', 'fornecedores'];

// foreach ($tipos as $tipo) {
//     $controller = ucfirst($tipo) . 'Controller';

//     $router->group(['prefix' => "/api/$tipo"], function () use ($router, $controller) {
//         $router->get('/{cpf:[0-9]+}', "$controller@Selecionar");
//         $router->get('/', "$controller@Selecionar");
//         $router->post('/', "$controller@Criar");
//         $router->put('/{cpf}', "$controller@Atualizar");
//         $router->delete('/{cpf}', "$controller@Deletar");
//     });
// }


$router->group(['prefix' => '/api/colaboradores'], function () use ($router) {
    $router->get('/{cpf:[0-9]+}', 'ColaboradoresController@Selecionar');
    $router->get('/', 'ColaboradoresController@Selecionar');
    $router->post('/', 'ColaboradoresController@Criar');
    $router->put('/{cpf}', 'ColaboradoresController@Atualizar');
    $router->delete('/{cpf}', 'ColaboradoresController@Deletar');
});

$router->group(['prefix' => '/api/logins'], function () use ($router) {
    $router->get('/{tipo}/{cpf}', 'LoginsController@Selecionar');
    $router->get('/{tipo}', 'LoginsController@Selecionar');
    $router->post('/{tipo}', 'LoginsController@Criar');
    $router->put('/{tipo}/{cpf}', 'LoginsController@Atualizar');
    $router->delete('/{tipo}/{cpf}', 'LoginsController@Desativar');
});

$router->group(['prefix' => '/api/familias'], function () use ($router) {
    $router->get('/{nome}', 'FamiliasController@Selecionar');
    $router->get('/', 'FamiliasController@Selecionar');
    $router->post('/', 'FamiliasController@Criar');
    $router->put('/{valor}', 'FamiliasController@Atualizar');
    $router->delete('/{valor}', 'FamiliasController@Deletar');
});

$router->group(['prefix' => '/api/pagamentos'], function () use ($router) {
    $router->get('/{nome}', 'PagamentosController@Selecionar');
    $router->get('/', 'PagamentosController@Selecionar');
    $router->post('/', 'PagamentosController@Criar');
    $router->put('/{valor}', 'PagamentosController@Atualizar');
    $router->delete('/{valor}', 'PagamentosController@Deletar');
});

$router->group(['prefix' => '/api/estoque'], function () use ($router) {
    $router->get('/{nome}', 'EstoqueController@Selecionar');
    $router->get('/', 'EstoqueController@Selecionar');
    $router->post('/', 'EstoqueController@Criar');
    $router->put('/{valor}', 'EstoqueController@Atualizar');
    $router->delete('/{valor}', 'EstoqueController@Deletar');
});

$router->group(['prefix' => '/api/fornecedores'], function () use ($router) {
    $router->get('/{cnpj}', 'FornecedoresController@Selecionar');
    $router->get('/', 'FornecedoresController@Selecionar');
    $router->post('/', 'FornecedoresController@Criar');
    $router->put('/{cnpj}', 'FornecedoresController@Atualizar');
    $router->delete('/{cnpj}', 'FornecedoresController@Deletar');
});

$router->get('/teste', 'TesteController@index');
