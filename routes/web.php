<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => '/api/clientes'], function () use ($router) {
    $router->get('/{cpf:[0-9]+}', 'ClientesController@Selecionar');
    $router->get('/', 'ClientesController@Selecionar');
    $router->post('/', 'ClientesController@Criar');
    $router->put('/{cpf}', 'ClientesController@Atualizar');
    $router->delete('/{cpf}', 'ClientesController@Deletar');
});

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

$router->group(['prefix' => '/api/familia'], function () use ($router) {
    $router->get('/{nome}', 'FamiliaController@Selecionar');
    $router->get('/', 'FamiliaController@Selecionar');
    $router->post('/', 'FamiliaController@Criar');
    $router->put('/{valor}', 'FamiliaController@Atualizar');
    $router->delete('/{valor}', 'FamiliaController@Deletar');
});

$router->get('/teste', 'TesteController@index');
