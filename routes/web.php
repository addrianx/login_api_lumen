<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// 🔐 AUTH (contoh)
$router->post('/api/login', 'AuthController@login');
$router->post('/api/auth/refresh', 'AuthController@refresh');

$router->group(['middleware' => 'auth'], function () use ($router) {
    // 📦 PRODUK
    $router->get('/api/produk', 'ProdukController@index');
    $router->get('/api/all_produk', 'ProdukController@all');
    $router->delete('/api/produk/bulk-delete', 'ProdukController@bulkDelete');
    $router->post('/api/produk', 'ProdukController@store');
    $router->get('/api/produk/{id}', 'ProdukController@show');
    $router->put('/api/produk/{id}', 'ProdukController@update');
    $router->delete('/api/produk/{id}', 'ProdukController@destroy');


    // 📁 KATEGORI PRODUK
    $router->get('/api/kategori-produk/all', 'KategoriProdukController@all');
    $router->get('/api/kategori-produk', 'KategoriProdukController@index');
    $router->post('/api/kategori-produk', 'KategoriProdukController@store');
    $router->get('/api/kategori-produk/{id}', 'KategoriProdukController@show');
    $router->put('/api/kategori-produk/{id}', 'KategoriProdukController@update');
    $router->delete('/api/kategori-produk/{id}', 'KategoriProdukController@destroy');


    // 📏 SATUAN PRODUK
    $router->get('/api/satuan-produk', 'SatuanProdukController@index');
    $router->get('/api/satuan-produk/{id}', 'SatuanProdukController@show');
    $router->post('/api/satuan-produk', 'SatuanProdukController@store');
    $router->put('/api/satuan-produk/{id}', 'SatuanProdukController@update');
    $router->delete('/api/satuan-produk/{id}', 'SatuanProdukController@destroy');

    // 🧮 STOK PRODUK (Histori masuk/keluar)
    $router->get('/api/stok-produk', 'StokProdukController@index');
    $router->get('/api/stok-produk/{id}', 'StokProdukController@show');
    $router->post('/api/stok-produk', 'StokProdukController@store'); // tambah histori stok
    $router->put('/api/stok-produk/{id}', 'StokProdukController@update');
    $router->delete('/api/stok-produk/{id}', 'StokProdukController@destroy');

    // User 
     $router->post('/api/verify-password', 'AuthController@verifyPassword');
});