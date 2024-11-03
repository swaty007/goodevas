<?php
namespace App\Http\Controllers\CraftablePro;

use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\Ysell\IndexYsellRequest;
use App\Http\Requests\CraftablePro\Ysell\CreateYsellRequest;
use App\Http\Requests\CraftablePro\Ysell\StoreYsellRequest;
use App\Http\Requests\CraftablePro\Ysell\EditYsellRequest;
use App\Http\Requests\CraftablePro\Ysell\UpdateYsellRequest;
use App\Http\Requests\CraftablePro\Ysell\DestroyYsellRequest;
use App\Http\Requests\CraftablePro\Ysell\BulkDestroyYsellRequest;
use App\Models\Ysell;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CraftablePro\YsellsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class YsellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexYsellRequest $request): Response | JsonResponse
    {
        $ysellsQuery = QueryBuilder::for(Ysell::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id','api_key','name'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id','api_key','name');

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($ysellsQuery->select(['id'])->pluck('id'));
        }

        $ysells = $ysellsQuery
            ->select('id','api_key','name')
            ->paginate($request->get('per_page'))->withQueryString();

        Session::put('ysells_url', $request->fullUrl());

        return Inertia::render('Ysell/Index', [
            'ysells' => $ysells,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateYsellRequest $request): Response
    {
        return Inertia::render('Ysell/Create', [
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreYsellRequest $request): RedirectResponse
    {
        $ysell = Ysell::create($request->validated());

        return redirect()->route('craftable-pro.ysells.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditYsellRequest $request, Ysell $ysell): Response
    {
        return Inertia::render('Ysell/Edit', [
            'ysell' => $ysell,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateYsellRequest $request, Ysell $ysell): RedirectResponse
    {
        $ysell->update($request->validated());

        if (session('ysells_url')) {
            return redirect(session('ysells_url'))->with(['message' => ___('craftable-pro', 'Operation successful')]);
        }

        return redirect()->route('craftable-pro.ysells.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyYsellRequest $request, Ysell $ysell): RedirectResponse
    {
        $ysell->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy resource.
     */
    public function bulkDestroy(BulkDestroyYsellRequest $request): RedirectResponse
    {
        // Mass delete of resource
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    Ysell::whereIn('id', $bulkChunk)->delete();
                });
        });

        // Individual delete of resource items
        //        DB::transaction(function () use ($request) {
        //            collect($request->validated()['ids'])->each(function ($id) {
        //                Ysell::find($id)->delete();
        //            });
        //        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Export
     */
    public function export(IndexYsellRequest $request): BinaryFileResponse
    {
        return Excel::download(new YsellsExport($request->all()), 'Ysells-'.now()->format("dmYHi").'.xlsx');
    }
}
