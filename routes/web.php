<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => '/api/usuarios'], function() use ($router) {
    
    $router->get('/{cpf:[0-9]+}', 'UsuariosController@Selecionar');
    $router->get('/', 'UsuariosController@Selecionar');
    $router->post('/', 'UsuariosController@Criar');
    $router->put('/{cpf}', 'UsuariosController@Atualizar');
    $router->delete('/{cpf}', 'UsuariosController@Deletar');
});

// Rota de teste
$router->get('/teste', 'TesteController@index');
