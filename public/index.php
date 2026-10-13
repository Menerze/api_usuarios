<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/usuarios.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

/* STATUS */
$app->get('/status', function ($req, $res) {
    $res->getBody()->write(json_encode(["status" => "ok"]));
    return $res->withHeader('Content-Type', 'application/json');
});

/* LISTAR */
$app->get('/usuarios', function ($req, $res) use (&$usuarios) {
    $res->getBody()->write(json_encode($usuarios));
    return $res->withHeader('Content-Type', 'application/json');
});

/* BUSCAR POR ID */
$app->get('/usuarios/{id}', function ($req, $res, $args) use (&$usuarios) {

    foreach ($usuarios as $u) {
        if ($u['id'] == $args['id']) {
            $res->getBody()->write(json_encode($u));
            return $res->withHeader('Content-Type', 'application/json');
        }
    }

    return $res->withStatus(404);
});

/* CADASTRAR */
$app->post('/usuarios', function ($req, $res) use (&$usuarios) {

    $dados = $req->getParsedBody();

    $usuarios[] = [
        "id" => count($usuarios) + 1,
        "nome" => $dados['nome'],
        "email" => $dados['email'],
        "login" => $dados['login'],
        "senha" => $dados['senha']
    ];

    $res->getBody()->write(json_encode(["mensagem" => "Usuário criado"]));

    return $res->withHeader('Content-Type', 'application/json');
});

/* ATUALIZAR */
$app->put('/usuarios/{id}', function ($req, $res, $args) use (&$usuarios) {

    $dados = $req->getParsedBody();

    foreach ($usuarios as &$u) {
        if ($u['id'] == $args['id']) {
            $u['nome'] = $dados['nome'];
            $u['email'] = $dados['email'];
            $u['login'] = $dados['login'];
        }
    }

    $res->getBody()->write(json_encode(["mensagem" => "Usuário atualizado"]));

    return $res->withHeader('Content-Type', 'application/json');
});

/* EXCLUIR */
$app->delete('/usuarios/{id}', function ($req, $res, $args) use (&$usuarios) {

    foreach ($usuarios as $i => $u) {

        if ($u['id'] == $args['id']) {
            unset($usuarios[$i]);
        }
    }

    $res->getBody()->write(json_encode(["mensagem" => "Usuário removido"]));

    return $res->withHeader('Content-Type', 'application/json');
});

/* LOGIN */
$app->post('/login', function ($req, $res) use (&$usuarios) {

    $dados = $req->getParsedBody();

    foreach ($usuarios as $u) {

        if (
            $u['login'] == $dados['login'] &&
            $u['senha'] == $dados['senha']
        ) {

            $res->getBody()->write(json_encode([
                "mensagem" => "Login realizado"
            ]));

            return $res->withHeader('Content-Type', 'application/json');
        }
    }

    return $res->withStatus(401);
});

/* ALTERAR SENHA */
$app->put('/usuarios/{id}/senha', function ($req, $res, $args) use (&$usuarios) {

    $dados = $req->getParsedBody();

    foreach ($usuarios as &$u) {

        if ($u['id'] == $args['id']) {

            if ($u['senha'] == $dados['senhaAtual']) {

                $u['senha'] = $dados['novaSenha'];

                $res->getBody()->write(json_encode([
                    "mensagem" => "Senha alterada"
                ]));

                return $res->withHeader('Content-Type', 'application/json');
            }
        }
    }

    return $res->withStatus(401);
});

$app->run();