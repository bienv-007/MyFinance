<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Categorie::query()->orderBy('nom_categorie');

        if ($search = $request->string('search')->toString()) {
            $query->where('nom_categorie', 'like', "%{$search}%");
        }

        return response()->json(
            CategoryResource::collection($query->paginate(10))
        );
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Categorie::create($request->validated());

        return response()->json(['data' => new CategoryResource($category)], 201);
    }

    public function show(Categorie $category): JsonResponse
    {
        return response()->json(['data' => new CategoryResource($category)]);
    }

    public function update(CategoryRequest $request, Categorie $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json(['data' => new CategoryResource($category)]);
    }

    public function destroy(Categorie $category): JsonResponse
    {
        if ($category->depenses()->exists()) {
            return response()->json(['message' => 'Catégorie utilisée par des dépenses.'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}
