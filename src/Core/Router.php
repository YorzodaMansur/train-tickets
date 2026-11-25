<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string, pattern:string, regex:string, handler:callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable $handler): void
    {
        $quoted = preg_quote($pattern, '~');
        $regex = preg_replace_callback(
            '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
            static fn(array $matches): string => '(?P<' . $matches[1] . '>[^/]+)',
            $quoted
        );

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'regex' => '~^' . $regex . '/?$~',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        // Ҳангоми кушодани лоиҳа аз зерпапка қисми base path хориҷ мешавад.
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($basePath !== '' && $basePath !== '.' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }
        $path = '/' . ltrim($path, '/');

        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches) !== 1) {
                continue;
            }

            $pathMatched = true;

            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            ($route['handler'])($params);
            return;
        }

        if ($pathMatched) {
            http_response_code(405);
            header('Allow: GET');
            $this->renderError(405, 'Методи дархост иҷозат дода нашудааст.');
            return;
        }

        http_response_code(404);
        $this->renderError(404, 'Саҳифаи дархостшуда ёфт нашуд.');
    }

    private function renderError(int $code, string $message): void
    {
        $view = new View(dirname(__DIR__, 2) . '/views');
        $view->render('errors/error', [
            'pageTitle' => $code . ' — Хато',
            'errorCode' => $code,
            'errorMessage' => $message,
        ]);
    }
}
