<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Selection\Selection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ShowSelectionController extends Controller
{
    public function __invoke(string $uniqueId)
    {
        $selection = Selection::query()
            ->with(['adverts', 'adverts.catalogable', 'adverts.catalogable.images'])
            ->where('uniqueid', $uniqueId)
            ->firstOrFail();

        $imagesCount = $selection->adverts->map(function ($advert) {
            return $advert->catalogable->images->count();
        })->sum();

        $apiKey = Cache::remember('api-key', 60 * 60 * 24, function () {
            return Str::random(32);
        });

        return view('frontend.selection.show',
            compact('selection', 'imagesCount', 'apiKey'));
    }
}
