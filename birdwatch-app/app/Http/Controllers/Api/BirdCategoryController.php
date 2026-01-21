<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBirdCategoryRequest;
use App\Http\Requests\UpdateBirdCategoryRequest;
use App\Http\Resources\BirdCategoryResource;
use App\Models\BirdCategory;
use Illuminate\Support\Str;

class BirdCategoryController extends Controller
{
    public function index()
    {
        return BirdCategoryResource::collection(
            BirdCategory::query()->orderBy('name')->paginate(50)
        );
    }

    public function store(StoreBirdCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);

        $cat = BirdCategory::create($data);

        return (new BirdCategoryResource($cat))->response()->setStatusCode(201);
    }

    public function show(BirdCategory $category)
    {
        return new BirdCategoryResource($category);
    }

    public function update(UpdateBirdCategoryRequest $request, BirdCategory $category)
    {
        $data = $request->validated();

        if (array_key_exists('name', $data)) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return new BirdCategoryResource($category->refresh());
    }

    public function destroy(BirdCategory $category)
    {
        $category->delete();

        return response()->noContent();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        $q = BirdCategory::query()->where('slug', $slug);
        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        }

        while ($q->exists()) {
            $slug = "{$base}-{$i}";
            $i++;

            $q = BirdCategory::query()->where('slug', $slug);
            if ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }
}
