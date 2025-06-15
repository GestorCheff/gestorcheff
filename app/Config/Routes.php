<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


/*     Bloco Home      */
/* ------------------- */

// Página Home 
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
/* ------------------- */

// Rotas públicas (sem filtro)
$routes->get('usuarios/login', 'Usuarios::login');
$routes->post('usuarios/logar', 'Usuarios::logar');
$routes->get('usuarios/cadastro', 'Usuarios::cadastro');
$routes->post('usuarios/cadastrar', 'Usuarios::cadastrar');


$routes->get('usuarios/logout', 'Usuarios::logout');
$routes->get('usuarios/painel-usuario', 'Usuarios::painelUsuario');
$routes->get('usuarios/informacao', 'Usuarios::informacao');
$routes->get('usuarios/editar/(:num)', 'Usuarios::editar/$1');
$routes->post('usuarios/atualizar/(:num)', 'Usuarios::atualizar/$1');
$routes->get('usuarios/painelUsuario', 'Usuarios::painelUsuario');


// ROTAS PÚBLICAS - sem filtro
$routes->get('restaurantes/cadastro', 'Restaurantes::cadastro');
$routes->post('restaurantes/cadastrar', 'Restaurantes::cadastrar');
$routes->get('restaurantes/login','Restaurantes::login');
$routes->post('restaurantes/logar', 'Restaurantes::logar');


// ROTAS PRIVADAS - com filtro restauranteAuth
$routes->group('restaurantes', ['filter' => 'restauranteAuth'], function($routes) {
    $routes->get('painel/(:num)', 'Restaurantes::painel/$1');
    $routes->get('editar/(:num)', 'Restaurantes::editar/$1');
    $routes->post('atualizar/(:num)', 'Restaurantes::atualizar/$1');
});




/*   Bloco Cardápio    */
/* ------------------- */

// Página Principal de Cardapio
$routes->get('cardapio/(:num)', 'Cardapio::index/$1');

// Página de Cadastro 
$routes->get('cardapio/novo/(:num)', 'Cardapio::novo/$1');

// Cadastrar novo Cardapio
$routes->post('cardapio/salvar/(:num)', 'Cardapio::salvar/$1');

// Página de Listagem do Cardápio
$routes->get('cardapiousuario/cardapio', 'CardapioUsuario::cardapio');

// Página de Listagem do Cardápio do Restaurante
$routes->get('cardapio/painel/(:num)', 'Cardapio::painel/$1');

// Página de Edição do Cardápio
$routes->get('cardapio/editar/(:num)', 'Cardapio::editar/$1');

// Atualizar Cardápio
$routes->post('cardapio/atualizar/(:num)', 'Cardapio::atualizar/$1');

// Página de Listagem do Cardápio
$routes->get('cardapio/listar/(:num)', 'Cardapio::listar/$1');
/* ------------------- */

 
/*   Bloco Endereço    */
/* ------------------- */

$routes->get('api/enderecos/usuario/(:num)', 'Api\Enderecos::usuario/$1');


// Página de Cadastro do Endereço
$routes->post('endereco/salvar', 'Endereco::salvar');

// Página de Exclusao do Endereço
$routes->post('endereco/excluir/(:num)', 'Endereco::excluir/$1');

// Salvar Endereço do Usuário
$routes->get('/endereco/perfil', 'Endereco::perfil');

// Página de Edição do Endereço
$routes->post('endereco/atualizar/(:num)', 'Endereco::atualizar/$1');
/* ------------------- */


/* Bloco Pedidos */
/* ------------------- */

// Página de Pedidos do Restaurante
$routes->get('pedidos/(:num)', 'Pedidos::index/$1');

// Salvar Pedido
$routes->get('pedidos/rastrear', 'Pedidos::rastrear'); 

$routes->post('pedidos/salvar', 'Pedidos::salvar');

$routes->post('pedidos/confirmar/(:num)', 'Pedidos::confirmar/$1');

$routes->post('pedidos/cancelar/(:num)', 'Pedidos::cancelar/$1');

$routes->post('pedidos/preparando/(:num)', 'Pedidos::iniciarPreparo/$1');

$routes->post('pedidos/enviar/(:num)', 'Pedidos::enviar/$1');

$routes->post('pedidos/enviado/(:num)', 'Pedidos::enviarParaEntrega/$1');

$routes->post('pedidos/finalizado/(:num)', 'Pedidos::finalizarPedido/$1');

$routes->post('pedidos/confirmar-entrega/(:num)', 'Pedidos::confirmarEntrega/$1');
/* ------------------- */


/*   Bloco Relátorios  */
/* ------------------- */

// Página de Relatórios
$routes->get('relatorios', 'Relatorios::index');

// Sincronizar Relatórios (para uso administrativo)
$routes->get('relatorios/sincronizar', 'Relatorios::sincronizar');


// Rotas de Pedidos (adicionar no seu arquivo app/Config/Routes.php)

$routes->group('pedidos', function($routes) {
    // Rastreamento de pedidos pelo usuário
    $routes->get('rastrear', 'Pedidos::rastrear', ['as' => 'usuario.pedidos']);
    
    // Detalhes de um pedido específico
    $routes->get('detalhes/(:num)', 'Pedidos::detalhes/$1', ['as' => 'usuario.pedidos.detalhes']);
    
    // Cancelamento de pedido (via AJAX)
    $routes->post('cancelar', 'Pedidos::cancelar', ['as' => 'usuario.pedidos.cancelar']);
    
    // Salvar novo pedido (usado no checkout)
    $routes->post('salvar', 'Pedidos::salvar', ['as' => 'usuario.pedidos.salvar']);
});

// Rotas para o restaurante ver seus pedidos (se necessário)
$routes->group('restaurante', function($routes) {
    $routes->get('pedidos/(:num)', 'Pedidos::index/$1', ['as' => 'restaurante.pedidos']);
});