<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/evaluacionDocente.php';
require __DIR__.'/auditoria.php';
require __DIR__.'/impacto.php';
require __DIR__.'/satisfaccion.php';

Route::get('/', function () {
    return view('landing');
});

