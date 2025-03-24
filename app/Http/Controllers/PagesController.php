<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Mostrar la página de política de privacidad.
     *
     * @return \Illuminate\View\View
     */
    public function privacy()
    {
        return view('pages.privacy', [
            'title' => 'Política de Privacidad - ConocIA',
            'lastUpdated' => '24 de marzo, 2025'
        ]);
    }

    /**
     * Mostrar la página de términos de uso.
     *
     * @return \Illuminate\View\View
     */
    public function terms()
    {
        return view('pages.terms', [
            'title' => 'Términos de Uso - ConocIA',
            'lastUpdated' => '24 de marzo, 2025'
        ]);
    }

    /**
     * Mostrar la página de política de cookies.
     *
     * @return \Illuminate\View\View
     */
    public function cookies()
    {
        return view('pages.cookies', [
            'title' => 'Política de Cookies - ConocIA',
            'lastUpdated' => '24 de marzo, 2025'
        ]);
    }
}