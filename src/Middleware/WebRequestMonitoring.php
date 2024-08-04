<?php


namespace Palzin\Laravel\Middleware;


use Closure;
use Palzin\Laravel\Facades\Palzin;
use Illuminate\Support\Facades\Auth;
use Palzin\Laravel\Filters;
use Symfony\Component\HttpKernel\TerminableInterface;
use Palzin\Models\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class WebRequestMonitoring implements TerminableInterface
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (
            Palzin::needTransaction()
            &&
            Filters::isApprovedRequest(config('palzin-apm.ignore_url'), $request)
            &&
            $this->shouldRecorded($request)
        ) {
            $this->startTransaction($request);
        }

        return $next($request);
    }

    /**
     * Determine if Palzin should monitor current request.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function shouldRecorded($request): bool
    {
        return true;
    }

    /**
     * Start a transaction for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function startTransaction($request)
    {
        $transaction = Palzin::startTransaction(
            $this->buildTransactionName($request)
        )->markAsRequest();

        $transaction->addContext(
            'Request Body',
            Filters::hideParameters(
                $request->request->all(),
                config('palzin.hidden_parameters')
            )
        );

        if (Auth::check() && config('palzin.user')) {
            $transaction->withUser(Auth::user()->getAuthIdentifier());
        }
    }

    /**
     * Terminates a request/response cycle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     */
    public function terminate(Request $request, Response $response): void
    {
        if (Palzin::isRecording() && Palzin::hasTransaction()) {

            Palzin::transaction()
                ->addContext('Response', [
                    'status_code' => $response->getStatusCode(),
                    'version' => $response->getProtocolVersion(),
                    'charset' => $response->getCharset(),
                    'headers' => $response->headers->all(),
                ])
                ->addContext('Response Body', json_encode($response->getContent(), true))
                ->setResult($response->getStatusCode());
        }
    }

    /**
     * Generate readable name.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function buildTransactionName(Request $request)
    {
        $route = $request->route();

        if($route instanceof \Illuminate\Routing\Route) {
            $uri = $request->route()->uri();
        } else {
            $array = explode('?', $_SERVER["REQUEST_URI"]);
            $uri = array_shift($array);
        }

        return $request->method() . ' ' . $this->normalizeUri($uri);
    }

    /**
     * Normalize URI string.
     *
     * @param $uri
     * @return string
     */
    protected function normalizeUri($uri)
    {
        return '/' . trim($uri, '/');
    }
}
