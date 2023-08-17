<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $listCategories = $this->showCategoriesInTable($categories);

        return view('admin.category.index', compact('categories', 'listCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $categoryChoosen = new Category();
        $listCategories = $this->showCategories($categoryChoosen, $categories);

        return view('admin.category.create', compact('listCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable',
            'name-category' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('categories.create')->withErrors($validator)->withInput();
        }

        $category = new Category();
        $category->parent_id = $request->input('parent-id');
        $category->name = $request->input('name-category');
        $category->desc = $request->input('description');
        $displayStatus = $request->input('display-status');
        $category->status = $displayStatus === 'on';
        if ($category->parent_id) {
            $parentCategory = Category::findOrFail($category->parent_id);
            $category->sort_order = $parentCategory->sort_order + $parentCategory->sort_order / 1000;
        }
        else {
            $category->sort_order = ceil(Category::max('sort_order') + 1);
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::where('id', '!=', $id)->get();
        $category = Category::findOrFail($id);
        $listCategories = $this->showCategories($category, $categories);

        return view('admin.category.edit', compact('category', 'listCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable',
            'name-category' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('categories.edit', $id)->withErrors($validator)->withInput();
        }

        $category = Category::findOrFail($id);
        $category->parent_id = $request->input('parent-id');
        $category->name = $request->input('name-category');
        $category->desc = $request->input('description');
        $displayStatus = $request->input('display-status');
        $category->status = $displayStatus === 'on';
        if ($category->parent_id) {
            $parentCategory = Category::findOrFail($category->parent_id);
            $category->sort_order = $parentCategory->sort_order + $parentCategory->sort_order / 1000;
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function deleteCategoryAndChildren($category): void
    {
        $children = Category::where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            $this->deleteCategoryAndChildren($child);
        }

        $category->delete();
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $this->deleteCategoryAndChildren($category);

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    public function showCategories($categoryChoosen, $categories, $parent_id = 0, $char = '')
    {
        $inputs = '';
        foreach ($categories as $key => $category)
        {
            if($category['parent_id'] == $parent_id)
            {
                $inputs .= '<div class="form-check">';
                if ($categoryChoosen && $categoryChoosen->parent_id == $category->id) {
                    $inputs .= '<input class="form-check-input" type="radio" name="parent-id" value="'.$category['id'].'" id="category_'.$category['id'].'" checked>';
                } else {
                    $inputs .= '<input class="form-check-input" type="radio" name="parent-id" value="'.$category['id'].'" id="category_'.$category['id'].'">';
                }
                $inputs .= '<label for="category_'.$category['id'].'">'.$char.$category['name'].'</label>';
                $inputs .= '</div>';

                $categories->forget($key);

                $inputs .= $this->showCategories($categoryChoosen, $categories, $category['id'], $char . $category['name']. ' > ');
            }
        }
        return $inputs;
    }
    
    public function showCategoriesInTable($categories, $parent_id = 0, $char = '', $numbering = '')
    {
        $inputs = '';
        $count = 1;
        foreach ($categories as $key => $category)
        {
            if($category['parent_id'] == $parent_id)
            {
                $numberingText = $numbering !== "" ? $numbering . "." . $count : $count;
                $inputs .= '<tbody>';
                $inputs .= '<tr>';
                $inputs .= '<td>'.$numberingText.'</td>';
                $inputs .= '<td>'.$char . $category->name.'</td>';
                $inputs .= '<td>';
                $inputs .= '<div class="form-check form-switch d-flex justify-content-center">';
                $inputs .= '<input class="form-check-input form-check-input-category" type="checkbox" role="switch" id="flexSwitchCheckChecked-'.$category->id.'"'.($category->status ? 'checked' : '').' name="display-status" data-category-id="'.$category->id.'">';
                $inputs .= '</div>';
                $inputs .= '</td>';
                $inputs .= '<td class="d-flex justify-content-center">';
                $inputs .= '<a href="'.route('categories.edit', $category->id).'" class="btn btn-warning btn-sm">Edit</a>';
                $inputs .= '</td>';
                $inputs .= '<td>';
                $inputs .= '<form method="post" action="'.route('categories.destroy', $category->id).'" class="d-flex justify-content-center">';
                $inputs .= csrf_field();
                $inputs .= method_field('DELETE');
                $inputs .= '<button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDelete-'.$category->id.'">';
                $inputs .= 'Delete';
                $inputs .= '</button>';
                $inputs .= '<div class="modal fade" id="confirmDelete-'.$category->id.'" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">';
                $inputs .= '<div class="modal-dialog modal-dialog-centered text-center">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteLabel">Delete Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Do you really want to delete this category?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <input type="submit" class="btn btn-danger" value="Delete"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </td>
            </tr>
            </tbody>';

                $categories->forget($key);
                $childNumbering = $numbering !== "" ? $numbering . "." . $count : (string) $count;
                $inputs .= $this->showCategoriesInTable($categories, $category['id'], $char . ' |--- ', $childNumbering);
                $count++;
            }
        }
        return $inputs;
    }

    public function updateStatusCategory(Request $request, $categoryId) {
        $isChecked = (boolean)$request->input('isChecked');

        $status = $isChecked ? 1 : 0;

        $category = Category::findOrFail($categoryId);
        $category->status = $status;
        $category->save();
    }
}
