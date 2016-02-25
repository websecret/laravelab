<?php namespace Websecret\LaravelAB\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use Websecret\LaravelAB\LaravelAB as AB;

class LaravelAB
{
    protected $ab;

    public function __construct(Container $container, AB $ab)
    {
        $this->ab = $ab;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response = $this->ab->boot($request, $response);
        return $response;
    }

}
