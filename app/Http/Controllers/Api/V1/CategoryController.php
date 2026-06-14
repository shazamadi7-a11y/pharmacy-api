<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;

final class CategoryController extends ApiController
{
    public function __construct(
        private readonly CloudinaryService $cloudinaryService,
    ) {}

    public function index(): JsonResponse
    {
        $categories = Category::query()->withCount('medicines')->get();

        return $this->success(CategoryResource::collection($categories));
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('medicines');

        return $this->success(new CategoryResource($category));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->cloudinaryService->upload($request->file('image'), 'categories');
        }

        $category = Category::query()->create($data);
        $category->loadCount('medicines');

        return $this->created(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($category->image !== null && $category->image !== '') {
                $this->cloudinaryService->delete($category->image);
            }

            $data['image'] = $this->cloudinaryService->upload($request->file('image'), 'categories');
        }

        $category->update($data);
        $category->loadCount('medicines');

        return $this->success(new CategoryResource($category));
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->image !== null && $category->image !== '') {
            $this->cloudinaryService->delete($category->image);
        }

        $category->delete();

        return $this->success(message: 'Category deleted successfully');
    }
}
