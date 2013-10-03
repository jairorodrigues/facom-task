<?php

function buscar_usuario_por_identificador($identificador) {
	return R::findOne('usuario', 'id=? or login=?', array($identificador, $identificador));
}

function buscar_tarefas_do_usuario($usuario) {
	return R::getAll(
		'select
			id, nome, prioridade
		from
			tarefa
		where
			usuario_id=?
		order by
			prioridade desc',
		array($usuario->id)
	);
}

function exibir_json_usuario($usuario) {
	setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
	echo json_encode(array(
		'id' => $usuario->id,
		'login' => $usuario->login,
		'nome' => $usuario->nome,
		'email' => $usuario->email
	));
}

function exibir_json_tarefa($tarefa) {
	setHttpResponseContentType(HttpContentType::APPLICATION_JSON);
	echo json_encode(array(
		'id' => $tarefa->id,
		'nome' => $tarefa->nome,
		'prioridade' => $tarefa->prioridade,
		'feita' => $tarefa->feita
	));
}

function esta_em_sha1($string) {
	return preg_match('/^[a-fA-F0-9]{40}$/', $string);
}

class Model_Usuario extends RedBean_SimpleModel {

	public function update() {

		$usuario = $this->bean;

		$usuario->nome = trim($usuario->nome);

		if (preg_match('/^[a-zA-Zà-úÀ-Ú ]+$/', $usuario->nome) != 1) {
			throw new InvalidArgumentException("Nome do usuário \"{$usuario->nome}\" inválido!");
		}

		$usuario->login = trim($usuario->login);

		if (preg_match("/^[a-zA-Z0-9]{4,16}$/", $usuario->login) != 1) {
			throw new InvalidArgumentException("Login do usuário \"{$usuario->login}\" inválido!");
		}

		if (!esta_em_sha1($usuario->senha)) {
			$usuario->senha = sha1($usuario->senha);
		}
	}

} 

class Model_Tarefa extends RedBean_SimpleModel {

	public function update() {

		$tarefa = $this->bean;

		$tarefa->nome = trim($tarefa->nome);

		if (preg_match('/^[a-zA-Zà-úÀ-Ú0-9 ]+$/', $tarefa->nome) != 1) {
			throw new InvalidArgumentException("Nome da tarefa \"{$tarefa->nome}\" inválido!");
		}

		$tarefa->prioridade = trim($tarefa->prioridade);

		if (!is_numeric($tarefa->prioridade) || ($tarefa->prioridade > 5 || $tarefa->prioridade < 1)) {
			throw new InvalidArgumentException("Prioridade da tarefa \"{$tarefa->prioridade}\" inválida!");
		}

		if (!is_numeric($tarefa->usuario_id)) {
			throw new InvalidArgumentException("Id do Usuário dona da tarefa \"{$tarefa->prioridade}\" inválido!");
		}

		$tarefa->feita = trim($tarefa->feita);

		if (preg_match('/^true|false$/', $tarefa->feita) != 1) {
			throw new InvalidArgumentException("Status da tarefa inválido!");
		}
	}

} 