<?php namespace Websecret\LaravelAB;

use Illuminate\Support\Facades\Cookie;

class LaravelAB
{

    public function handle($request, $response)
    {
        foreach (config('ab.experiments') as $experiment => $variants) {
            $cookie = $this->getOrSetUserKey($request, $response, $experiment);
            if(is_null($cookie)) {
                continue;
            }
            $response->withCookie();
        }
        return $response;
    }

    protected function getOrSetUserKey($request, $experiment)
    {
        $variantsCount = count(config('ab.experiments.' . $experiment));
        $cookieName = $this->getCookieName($experiment);
        $cookieValue = rand(0, $variantsCount - 1);
        $oldCookieValue = $request->cookie($cookieName);
        if(!is_null($oldCookieValue)) {
            return null;
        }
        return cookie()->forever($cookieName, $cookieValue);
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
