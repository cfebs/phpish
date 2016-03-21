<?php

/**
 * $r = new Router();
 *
 * $r->add('/home', ['method' => 'get', 'type' => true]);
 */
class Router
{
    protected $_request;

    protected $_routes = [];

    const TYPE_DEFAULT = 1;
    const TYPE_STRICT = 2;
    const TYPE_REGEX = 3;

    const REGEX_ALL = '.*';
    const REGEX_ROUTE_NUM = '[[:digit:]]+';
    const REGEX_ROUTE_ALPHA = '[[:alpha:]]+';
    const REGEX_ROUTE_ALPHA_NUM = '[[:alnum:]]+';

    public function __construct()
    {
    }

    // @chain
    public function add($path, array $options = [], $callback = null)
    {
        $options['type'] = self::TYPE_DEFAULT;
        $this->_routes[] = [$path, $options, $callback];
        return $this;
    }

    // @chain
    public function addRegex($path, array $options = [], $callback = null)
    {
        $options['type'] = self::TYPE_REGEX;
        $this->_routes[] = [$path, $options, $callback];
        return $this;
    }

    // @chain
    public function addStrict($path, array $options = [], $callback = null)
    {
        $options['type'] = self::TYPE_STRICT;
        $this->_routes[] = [$path, $options, $callback];
        return $this;
    }

    // @chain
    public function addMulti($routes = [])
    {
        foreach ($routes as $route) {
            list($path, $options, $callback) = $route;
            $this->add($path, $options, $callback);
        }
    }

    public function run(Request $request)
    {
        $request_method = $request->getMethod();
        $request_uri = $request->getUri();

        foreach ($this->_routes as $route_data) {
            $param_matches = null;
            $matches = null;
            $regex = null;

            list($path, $options, $callback) = $route_data;
            $type = $options['type'];

            if (isset($options['method'])) {
                if (strtoupper($options['method']) !== strtoupper($request_method)) {
                    continue;
                }
            }

            // strict route
            switch ($type) {
                case self::TYPE_STRICT:
                    if ($request_uri != $path) {
                        continue;
                    }

                    return $callback();
                    break;

                case self::TYPE_DEFAULT:
                    list($regex, $match_names) = $this->defaultPathToRegex($path);
                    $match_names = $match_names ?: [];
                    $match = preg_match($regex, $request_uri, $matches);

                    if (!$match) {
                        continue;
                    }

                    $i = 0;
                    $param_matches = [];
                    foreach ($match_names as $name) {
                        $param_matches[$name] = $matches[++$i];
                    }

                    return $callback($request, $param_matches);

                    break;

                case self::TYPE_REGEX:
                    $regex = '/^' . $path . '$/';
                    $matches = [];
                    $match = preg_match($regex, $request_uri, $matches);

                    if (!$match) {
                        continue;
                    }

                    array_shift($matches);
                    $callback($request, $matches);
                    return true;

                    break;

                default:
                    throw new Exception('Route type not properly defined');
                    break;
            }
        }

        return false;
    }

    // support different shortcuts
    public function defaultPathToRegex($path)
    {
        $parts = explode('/', $path);
        $new_parts = [];
        foreach ($parts as $idx => $part) {
            $new_regex_part = null;
            // /blog/:name
            if ($part[0] === ':') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ROUTE_ALPHA_NUM . ')';
            }
            // /blog/#id
            else if ($part[0] === '#') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ROUTE_NUM . ')';
            }
            // /blog/@id
            else if ($part[0] === '@') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ROUTE_ALPHA . ')';
            }
            // /blog/*path
            else if ($part[0] === '*') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ALL . ')';
            }

            $new_parts[] = ($new_regex_part ?: $part);
        }

        $regex = '/^' . implode('\/', $new_parts) . '$/';

        return [$regex, $match_names];
    }
}
