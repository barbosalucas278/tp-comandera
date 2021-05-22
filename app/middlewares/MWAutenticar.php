<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
use Firebase\JWT\JWT;

class MWAutenticar
{
    public function VerificarUsuario(Request $request, RequestHandler $handler)
    {
        try {
            if (empty($request->getHeaderLine('Authorization'))) {
                throw new Exception("Falta token de autorización");
            }
            var_dump($request->getHeaderLine('Authorization'));
            //el token viaja en el header como un string "Bearer ...token"
            $header = $request->getHeaderLine('Authorization');
            //primero le hacemos un explode para transformar ese string en un array, y ponemos como delimitador "Bearer"
            //luego un trim para volver a unir ese array en un string propiamente dicho, sin la palabra "Bearer", solamente el token
            $token = trim(explode("Bearer", $header)[1]);
            self::VerificarToken($token);
            $response = $handler->handle($request);
            return  $response;
        } catch (Exception $ex) {
            throw new Exception("Ocurrio un problema " . $ex->getMessage(), 0, $ex);
        }
    }
    private static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token se encuentra vacio");
        }
        try {
            $tokenDecodificado = JWT::decode($token, $_ENV['SECRET_KEY'], [$_ENV['TIPO_ENCRYP']]);
        } catch (Exception $ex) {
            throw $ex;
        }
        if ($tokenDecodificado->aud !== UsuarioController::Aud()) {
            throw new Exception("No es un usuario válido ");
        }
    }
}
