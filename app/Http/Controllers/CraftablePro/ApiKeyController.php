<?php

namespace App\Http\Controllers\CraftablePro;

use App\Exports\CraftablePro\ApiKeysExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\ApiKey\BulkDestroyApiKeyRequest;
use App\Http\Requests\CraftablePro\ApiKey\CreateApiKeyRequest;
use App\Http\Requests\CraftablePro\ApiKey\DestroyApiKeyRequest;
use App\Http\Requests\CraftablePro\ApiKey\EditApiKeyRequest;
use App\Http\Requests\CraftablePro\ApiKey\IndexApiKeyRequest;
use App\Http\Requests\CraftablePro\ApiKey\StoreApiKeyRequest;
use App\Http\Requests\CraftablePro\ApiKey\UpdateApiKeyRequest;
use App\Models\ApiKey;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexApiKeyRequest $request): Response|JsonResponse
    {
        $apiKeysQuery = QueryBuilder::for(ApiKey::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'name', 'type', 'key', 'additional_data'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'name', 'type', 'key', 'additional_data');

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($apiKeysQuery->select(['id'])->pluck('id'));
        }

        $apiKeys = $apiKeysQuery
            ->select('id', 'name', 'type', 'key', 'additional_data')
            ->paginate($request->get('per_page'))->withQueryString();

        Session::put('apiKeys_url', $request->fullUrl());

        return Inertia::render('ApiKey/Index', [
            'apiKeys' => $apiKeys,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateApiKeyRequest $request): Response
    {
        return Inertia::render('ApiKey/Create', [

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApiKeyRequest $request): RedirectResponse
    {
        $apiKey = ApiKey::create($request->validated());

        return redirect()->route('craftable-pro.api-keys.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditApiKeyRequest $request, ApiKey $apiKey): Response
    {
        return Inertia::render('ApiKey/Edit', [
            'apiKey' => $apiKey,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApiKeyRequest $request, ApiKey $apiKey): RedirectResponse
    {
        $apiKey->update($request->validated());

        if (session('apiKeys_url')) {
            return redirect(session('apiKeys_url'))->with(['message' => ___('craftable-pro', 'Operation successful')]);
        }

        return redirect()->route('craftable-pro.api-keys.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyApiKeyRequest $request, ApiKey $apiKey): RedirectResponse
    {
        $apiKey->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy resource.
     */
    public function bulkDestroy(BulkDestroyApiKeyRequest $request): RedirectResponse
    {
        // Mass delete of resource
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    ApiKey::whereIn('id', $bulkChunk)->delete();
                });
        });

        // Individual delete of resource items
        //        DB::transaction(function () use ($request) {
        //            collect($request->validated()['ids'])->each(function ($id) {
        //                ApiKey::find($id)->delete();
        //            });
        //        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Export
     */
    public function export(IndexApiKeyRequest $request): BinaryFileResponse
    {
        return Excel::download(new ApiKeysExport($request->all()), 'ApiKeys-'.now()->format('dmYHi').'.xlsx');
    }
}
