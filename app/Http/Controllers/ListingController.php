<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchListingRequest;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ListingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ListingResource::collection(Listing::query()->latest()->paginate($this->perPage()));
    }

    public function store(StoreListingRequest $request): JsonResponse
    {
        $listing = Listing::create($request->validated());

        return (new ListingResource($listing))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Listing $listing): ListingResource
    {
        return new ListingResource($listing);
    }

    public function update(UpdateListingRequest $request, Listing $listing): ListingResource
    {
        $listing->update($request->validated());

        return new ListingResource($listing->refresh());
    }

    public function destroy(Listing $listing): Response
    {
        $listing->delete();

        return response()->noContent();
    }

    public function search(SearchListingRequest $request): AnonymousResourceCollection|JsonResponse
    {
        $filters = $request->validated();
        $query = Listing::query();

        $query->when(isset($filters['type']), fn ($query) => $query->where('type', $filters['type']));
        $query->when(isset($filters['min_price']), fn ($query) => $query->where('price', '>=', $filters['min_price']));
        $query->when(isset($filters['max_price']), fn ($query) => $query->where('price', '<=', $filters['max_price']));
        $query->when(isset($filters['bedrooms']), fn ($query) => $query->where('bedrooms', $filters['bedrooms']));

        if (isset($filters['latitude'], $filters['longitude'], $filters['radius_km'])) {
            $this->applyDistanceFilter($query, $filters);
        }

        return ListingResource::collection($query->latest()->paginate($filters['per_page'] ?? 15));
    }

    private function applyDistanceFilter($query, array $filters): void
    {
        $latitude = (float) $filters['latitude'];
        $longitude = (float) $filters['longitude'];
        $radius = (float) $filters['radius_km'];

        if (DB::connection()->getDriverName() === 'sqlite') {
            $query->whereRaw(
                '((latitude - ?) * (latitude - ?) + (longitude - ?) * (longitude - ?)) <= ?',
                [$latitude, $latitude, $longitude, $longitude, ($radius / 111) ** 2]
            );

            return;
        }

        $distanceSql = '(6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude))))';
        $query->select('*')->selectRaw("$distanceSql as distance_km", [$latitude, $longitude, $latitude])
            ->whereRaw("$distanceSql <= ?", [$latitude, $longitude, $latitude, $radius])
            ->orderBy('distance_km');
    }

    private function perPage(): int
    {
        return min(max((int) request()->integer('per_page', 15), 1), 100);
    }
}