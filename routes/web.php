<?php

use App\Http\Controllers\StatisticController;
use App\Http\Controllers\AdresController;
use App\Http\Controllers\AmunicjaController;
use App\Http\Controllers\KategoriaController;
use App\Http\Controllers\KlientController;
use App\Http\Controllers\PracownikController;
use App\Http\Controllers\ProduktController;
use App\Http\Controllers\TransakcjaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StatisticController::class, 'index'])->name('home');

Route::get('/adres', [AdresController::class, 'read'])->name('adres.index');
Route::get('/adres/filter', [AdresController::class, 'readByMiasto'])->name('adres.filter');
Route::get('/adres/create', [AdresController::class, 'createForm'])->name('adres.create');
Route::get('/adres/{id}/edit', [AdresController::class, 'editForm'])->name('adres.edit');
Route::post('/adres', [AdresController::class, 'create'])->name('adres.store');
Route::put('/adres/{id}', [AdresController::class, 'update'])->name('adres.update');
Route::delete('/adres/{id}', [AdresController::class, 'delete'])->name('adres.delete');

Route::get('/amunicja', [AmunicjaController::class, 'read'])->name('amunicja.index');
Route::get('/amunicja/filter', [AmunicjaController::class, 'readByNazwa'])->name('amunicja.filter');
Route::get('/amunicja/create', [AmunicjaController::class, 'createForm'])->name('amunicja.create');
Route::get('/amunicja/{id}/edit', [AmunicjaController::class, 'editForm'])->name('amunicja.edit');
Route::post('/amunicja', [AmunicjaController::class, 'create'])->name('amunicja.store');
Route::put('/amunicja/{id}', [AmunicjaController::class, 'update'])->name('amunicja.update');
Route::delete('/amunicja/{id}', [AmunicjaController::class, 'delete'])->name('amunicja.delete');


Route::get('/kategorie', [KategoriaController::class, 'read'])->name('kategoria.index');
Route::get('/kategorie/filter', [KategoriaController::class, 'readByUprawnienia'])->name('kategoria.filter.uprawnienia');
Route::get('/kategorie/create', [KategoriaController::class, 'createForm'])->name('kategoria.create');
Route::get('/kategorie/{id}/edit', [KategoriaController::class, 'editForm'])->name('kategoria.edit');
Route::post('/kategorie', [KategoriaController::class, 'create'])->name('kategoria.store');
Route::put('/kategorie/{id}', [KategoriaController::class, 'update'])->name('kategoria.update');
Route::delete('/kategorie/{id}', [KategoriaController::class, 'delete'])->name('kategoria.delete');

Route::get('/klienci', [KlientController::class, 'read'])->name('klient.index');
Route::get('/klienci/active', [KlientController::class, 'readActive'])->name('klient.active');
Route::get('/klienci/create', [KlientController::class, 'createForm'])->name('klient.create');
Route::get('/klienci/{id}/edit', [KlientController::class, 'editForm'])->name('klient.edit');
Route::post('/klienci', [KlientController::class, 'create'])->name('klient.store');
Route::put('/klienci/{id}', [KlientController::class, 'update'])->name('klient.update');
Route::delete('/klienci/{id}', [KlientController::class, 'delete'])->name('klient.delete');
Route::get('/klient/{id}', [KlientController::class, 'show'])->name('klient.show');

Route::get('/pracownicy', [PracownikController::class, 'read'])->name('pracownik.index');
Route::get('/pracownicy/active', [PracownikController::class, 'readActive'])->name('pracownik.active');
Route::get('/pracownicy/create', [PracownikController::class, 'createForm'])->name('pracownik.create');
Route::get('/pracownicy/{id}/edit', [PracownikController::class, 'editForm'])->name('pracownik.edit');
Route::post('/pracownicy', [PracownikController::class, 'create'])->name('pracownik.store');
Route::put('/pracownicy/{id}', [PracownikController::class, 'update'])->name('pracownik.update');
Route::delete('/pracownicy/{id}', [PracownikController::class, 'delete'])->name('pracownik.delete');

Route::get('/produkty', [ProduktController::class, 'read'])->name('produkt.index');
Route::get('/produkty/active', [ProduktController::class, 'readActive'])->name('produkt.active');
Route::get('/produkty/create', [ProduktController::class, 'createForm'])->name('produkt.create');
Route::get('/produkty/{id}/edit', [ProduktController::class, 'editForm'])->name('produkt.edit');
Route::post('/produkty', [ProduktController::class, 'create'])->name('produkt.store');
Route::put('/produkty/{id}', [ProduktController::class, 'update'])->name('produkt.update');
Route::delete('/produkty/{id}', [ProduktController::class, 'delete'])->name('produkt.delete');

Route::get('/transakcje', [TransakcjaController::class, 'read'])->name('transakcja.index');
Route::get('/transakcje/filter', [TransakcjaController::class, 'readByData'])->name('transakcja.filter');
Route::get('/transakcje/create', [TransakcjaController::class, 'createForm'])->name('transakcja.create');
Route::get('/transakcje/{id}', [TransakcjaController::class, 'readById'])->name('transakcja.show');
Route::post('/transakcje', [TransakcjaController::class, 'create'])->name('transakcja.store');
Route::delete('/transakcje/{id}', [TransakcjaController::class, 'delete'])->name('transakcja.delete');
Route::get('/transakcje/{id}/edit', [TransakcjaController::class, 'editForm'])->name('transakcja.edit');
Route::put('/transakcje/{id}', [TransakcjaController::class, 'update'])->name('transakcja.update');

