<?php

namespace Palzin\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use Palzin\Models\Error;

class PalzinReportController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function report(Request $request)
    {
        if(config('palzin-apm.report_js_error')) {


            $exception = new \ErrorException(
                $request->input('message'),
                0,
                $request->input('severity'),
                $request->input('file'),
                $request->input('line'),
                $request->input('stack')
            );

            palzin()->reportException(
                $exception,
                false
            );

            return response('ok', 200);
        } else {
            return response('disabled', 200);
        }
    }
}