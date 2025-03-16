<?php defined('SYSPATH') or die('No direct script access.');

// хелпер в одном из фреймворков
class Helper_Seo
{
    private static $routes = [];

    public static function checkRedirectStatic()
    {
        $url = Helper_Url::currentUrl();
        $oldCategoryUrls = Kohana::$config->load('params')->oldStaticUrls;
        foreach ($oldCategoryUrls as $old => $new)
        {
            if($url == $old)
                HTTP::redirect(str_replace($old, $new, $url), 301);
        }
    }


    //Проверяет необходимсть редиректа на основе глобальной переменныой фреймворка
    public static function checkRedirectCategory()
    {
        $oldCategoryUrls = Kohana::$config->load('params')->oldCategoryUrls;
        foreach ($oldCategoryUrls as $old => $new)
        {
            $url = Helper_Url::currentUrl();
            if(strpos($url, $old) !== false)
            {
                HTTP::redirect(str_replace($old, $new, $url), 301);

            }
        }
    }

    //Проверяет необходимсть редиректа
    public static function checkRedirect()
    {
        $uri = Request::current()->uri();
        if (($redirectTo = self::getRedirectUrl($uri)) !== false)
        {
            HTTP::redirect($redirectTo, 301);
        }
    }

    private static function setRedirectUrls()
    {
        $redirectUrls = Kohana::$config->load('params')->redirectUrls;
        foreach ($redirectUrls as $fromUri => $toUri)
        {
            Route::set($fromUri, $toUri);
        }
    }

    public static function getRedirectUrl($uri)
    {
        if (empty(self::$redirectUrls)) self::setRedirectUrls();

        $allRoutes = Route::all();
        if (in_array($uri, array_keys($allRoutes)))
        {
            $route = $allRoutes[$uri];
            return Helper_Url::createUrl($route->uri(), [], true);
        }

        return false;
    }
}
