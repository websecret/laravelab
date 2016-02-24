<?php namespace Secret\LaravelAB;

use Illuminate\Http\Request;
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
        $cookieName = $this->getCookieName($experiment);
        $cookieValue = rand(0, $variantsCount - 1);
        if (!Cookie::has($cookieName)) {
            Cookie::forever($cookieName, $cookieValue);
        }
    }

    public function getVariant($experiment)
    {
        $cookieName = $this->getCookieName($experiment);
        $cookieValue = Cookie::get($cookieName);
        $variant = config('ab.experiments.' . $experiment . '.' . $cookieValue);
        return $variant;
    }

    protected function getCookieName($experiment) {
        return config('ab.prefix') . $experiment;
    }

}
