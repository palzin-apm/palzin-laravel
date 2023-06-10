<?php

if (!function_exists('palzin')) {
    /**
     * @return \Palzin\Laravel\Palzin
     */
    function palzin(): \Palzin\Laravel\Palzin
    {
        return app('palzin');
    }
}
