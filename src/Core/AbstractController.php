<?php

namespace App\Core;

use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractController
{
    public function __construct(
        // Inyectado automáticamente por el Container (dependency injection)
        protected Engine $templates,
    ) {
    }

    public function render(
        string $templateName,
        array $data = [],
        int $statusCode = 200,
        array $headers = [],
    ): ResponseInterface {
        $body = $this->templates->render($templateName, $data);

        return new Response($statusCode, $headers, $body);
    }

    public function json(
        mixed $content,
        int $statusCode = 200,
        array $headers = [],
        bool $rawContent = false,
        bool $pretty = true,
    ) {
        $headers = ['Content-Type' => 'application/json; charset=utf-8'] + $headers;

        // Para estandarizar el formato de la respuesta
        if (!$rawContent) {
            $content = [
                'statusCode' => $statusCode,
                //'meta' => [], // Paginación o filtros por ejemplo
                'data' => $content,
            ];
        }

        $body = json_encode($content, $pretty ? JSON_PRETTY_PRINT : 0);

        return new Response($statusCode, $headers, $body);
    }

    public function redirect(
        string $destination,
        int $statusCode = 302,
        array $headers = [],
    ) {
        $headers = ['Location' => $destination] + $headers;

        $body = "En breve tu navegador te va a redirigir <a href=\"{$destination}\">aquí</a>.";

        return new Response($statusCode, $headers, $body);
    }
}
