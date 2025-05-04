<?php

namespace App\Http\Controllers\Admin;

use App\Models\Newsletter;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\News;
use App\Models\Research;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NewsletterAdminController extends Controller
{
    public function index()
    {
        $subscribers = Newsletter::with('categories')->latest()->paginate(15);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(Newsletter $newsletter)
    {
        // Eliminar también las relaciones en la tabla pivot
        $newsletter->categories()->detach();
        $newsletter->delete();
        return back()->with('success', 'Suscriptor eliminado correctamente');
    }
    
    public function toggleActive(Newsletter $newsletter)
    {
        $newsletter->update(['is_active' => !$newsletter->is_active]);
        $status = $newsletter->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Suscriptor {$status} correctamente");
    }

    public function showSendForm()
    {
        $subscribersCount = Newsletter::where('is_active', true)->count();
        $categories = Category::all();
        return view('admin.newsletter.send', compact('subscribersCount', 'categories'));
    }
    
    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'news_count' => 'required|integer|min:1|max:10',
            'research_count' => 'nullable|integer|min:0|max:5',
            'columns_count' => 'nullable|integer|min:0|max:5',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);
        
        // Obtener suscriptores activos filtrando por categorías si es necesario
        $query = Newsletter::where('is_active', true);
        
        if ($request->has('filter_by_categories') && $request->has('categories') && count($request->categories) > 0) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', $request->categories);
            });
        }
        
        $subscribers = $query->get();
        
        if ($subscribers->isEmpty()) {
            return back()->with('error', 'No hay suscriptores activos para las categorías seleccionadas.');
        }
        
        // Obtener las últimas noticias
        $news = News::published()->latest()->take($request->news_count)->get();
        
        if ($news->isEmpty()) {
            return back()->with('error', 'No hay noticias disponibles para enviar.');
        }
        
        // Obtener noticias destacadas
        $featuredNews = News::published()->featured()->latest()->take(1)->get();
        
        // Obtener investigaciones si se solicitaron
        $researches = collect();
        if ($request->include_research && $request->research_count > 0) {
            $researches = Research::published()->latest()->take($request->research_count)->get();
        }
        
        // Obtener columnas si se solicitaron
        $columns = collect();
        if ($request->include_columns && $request->columns_count > 0) {
            $columns = Column::published()->latest()->take($request->columns_count)->get();
        }
        
        // Enviar el newsletter a cada suscriptor
        $sentCount = 0;
        $errorCount = 0;
        
        foreach ($subscribers as $subscriber) {
            try {
                // Generar token si no existe
                if (!$subscriber->token) {
                    $subscriber->token = Str::random(60);
                    $subscriber->save();
                }
                
                Mail::to($subscriber->email)
                    ->send(new NewsletterMail(
                        $news, 
                        $request->subject, 
                        $subscriber->token,
                        $featuredNews,
                        $researches,
                        $columns,
                        $subscriber
                    ));
                $sentCount++;
            } catch (\Exception $e) {
                // Registrar error
                Log::error("Error enviando newsletter a {$subscriber->email}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $message = "Newsletter enviado a {$sentCount} suscriptores.";
        if ($errorCount > 0) {
            $message .= " Hubo {$errorCount} errores durante el envío.";
        }
        
        return back()->with('success', $message);
    }
    
    public function edit(Newsletter $newsletter)
    {
        $categories = Category::all();
        return view('admin.newsletter.edit', compact('newsletter', 'categories'));
    }
    
    public function update(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email,' . $newsletter->id,
            'name' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);
        
        $newsletter->update([
            'email' => $request->email,
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);
        
        // Sincronizar categorías
        $newsletter->categories()->sync($request->categories ?? []);
        
        return redirect()->route('admin.newsletter.index')
            ->with('success', 'Suscriptor actualizado correctamente');
    }
}