<?php

namespace Craftworks\App\Middleware;

class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (array_key_exists('oldInput', $_SESSION)) {
            $this->container->view->getEnvironment()->addGlobal('oldInput', $_SESSION['oldInput']);
        }
        $_SESSION['oldInput'] = $request->getParams();

        $response = $next($request, $response);
        return $response;
    }
}
