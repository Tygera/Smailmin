<?php

namespace Smailmin\Core;

class Request {
    
    private string $request_path = "";
    private string $request_method = "";
    private array  $server = [];
    private array  $get = [];
    private array  $post = [];

    private static array $allowed_methods = ['GET', 'POST', 'HEAD'];
    
    public function __construct(array $server = [], array $get = [], array $post = []) {

        $this->server = $server ?: $_SERVER;
        $this->get    = $get    ?: $_GET;
        $this->post   = $post   ?: $_POST;
        
        $this->request_path   = $this->normalizeUri($_SERVER['REQUEST_URI']);
        $this->request_method = $this->normalizeMethod($_SERVER['REQUEST_METHOD']);

    }

    public static function normalizeUri(?string $request_uri = null): string {
        $uri = $request_uri ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = '/' . ltrim($uri, '/');
        $uri = preg_replace('#/{2,}#', '/', $uri);
        $uri = rtrim($uri, '/');

        if(empty($uri)) {
            $uri = "/";
        }
        return $uri;
    }

    public static function normalizeMethod(?string $request_method = null): string {
        $allowed_methods = self::$allowed_methods;
        $method = $request_method ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if(!in_array($method, $allowed_methods)) {
            http_response_code(405);
        }

        if(empty($method)) {
            $method = "GET";
        }

        return $method;
    }

    public function getRequestParameters(?string $request_uri = null): array {

        $raw   = $request_uri ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $path  = self::normalizeUri($raw);
        $query = parse_url($raw, PHP_URL_QUERY) ?? '';

        $get = [];
        if ($query !== '') {
            parse_str($query, $get);
        }

        return [$path, $get];
    }

    public function getMethod(): string {
        return $this->request_method;
    }

    public function getPath(): string {
        return $this->request_path;
    }

    public function getParams(): string|array {
        return $this->get;
    }

}
