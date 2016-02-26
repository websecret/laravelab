<?php

namespace Websecret\LaravelAB;

use Illuminate\Support\Facades\Cookie;

class LaravelAB
{
    protected static $generatedVariants = [];

    public function handle($response)
    {
        foreach (static::$generatedVariants as $experiment => $variant) {
            $response->withCookie(cookie()->forever($this->getCookieName($experiment), $variant));
        }

        return $response;
    }

    public function getVariant($experiment, $variant = null)
    {
        $experimentVariant = $this->getExperimentVariant($experiment);

        $userVariant = config('ab.experiments.' . $experiment . '.' . $experimentVariant);
        if ($variant) {
            return $userVariant == $variant;
        }

        return $userVariant;
    }

    protected function getExperimentVariant($experiment)
    {
        $generatedVariant = array_get(static::$generatedVariants, $experiment, null);
        if ($generatedVariant !== null) {
            return $generatedVariant;
        }

        $cookieVariant = Cookie::get($this->getCookieName($experiment));
        if ($cookieVariant !== null) {
            return $cookieVariant;
        }

        return $this->generateExperimentVariant($experiment);
    }

    protected function setExperimentVariant($experiment, $variant)
    {
        static::$generatedVariants[$experiment] = $variant;
    }

    protected function generateExperimentVariant($experiment)
    {
        $variantsCount = count(config('ab.experiments.' . $experiment));
        $variant = rand(0, $variantsCount - 1);
        $this->setExperimentVariant($experiment, $variant);

        return $variant;
    }

    protected function getCookieName($experiment) {
        return config('ab.prefix') . $experiment;
    }
}
