<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_date')->get();
        return view('admin.agenda.index', compact('events'));
    }

    public function create()
    {
        return view('admin.agenda.form', ['event' => new Event()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        Event::create($data);
        return redirect()->route('admin.agenda.index')->with('success', 'Evento creado correctamente.');
    }

    public function edit(Event $agenda)
    {
        return view('admin.agenda.form', ['event' => $agenda]);
    }

    public function update(Request $request, Event $agenda)
    {
        $data = $this->validated($request, $agenda->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $agenda->update($data);
        return redirect()->route('admin.agenda.index')->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $agenda)
    {
        $agenda->delete();
        return redirect()->route('admin.agenda.index')->with('success', 'Evento eliminado.');
    }

    public function toggleActive(Event $agenda)
    {
        $agenda->update(['active' => !$agenda->active]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => "nullable|string|unique:events,slug,{$ignoreId}",
            'description' => 'nullable|string',
            'type'        => 'required|in:conference,webinar,deadline,workshop,summit',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'location'    => 'nullable|string|max:255',
            'is_online'   => 'boolean',
            'url'         => 'nullable|url',
            'organizer'   => 'nullable|string|max:255',
            'is_free'     => 'boolean',
            'featured'    => 'boolean',
            'active'      => 'boolean',
        ]);
    }
}
