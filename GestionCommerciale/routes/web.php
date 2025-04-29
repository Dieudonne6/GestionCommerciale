<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceptionCmdAchatController;
use App\Http\Controllers\CommandeAchatController;

// Routes pour la gestion des réceptions
Route::get('/receptions', [ReceptionCmdAchatController::class, 'index'])->name('receptions.index');
Route::get('/receptions/create', [ReceptionCmdAchatController::class, 'create'])->name('receptions.create');
Route::post('/receptions', [ReceptionCmdAchatController::class, 'store'])->name('receptions.store');
Route::get('/receptions/{id}/edit', [ReceptionCmdAchatController::class, 'edit'])->name('receptions.edit');
Route::put('/receptions/{id}', [ReceptionCmdAchatController::class, 'update'])->name('receptions.update');
Route::delete('/receptions/{id}', [ReceptionCmdAchatController::class, 'destroy'])->name('receptions.destroy');

// Routes pour la gestion des commandes d'achat
Route::get('/commandes', [CommandeAchatController::class, 'index'])->name('commandes.index');
Route::get('/commandes/create', [CommandeAchatController::class, 'create'])->name('commandes.create');
Route::post('/commandes', [CommandeAchatController::class, 'store'])->name('commandes.store');
Route::get('/commandes/{id}/edit', [CommandeAchatController::class, 'edit'])->name('commandes.edit');
Route::put('/commandes/{id}', [CommandeAchatController::class, 'update'])->name('commandes.update');
Route::delete('/commandes/{id}', [CommandeAchatController::class, 'destroy'])->name('commandes.destroy');