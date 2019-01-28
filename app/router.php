<?php

namespace Scratch;

class Router
{
    private $app_routes;
    private $default_headers;
    private $ROUTE_SPEC_REGEX = "/:([^\/$]+)/";

    function __construct($routes, $default_headers)
    {
        $this->app_routes = $routes;
        $this->default_headers = $default_headers;
    }

    function call($env)
    {
        $path = $env['REQUEST_URI'];
        $route = $this->find_route($path);

        if (empty($route))
        {
            return array(404, 'text/html', 'Not Found');
        }

        if (is_array($route['proc'])) 
        {
            return $route['proc'];
        }

        return $this->handle($route);
    }

    private function find_route($path)
    {
        foreach ($this->app_routes as $route_spec => $route_proc)
        {
            $matches;
            $routeSpecAsRegex = $this->translate_route_key_to_regex($route_spec);

            if (preg_match($routeSpecAsRegex, $path, $matches) == 1) 
            {
                $tokens = $this->scan_route_key_for_tokens($route_spec);
                $token_values = array_slice($matches, 1);

                $route_args = [];
                if (!empty($tokens))
                {
                    $route_args = array_combine($tokens, $token_values);
                }

                return ['proc'=>$route_proc, 'args'=>$route_args];
            }
        }
        return null;
    }   

    private function handle($route)
    {
        $engine_result = $route['proc']($route['args']);
                
        $status_code = $engine_result[0];
        $content_type = $engine_result[1];
        $content = $engine_result[2];
        $extra_headers = $engine_result[3] ?? [];
                
        return array(
            $status_code, 
            array_merge_recursive(
                ['Content-type' => $content_type],
                $extra_headers,
                $this->default_headers
            ),
            $content
        );
    }

    private function translate_route_key_to_regex($route_spec)
    {
        $path_escaped = str_replace('&', '\&', $route_spec);
        $path_escaped = str_replace('?', '\?', $path_escaped);
        return "/^" . str_replace("/", "\/", preg_replace($this->ROUTE_SPEC_REGEX, "([^/]+)", $path_escaped)) . "$/";
    }

    private function scan_route_key_for_tokens($route_spec)
    {
        $matches;
        preg_match_all($this->ROUTE_SPEC_REGEX, $route_spec, $matches);
        if (!empty($matches))
        {
            return array_slice($matches, 1)[0];
        }

    }
}

?>