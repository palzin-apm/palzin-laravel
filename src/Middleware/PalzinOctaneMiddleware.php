<?php

namespace Palzin\Laravel\Middleware;

use Palzin\Laravel\Middleware\WebRequestMonitoring;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PalzinOctaneMiddleware extends WebRequestMonitoring
{
    /**
    * Terminates a request/response cycle.
    *
    * @param \Illuminate\Http\Request $request
    * @param \Illuminate\Http\Response $response
    * @throws \Exception
    */
    public function terminate(Request $request, Response $response): void
    {
        parent::terminate($request, $response);

        /*
        * Manually flush due to the long-running process.
        */
        palzin()->flush();
    }
}