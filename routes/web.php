<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => '/api/clientes'], function () use ($router) {
    $router->get('/{cpf:[0-9]+}', 'ClientesController@Selecionar');
    $router->get('/', 'ClientesController@Selecionar');
    $router->post('/', 'ClientesController@Criar');
    $router->put('/{cpf}', 'ClientesController@Atualizar');
    // $router->delete('/{cpf}', 'ClientesController@Desativar');
    $router->delete('/{cpf}', 'ClientesController@Deletar');
});

$router->group(['prefix' => '/api/logins'], function () use ($router) {
    $router->get('/{tipo}/{cpf}', 'LoginsController@Selecionar');
    $router->get('/{tipo}', 'LoginsController@Selecionar');
    $router->post('/{tipo}', 'LoginsController@Criar');
    $router->put('/{tipo}/{cpf}', 'LoginsController@Atualizar');
    $router->delete('/{tipo}/{cpf}', 'LoginsController@Desativar');
    // $router->get('');
});

// Rota de teste
$router->get('/teste', 'TesteController@index');
