<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use Illuminate\Http\Request;

class AiModelController extends Controller
{
    public function index(Request $request)
    {
        $models = AiModel::active()
            ->orderBy('sort_order')
            ->get();

        $companies  = $models->pluck('company', 'company_slug')->unique()->sort();
        $types      = $models->pluck('type')->unique()->sort()->values();

        return view('modelos.index', compact('models', 'companies', 'types'));
    }
}
