<?php

declare(strict_types=1);

namespace App\Controller;

final class RecipeController extends AppController
{
    public function index(): void
    {
        $recipes = [
            ['id' => 1, 'title' => 'Owsianka z owocami leśnymi', 'calories' => 380, 'time' => '10 min', 'category' => 'Śniadanie'],
            ['id' => 2, 'title' => 'Kurczak curry z ryżem jaśminowym', 'calories' => 620, 'time' => '30 min', 'category' => 'Obiad'],
            ['id' => 3, 'title' => 'Sałatka grecka z serem feta', 'calories' => 310, 'time' => '15 min', 'category' => 'Kolacja'],
        ];

        $this->render('recipes', [
            'pageTitle' => 'Katalog przepisów - MealPlanner',
            'dailyCaloriesLimit' => 2200,
            'recipesCount' => count($recipes),
            'recipes' => $recipes,
        ]);
    }

    public function show(int $id): void
    {
        $this->render('recipe', [
            'pageTitle' => 'Szczegóły przepisu #' . $id,
            'recipeId' => $id,
            'recipeName' => 'Domowy przepis numer ' . $id,
            'prepTime' => '25 minut',
            'calories' => 450,
        ]);
    }
}