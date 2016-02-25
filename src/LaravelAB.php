<?php namespace Websecret\LaravelAB;

use Illuminate\Support\Facades\Cookie;

class LaravelAB
{

    public function __construct()
    {
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

    public function getVariant($experiment, $variant = null)
    {
        $cookieName = $this->getCookieName($experiment);
        $cookieValue = Cookie::get($cookieName);
        $userVariant = config('ab.experiments.' . $experiment . '.' . $cookieValue);
        if($variant) {
            return $userVariant == $variant;
        }
        return $userVariant;
    }

    protected function getCookieName($experiment) {
        return config('ab.prefix') . $experiment;
    }

    public function clear($experiment = null) {
        if($experiment) {
            $cookieName = $this->getCookieName($experiment);
            Cookie::forget($cookieName);
        } else {
            foreach (config('ab.experiments') as $experiment => $variants) {
                $cookieName = $this->getCookieName($experiment);
                Cookie::forget($cookieName);
            }
        }
    }

}
