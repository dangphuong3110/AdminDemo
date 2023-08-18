<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(5);

        return view('admin.product.index', compact('products'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $tempProduct = new Product();
        $listCategories = $this->showCategories($tempProduct, $categories, 0, '');
        $manufacturers = Manufacturer::all();

        return view('admin.product.create', compact('listCategories', 'manufacturers'));
    }

    public function attachParentCategories($product, $categoryIds): void
    {
        $product->categories()->detach();
        foreach ($categoryIds as $categoryId) {
            $product->categories()->syncWithoutDetaching($categoryId);
            $this->attachParentCategoriesRecursive($product, $categoryId);
        }
    }

    public function attachParentCategoriesRecursive($product, $categoryId): void
    {
        $category = Category::where('id', $categoryId)->first();
        if ($category) {
            $parentCategory = Category::where('id', $category->parent_id)->first();

            if ($parentCategory) {
                $product->categories()->syncWithoutDetaching($parentCategory);

                $this->attachParentCategoriesRecursive($product, $parentCategory->id);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name-product' => 'required|max:255',
            'price' => 'numeric|nullable',
            'stock-quantity' => 'integer|nullable',
            'category_id' => 'nullable',
            'manufacturer_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.create')->withErrors($validator)->withInput();
        }

        $product = new Product();
        $product->name = $request->input('name-product');
        $product->shortDesc = $request->input('short-description');
        $product->detailDesc = $request->input('detail-description');
        $product->price = $request->input('price');
        $product->quantity = $request->input('stock-quantity');
        $product->link_video = $request->input('video');

        $displayStatus = $request->input('display-status');

        $product->status = $displayStatus === 'on';

        $product->manufacturer_id = $request->input('manufacturer');

        $product->save();

        if ($request->input('category_ids')) {
            $this->attachParentCategories($product, $request->input('category_ids'));
        }

        $image = $request->file('image');
        if($image) {
            $extension = $image->getClientOriginalExtension();

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array($extension, $allowedExtensions)) {
                $imageName = 'product' . $product->id . '.jpg';
                $image->move(public_path('assets/image/product'), $imageName);
                $imgProduct = new Image();
                $imgProduct->product_id = $product->id;
                $imgProduct->path = $imageName;
                $imgProduct->save();
            } else {
                return redirect()->route('products.create')->with('failure', 'The uploaded file must be in the correct image format (jpg, jpeg, png, gif).');
            }
        }

        return redirect()->route('products.index')->with('success', 'Product has been added successfully.');
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
        $categories = Category::all();
        $manufacturers = Manufacturer::all();
        $product = Product::findOrFail($id);
        $listCategories = $this->showCategories($product, $categories);

        return view('admin.product.edit', compact('product', 'listCategories', 'manufacturers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name-product' => 'required|max:255',
            'price' => 'numeric|nullable',
            'stock-quantity' => 'integer|nullable',
            'category_id' => 'nullable',
            'manufacturer_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.edit', $id)->withErrors($validator)->withInput();
        }

        $product = Product::findOrFail($id);
        $product->name = $request->input('name-product');
        $product->shortDesc = $request->input('short-description');
        $product->detailDesc = $request->input('detail-description');
        $product->price = $request->input('price');
        $product->quantity = $request->input('stock-quantity');
        $product->link_video = $request->input('video');

        $displayStatus = $request->input('display-status');
        if($displayStatus === 'on'){
            $product->status = true;
        }
        else{
            $product->status = false;
        }

        $product->manufacturer_id = $request->input('manufacturer');

        $product->save();
        if ($request->input('category_ids')) {
            $this->attachParentCategories($product, $request->input('category_ids'));
        }

        $image = $request->file('image');
        if($image) {
            $extension = $image->getClientOriginalExtension();

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array($extension, $allowedExtensions)) {
                if($product->images->first()) {
                    $oldImagePath = public_path('assets/image/product/' . $product->images->first()->path);
                    if(file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $imageName = 'product' . $product->id . '.jpg';
                $image->move(public_path('assets/image/product'), $imageName);
                $imgProduct = Image::where('product_id', $product->id)->first();
                $imgProduct->path = $imageName;
                $imgProduct->save();
            } else {
                return redirect()->route('products.edit', $product->id)->with('failure', 'The uploaded file must be in the correct image format (jpg, jpeg, png, gif).');
            }
        }

        return redirect()->route('products.index')->with('success', 'Product has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if ($product->images->first()) {
            $oldImagePath = public_path('assets/image/product/' . $product->images->first()->path);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $image = Image::where('product_id', $product->id)->first();
        if ($image) {
            $image->delete();
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function showCategories($product, $categories, $parent_id = 0, $char = '')
    {
        $inputs = '';
        foreach ($categories as $key => $category)
        {
            if($category['parent_id'] == $parent_id)
            {
                $inputs .= '<div class="form-check">';
                if ($product && $product->categories->contains($category)) {
                    $inputs .= '<input class="form-check-input" type="checkbox" name="category_ids[]" value="'.$category['id'].'" id="category_'.$category['id'].'" checked>';
                } else {
                    $inputs .= '<input class="form-check-input" type="checkbox" name="category_ids[]" value="' . $category['id'] . '" id="category_' . $category['id'] . '">';
                }
                $inputs .= '<label for="category_'.$category['id'].'">'.$char.$category['name'].'</label>';
                $inputs .= '</div>';

                $categories->forget($key);

                $inputs .= $this->showCategories($product, $categories, $category['id'], $char . $category['name']. ' > ');
            }
        }
        return $inputs;
    }

    public function updateStatusProduct(Request $request, $productId) {
        $isChecked = (boolean)$request->input('isChecked');

        $status = $isChecked ? 1 : 0;

        $product = Product::findOrFail($productId);
        $product->status = $status;
        $product->save();
    }
}
