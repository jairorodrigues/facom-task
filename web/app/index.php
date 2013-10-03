<?php

date_default_timezone_set("America/Campo_Grande");

include 'config/config.php';

include 'lib/lighttp.php';
include 'lib/rb.php';

include 'module/module.php';

get('/usuario/:login', function() {

	$login = param('login');

	$usuario = R::findOne('usuario', 'login=?', array($login));

	if ($usuario == NULL) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		die();
	}

	setHttpResponseStatus(HttpStatus::OK);
	setHttpResponseContentType(HttpContentType::APPLICATION_JSON);

	echo json_encode(array(
		'id' => $usuario->id,
		'nome' => $usuario->nome,
		'email' => $usuario->email
	));
});

post('/usuarios', function() {

});

put('/usuario/:login', function() {
	
});

delete('/usuario/:login', function() {
	
});

get('/usuario/:login/tarefas', function () {

});

post('/usuario/:login/tarefas', function () {
	
});

get('/usuario/:login/tarefa/:tarefa_id', function () {
	
});

put('/usuario/:login/tarefa/:tarefa_id', function () {
	
});

delete('/usuario/:login/tarefa/:tarefa_id', function () {
	
});

run();