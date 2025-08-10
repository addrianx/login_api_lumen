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
    $router->post('/api/satuan-produk', 'SatuanProdukController@store');
    $router->get('/api/satuan-produk/{id}', 'SatuanProdukController@show');
    $router->put('/api/satuan-produk/{id}', 'SatuanProdukController@update');
    $router->delete('/api/satuan-produk/{id}', 'SatuanProdukController@destroy');

    
    // 🧮 STOK PRODUK (Histori masuk/keluar)
    $router->get('/api/stok-produk', 'StokProdukController@index');
    $router->get('/api/stok-produk-all', 'StokProdukController@paginate');
    $router->post('/api/stok-produk', 'StokProdukController@store'); // tambah histori stok
    $router->get('/api/stok-produk/{id}', 'StokProdukController@show');
    $router->put('/api/stok-produk/{id}', 'StokProdukController@update');
    $router->delete('/api/stok-produk/{id}', 'StokProdukController@destroy');


    // 🧮 STOK USER (Histori masuk/keluar)
    $router->get('/api/customers', 'CustomerController@index');
    $router->post('/api/customer', 'CustomerController@store');
    $router->delete('/api/customers/bulk-delete', 'CustomerController@bulkDelete');
    $router->get('/api/customer/{id}', 'CustomerController@show');
    $router->put('/api/customers/{id}', 'CustomerController@update');
    $router->delete('/api/customers/{id}', 'CustomerController@destroy');

    // 🧮 Data Transaksi dan Riwayat (Histori masuk/keluar)
    $router->get('/api/transaksi/all', 'TransactionController@index');
    $router->post('/api/transaksi', 'TransactionController@store');
    $router->get('/api/transaksi/{id}', 'TransactionController@show');
    $router->delete('/api/transaksi/{id}', 'TransactionController@destroy');

    // Tambah riwayat pembelian
    $router->post('{id}/purchase', 'CustomerController@addPurchase');

    // User 
     $router->post('/api/verify-password', 'AuthController@verifyPassword');
});