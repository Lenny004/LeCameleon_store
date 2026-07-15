<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\SavedSearchRequest;
use App\Models\SavedSearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedSearchController extends Controller
{
    public function index(Request $request): View
    {
        $savedSearches = $request->user()
            ->savedSearches()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('store.account.saved-searches.index', compact('savedSearches'));
    }

    public function store(SavedSearchRequest $request): RedirectResponse
    {
        $request->user()->savedSearches()->create([
            'name' => $request->validated('name'),
            'query_params' => $request->validated('query_params'),
        ]);

        return back()->with('success', 'Búsqueda guardada.');
    }

    public function destroy(Request $request, SavedSearch $savedSearch): RedirectResponse
    {
        abort_unless($savedSearch->user_id === $request->user()->id, 403);

        $savedSearch->delete();

        return back()->with('success', 'Búsqueda eliminada.');
    }
}
