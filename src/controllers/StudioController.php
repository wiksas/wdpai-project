<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Service\StudioCatalog;
use App\Support\PolishDate;

class StudioController extends AppController
{
    // Obsługa trasy: /studio?style=moyogi&species=pine (style, kalendarz zabiegów, drutowanie, próbne cięcie)
    public function index(): void
    {
        $this->requireLogin();

        $catalog = new StudioCatalog();
        $styleId = $catalog->normalizeStyle($_GET['style'] ?? null);
        $speciesId = $catalog->normalizeSpecies($_GET['species'] ?? null);
        $today = PolishDate::today();

        $this->render('studio', [
            'pageTitle' => 'Studio formowania | KOMOREBI',
            'activeNav' => 'studio',
            'styles' => $catalog->styles($styleId),
            'style' => $catalog->style($styleId),
            'speciesTabs' => $catalog->speciesTabs($speciesId),
            'calendar' => $catalog->calendar($speciesId, $today),
            'monthName' => PolishDate::month($today),
        ]);
    }
}
