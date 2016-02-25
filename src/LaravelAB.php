<?php namespace Websecret\LaravelAB;

use Illuminate\Support\Facades\Cookie;

class LaravelAB
{

    public function __construct()
    {
    }

    public function boot($request, $response)
    {
        foreach (config('ab.experiments') as $experiment => $variants) {
            $response->withCookie($this->getOrSetUserKey($request, $response, $experiment));
        }
        return $response;
    }

    protected function getOrSetUserKey($request, $experiment)
    {
        $variantsCount = count(config('ab.experiments.' . $experiment));
        $cookieName = $this->getCookieName($experiment);
        $cookieValue = rand(0, $variantsCount - 1);
        $oldCookieValue = $request->cookie($cookieName);
        if ($oldCookieValue === null) {
            $cookie = cookie()->forever($cookieName, $cookieValue);
        } else {
            $cookie = cookie()->forever($cookieName, $oldCookieValue);
        }
        return $cookie;
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
