<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreMedicineRequest;
use App\Http\Requests\Api\V1\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Models\MedicineImage;
use App\Services\CloudinaryService;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class MedicineController extends ApiController
{
    public function __construct(
        private readonly CloudinaryService $cloudinaryService,
    ) {}

    #[QueryParameter('filter[search]', description: 'Search by brand name or scientific name', type: 'string', example: 'Panadol')]
    #[QueryParameter('filter[category_id]', description: 'Filter by category ID', type: 'int', example: 1)]
    #[QueryParameter('filter[is_available]', description: 'Filter by availability (1 = available, 0 = unavailable)', type: 'int', example: 1)]
    #[QueryParameter('filter[requires_prescription]', description: 'Filter by prescription requirement (1 = yes, 0 = no)', type: 'int', example: 0)]
    #[QueryParameter('sort', description: 'Sort field. Prefix with - for descending. Allowed: price, brand_name, created_at', type: 'string', example: '-price')]
    #[QueryParameter('per_page', description: 'Number of items per page', type: 'int', default: 15, example: 10)]
    #[QueryParameter('page', description: 'Page number', type: 'int', default: 1, example: 1)]
    public function index(): JsonResponse
    {
        $medicines = QueryBuilder::for(Medicine::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function (Builder $query, string $value): void {
                    $query->where(function (Builder $q) use ($value): void {
                        $q->where('brand_name', 'like', sprintf('%%%s%%', $value))
                            ->orWhere('scientific_name', 'like', sprintf('%%%s%%', $value));
                    });
                }),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('is_available'),
                AllowedFilter::exact('requires_prescription'),
            ])
            ->allowedSorts(['price', 'brand_name', 'created_at'])
            ->with('images')
            ->paginate(request()->integer('per_page', 15));

        return $this->success(MedicineResource::collection($medicines)->response()->getData(true));
    }

    public function show(Medicine $medicine): JsonResponse
    {
        $medicine->load('images');

        return $this->success(new MedicineResource($medicine));
    }

    public function store(StoreMedicineRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['images']);

        $medicine = Medicine::query()->create($data);

        if ($request->hasFile('images')) {
            $isFirst = true;

            /** @var UploadedFile $image */
            foreach ($request->file('images') as $image) {
                $url = $this->cloudinaryService->upload($image, 'medicines');

                $medicine->images()->create([
                    'image_path' => $url,
                    'is_primary' => $isFirst,
                ]);

                $isFirst = false;
            }
        }

        $medicine->load('images');

        return $this->created(new MedicineResource($medicine));
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine): JsonResponse
    {
        $data = $request->validated();
        unset($data['images']);

        $medicine->update($data);

        if ($request->hasFile('images')) {
            /** @var UploadedFile $image */
            foreach ($request->file('images') as $image) {
                $url = $this->cloudinaryService->upload($image, 'medicines');

                $medicine->images()->create([
                    'image_path' => $url,
                    'is_primary' => false,
                ]);
            }
        }

        $medicine->load('images');

        return $this->success(new MedicineResource($medicine));
    }

    public function destroy(Medicine $medicine): JsonResponse
    {
        foreach ($medicine->images as $image) {
            $this->cloudinaryService->delete($image->image_path);
        }

        $medicine->delete();

        return $this->success(message: 'Medicine deleted successfully');
    }

    public function deleteImage(Medicine $medicine, MedicineImage $image): JsonResponse
    {
        if ($image->medicine_id !== $medicine->id) {
            return $this->notFound('Image not found for this medicine');
        }

        $this->cloudinaryService->delete($image->image_path);
        $image->delete();

        return $this->success(message: 'Image deleted successfully');
    }

    public function toggle(Medicine $medicine): JsonResponse
    {
        $medicine->update(['is_available' => ! $medicine->is_available]);
        $medicine->load('images');

        return $this->success(new MedicineResource($medicine));
    }
}
