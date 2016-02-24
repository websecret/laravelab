<?php namespace Secret\LaravelAB;

use Illuminate\Support\Facades\Cookie;

class LaravelAB
{
    protected $app;

    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();
        }
        $this->app = $app;
        $this->boot();
    }

    protected function boot()
    {
        foreach (config('ab.experiments') as $experiment => $variants) {
            $this->getOrSetUserKey($experiment);
        }
    }

    protected function getOrSetUserKey($experiment)
    {
        $variantsCount = count(config('ab.experiments.' . $experiment));
        $cookieValue = rand(0, $variantsCount - 1);
        $cookieName = 'ab-' . $experiment;
        if (!Cookie::has($cookieName)) {
            $this->app['request']->cookie($cookieName, $cookieValue);
        }
    }

    public function getVariant($experiment)
    {
        $cookieName = 'ab-' . $experiment;
        $cookieValue = Cookie::get($cookieName);
        $variant = config('ab.experiments.' . $experiment . '.' . $cookieValue);
        return $variant;
    }

}
