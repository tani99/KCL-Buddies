<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

trait RedirectMessages
{
    /**
     * @param Request $request
     * @param array|null $data An associative array containing data
     * @return array An associative array containing specific data from the session in addition to the optional specified data.
     */
    protected final function applySessionToData(Request $request, array &$data = null): array
    {
        if (!isset($data)) {
            $data = [];
        }
        if ($request->session()->has('success')) {
            $data['success'] = $request->session()->get('success');
        }
        if ($request->session()->has('info')) {
            $data['info'] = $request->session()->get('info');
        }
        if ($request->session()->has('errors')) {
            $data['errors'] = $request->session()->get('errors');
        }
        return $data;
    }
}