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
    const REGEX_ALPHA_NUM = '[[:alnum:]]+';

    public function __construct(Request $request)
    {
        $this->_request = $request;
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

    public function run()
    {
        $request_method = $this->_request->getMethod();
        $request_uri = $this->_request->getUri();

        foreach ($this->_routes as $route_data) {
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

                    $callback && $callback();
                    return true;
                    break;

                case self::TYPE_DEFAULT:
                    list($regex, $match_names) = $this->defaultPathToRegex($path);
                    $match = preg_match($regex, $path, $matches);
                    dump($regex, $match_names, $match);

                    if (!$match) {
                        continue;
                    }

                    $callback && $callback($this->_request, $matches);

                    // @todo handle match
                    break;

                case self::TYPE_REGEX:
                    $regex = $path;
                    preg_match($regex);
                    continue;
                    break;

                default:
                    throw new Exception('Route type not properly defined');
            }

        }
    }

    // support different shortcuts
    public function defaultPathToRegex($path)
    {
        $parts = explode('/', $path);
        $new_parts = [];
        foreach ($parts as $idx => $part) {
            $new_regex_part = null;
            //  /blog/:name
            if ($part[0] === ':') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ALPHA_NUM . ')';
            }
            //  /blog/#id
            else if ($part[0] === '#') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ROUTE_NUM . ')';
            }
            //  /blog/*path
            else if ($part[0] === '*') {
                $match_name = substr($part, 1);
                $match_names[] = $match_name;
                $new_regex_part = '(' . self::REGEX_ALL . ')';
            }

            $new_parts[] = ($new_regex_part ?: $part);
        }

        $regex = '/' . implode('\/', $new_parts) . '/';

        return [$regex, $match_names];
    }
}
