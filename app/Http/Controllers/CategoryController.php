<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\Category\IndexCategoryService;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * @var IndexCategoryService
     */
    private $indexCategoryService;

    /**
     * ClientController constructor.
     * @param IndexCategoryService $indexCategoryService
     */

    public function __construct(IndexCategoryService $indexCategoryService)
    {
        //$this->middleware('permission: register client')->except('index');
        $this->indexCategoryService = $indexCategoryService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        if ($request->expectsJson()) {
            $categories = $this->indexCategoryService->run($request);
            return response()->json($categories);
        }

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parentCategories = Category::where('parent_id', null)->get();

        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'photo' => 'file|mimetypes:image/jpeg,image/png',
            'name' => 'required|string|unique:categories',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName   = time() . '.' . $photo->getClientOriginalExtension();
            $data['photo_url'] = $photo->storeAs('public/category_photos', $fileName, 'local');
            $data['photo_url'] = str_replace('public/', '', $data['photo_url']);
        }

        Category::create($data);

        return redirect()->route('categories.index')->with([
            'success' => true,
            'message' => 'Categoria cadastrada com sucesso'
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parentCategories = Category::where('parent_id', null)->get();
        $category = Category::findOrFail($id);

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'photo' => 'file|mimetypes:image/jpeg,image/png',
            'name' => 'required|string|unique:categories',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::findOrFail($id);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName   = time() . '.' . $photo->getClientOriginalExtension();
            $data['photo_url'] = $photo->storeAs('public/category_photos', $fileName, 'local');
            $data['photo_url'] = str_replace('public/', '', $data['photo_url']);
        }

        $category->update($data);

        return redirect()->route('categories.index')->with([
            'success' => true,
            'message' => 'Categoria editada com sucesso'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function delete($id)
    {

        $category = Category::findOrFail($id);

        return view('categories.delete', compact('category'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::destroy($id);

        return redirect()->route('categories.index')->with([
            'success' => true,
            'message' => 'Categoria removida com sucesso'
        ]);
    }
}
