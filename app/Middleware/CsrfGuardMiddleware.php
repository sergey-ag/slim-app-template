<?php

namespace Craftworks\App\Middleware;

class CsrfGuardMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->container->view->getEnvironment()->addGlobal('csrfGuard', [
            'tokenNameKey' => $this->container->csrfGuard->getTokenNameKey(),
            'tokenName' => $this->container->csrfGuard->getTokenName(),
            'tokenValueKey' => $this->container->csrfGuard->getTokenValueKey(),
            'tokenValue' => $this->container->csrfGuard->getTokenValue()
        ]);

        $response = $next($request, $response);
        return $response;
    }
}
