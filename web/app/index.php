<?php

date_default_timezone_set("America/Campo_Grande");

include 'config/config.php';

include 'lib/lighttp.php';

include 'lib/rb.php';

include 'module/module.php';

set_exception_handler(function($ex) {

	$classeDaException = get_class($ex);

	setHttpResponseContentType(HttpContentType::APPLICATION_JSON);

	if ($classeDaException == 'InvalidArgumentException') {
		setHttpResponseStatus(HttpStatus::BAD_REQUEST);
		die(json_encode(array(
			"mensagem" => $ex->getMessage()
		)));
	} else {
		setHttpResponseStatus(HttpStatus::INTERNAL_SERVER_ERROR);
		die(json_encode(array(
			"mensagem" => "Ocorreu um erro no servidor.",
			"mensagemTecnica" => $ex->getMessage()
		)));
	}
});

get('/usuario/:identificador', function() {

	$identificador = param('identificador');

	$usuario = buscar_usuario_por_identificador($identificador);

	if ($usuario == NULL) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Usuário \"{$identificador}\" não encontrado!"
		)));
	}

	setHttpResponseStatus(HttpStatus::OK);

	exibir_json_usuario($usuario);
});

post('/usuarios', function() {

	$identificador = param('login');

	$usuario = buscar_usuario_por_identificador($identificador);

	if ($usuario != NULL) {
		setHttpResponseStatus(HttpStatus::BAD_REQUEST);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "O identificador \"{$identificador}\" já sendo utilizado por outro usuário!"
		)));
	}

	$usuario = R::dispense('usuario');

	$usuario->login = param('login');
	$usuario->senha = param('senha');
	$usuario->nome = param('nome');
	$usuario->email = param('email');

	$id = R::store($usuario);

	setHttpResponseStatus(HttpStatus::CREATED);
	
	exibir_json_usuario($usuario);
});

put('/usuario/:identificador', function() {
	
	$identificador = param('identificador');

	$usuario = buscar_usuario_por_identificador($identificador);

	if ($usuario == NULL) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Usuário \"{$identificador}\" não encontrado!"
		)));
	}

	$usuario->senha = param('senha');
	$usuario->nome = param('nome');
	$usuario->email = param('email');

	R::store($usuario);

	setHttpResponseStatus(HttpStatus::OK);
	exibir_json_usuario($usuario);
});

delete('/usuario/:identificador', function() {
	
	$identificador = param('identificador');

	$usuario = buscar_usuario_por_identificador($identificador);

	if ($usuario == NULL) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Usuário \"{$identificador}\" não encontrado!"
		)));
	}

	R::trash($usuario);

	setHttpResponseStatus(HttpStatus::OK);
});

post('/usuario/:identificador_usuario/tarefas', function () {
	
	$identificador = param('identificador_usuario');

	$usuario = buscar_usuario_por_identificador($identificador);

	if ($usuario == NULL) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Usuário \"{$identificador}\" não encontrado!"
		)));
	}

	$tarefa= R::dispense('tarefa');

	$tarefa->nome = param('nome');
	$tarefa->prioridade = param('prioridade');
	$tarefa->feita = param('feita');
	$tarefa->usuario_id = $usuario->id;

	R::store($tarefa);

	setHttpResponseStatus(HttpStatus::CREATED);
	
	exibir_json_tarefa($tarefa);
});

get('/usuario/:identificador_usuario/tarefas', function () {

	$identificador = param('identificador_usuario');

	$usuario = buscar_usuario_por_identificador($identificador);

	if ($usuario == NULL) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Usuário \"{$identificador}\" não encontrado!"
		)));
	}

	$tarefas = buscar_tarefas_do_usuario($usuario);

	setHttpResponseStatus(HttpStatus::OK);
	setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
	echo json_encode($tarefas);
});

get('/usuario/:login/tarefa/:tarefa_id', function () {
	
	$id = param('tarefa_id');

	$tarefaRequisitada = R::load('tarefa', $id);

	if ($tarefaRequisitada->id == 0) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Tarefa \"{$id}\" não foi encontrada!"
		)));
	}

	setHttpResponseStatus(HttpStatus::OK);
	exibir_json_tarefa($tarefaRequisitada);
});

put('/usuario/:login/tarefa/:tarefa_id', function () {
	
	$id = param('tarefa_id');

	$tarefaRequisitada = R::load('tarefa', $id);

	if ($tarefaRequisitada->id == 0) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Tarefa \"{$id}\" não foi encontrada!"
		)));
	}

	$tarefaRequisitada->nome = param('nome');
	$tarefaRequisitada->prioridade = param('prioridade');
	$tarefaRequisitada->feita = param('feita');

	R::store($tarefaRequisitada);

	setHttpResponseStatus(HttpStatus::OK);
	exibir_json_tarefa($tarefaRequisitada);
});

delete('/usuario/:login/tarefa/:tarefa_id', function () {

	$id = param('tarefa_id');

	$tarefaRequisitada = R::load('tarefa', $id);

	if ($tarefaRequisitada->id == 0) {
		setHttpResponseStatus(HttpStatus::NOT_FOUND);
		setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
		die(json_encode(array(
			"mensagem" => "Tarefa \"{$id}\" não foi encontrada!"
		)));
	}

	R::trash($tarefaRequisitada);

	setHttpResponseStatus(HttpStatus::OK);
});

run();