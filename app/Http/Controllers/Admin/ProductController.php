<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use App\Models\Image;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productCollection = new Collection();
        $lastId = Product::max('id');
        $chunkSize = 1000;

        while (true) {
            $chunkProducts = DB::table('products')
                ->select('id', 'name', 'quantity', 'price', 'status')
                ->where('id', '<=', $lastId)
                ->orderBy('id', 'desc')
                ->limit($chunkSize)
                ->get();

            if ($chunkProducts->isEmpty()) {
                break;
            }

            foreach ($chunkProducts as $p) {
                $product = new \stdClass();
                $product->id = $p->id;
                $product->name = $p->name;
                $product->quantity = $p->quantity;
                $product->price = $p->price;
                $product->status = $p->status;
                $productCollection->push($product);
            }
            $lastId -= $chunkSize;
        }

        $perPage = 25;
        $currentPage = \request('page') ?? 1;

        $startIndex = ($currentPage - 1) * $perPage + 1;
        $products = new LengthAwarePaginator(
            $productCollection->slice($startIndex - 1, $perPage),
            Product::count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );

        $categories = Category::orderBy('name')->get();
        $listCategories = $this->showCategoriesInSelectOption(0, $categories);
        $filter_name_product = '';
        $filter_status = '';

        return view('admin.product.index', compact('filter_status', 'filter_name_product', 'products', 'listCategories'));
    }

    public function filterProduct(Request $request)
    {
        $filter_name_product = $request->query('name-product-filter');
        $filter_category_id = $request->query('category-filter');
        $filter_status = $request->query('status-filter');

        if ($filter_name_product || $filter_category_id != 0 || $filter_status != 2) {
//            $productCollection = new Collection();
//            $lastId = Product::max('id');
//            $chunkSize = 1000;
//
//            while (true) {
//                $chunkProducts = Product::when($filter_name_product, function (Builder $query) use ($filter_name_product) {
//                    return $query->where('name', 'like', '%' . $filter_name_product . '%');
//                })->when($filter_category_id != 0, function (Builder $query) use ($filter_category_id) {
//                    return $query->whereHas('categories', function (Builder $q) use ($filter_category_id) {
//                    });
//                })->when($filter_status != 2, function (Builder $query) use ($filter_status){
//                    return $query->where('status', '=', $filter_status);
//                })->select('id', 'name', 'quantity', 'price', 'status')
//                    ->where('id', '<=', $lastId)
//                    ->orderBy('id', 'desc')
//                    ->limit($chunkSize)
//                    ->get();
//
//                if ($chunkProducts->isEmpty()) {
//                    break;
//                }
//
//                foreach ($chunkProducts as $p) {
//                    $product = new \stdClass();
//                    $product->id = $p->id;
//                    $product->name = $p->name;
//                    $product->quantity = $p->quantity;
//                    $product->price = $p->price;
//                    $product->status = $p->status;
//                    $productCollection->push($product);
//                }
//                $lastId -= $chunkSize;
//            }

            $productCollection = Product::when($filter_name_product, function (Builder $query) use ($filter_name_product) {
                return $query->where('name', 'like', '%' . $filter_name_product . '%');
            })->when($filter_category_id != 0, function (Builder $query) use ($filter_category_id) {
                return $query->whereHas('categories', function (Builder $q) use ($filter_category_id) {
                    $q->where('id', 'like', $filter_category_id);
                });
            })->when($filter_status != 2, function (Builder $query) use ($filter_status) {
                return $query->where('status', '=', $filter_status);
            })->orderBy('id', 'desc')->select('id', 'name', 'quantity', 'price', 'status')->get();
            $categories = Category::orderBy('name')->get();
            $listCategories = $this->showCategoriesInSelectOption($filter_category_id, $categories);

            $perPage = 25;
            $currentPage = \request('page') ?? 1;

            $startIndex = ($currentPage - 1) * $perPage + 1;

            $products = new LengthAwarePaginator(
                $productCollection->slice($startIndex - 1, $perPage),
                sizeof($productCollection),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ],
            );

            return view('admin.product.index', compact('filter_status', 'filter_name_product', 'products', 'listCategories'));
        }

        return redirect()->route('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::select('id', 'parent_id', 'name')->orderBy('name')->get();
        $tempProduct = new Product();
        $listCategories = $this->showCategories($tempProduct, $categories, 0, '');
        $manufacturers = Manufacturer::select('name')->orderBy('name')->get();

        return view('admin.product.create', compact('listCategories', 'manufacturers'));
    }

    public function attachParentCategories($product, $categoryIds): void
    {
        $product->categories()->detach();
        if ($categoryIds) {
            foreach ($categoryIds as $categoryId) {
                $product->categories()->syncWithoutDetaching($categoryId);
                $this->attachParentCategoriesRecursive($product, $categoryId);
            }
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
    public function store(Request $request): RedirectResponse
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
        if ($image) {
            $extension = $image->getClientOriginalExtension();

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowedExtensions)) {
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
        $categories = Category::select('id', 'parent_id', 'name')->orderBy('name')->get();
        $manufacturers = Manufacturer::select('name')->orderBy('name')->get();
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
        if ($displayStatus === 'on') {
            $product->status = true;
        } else {
            $product->status = false;
        }

        $product->manufacturer_id = $request->input('manufacturer');
        $product->save();
        $this->attachParentCategories($product, $request->input('category_ids'));

        $image = $request->file('image');
        if ($image) {
            $extension = $image->getClientOriginalExtension();

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowedExtensions)) {
                if ($product->images->first()) {
                    $oldImagePath = public_path('assets/image/product/' . $product->images->first()->path);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $imageName = 'product' . $product->id . '.jpg';
                $image->move(public_path('assets/image/product'), $imageName);
                $imgProduct = Image::where('product_id', $product->id)->first();
                if ($imgProduct) {
                    $imgProduct->path = $imageName;
                    $imgProduct->save();
                } else {
                    $imgProduct = new Image();
                    $imgProduct->product_id = $product->id;
                    $imgProduct->path = $imageName;
                    $imgProduct->save();
                }
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
//        if ($product->images->first()) {
//            $oldImagePath = public_path('assets/image/product/' . $product->images->first()->path);
//            if (file_exists($oldImagePath)) {
//                unlink($oldImagePath);
//            }
//        }
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
        foreach ($categories as $key => $category) {
            if ($category['parent_id'] == $parent_id) {
                $inputs .= '<div class="form-check">';
                if ($product && $product->categories->contains($category)) {
                    $inputs .= '<input class="form-check-input checkbox-category" type="checkbox" name="category_ids[]" value="' . $category['id'] . '" id="category_' . $category['id'] . '" checked>';
                } else {
                    $inputs .= '<input class="form-check-input checkbox-category" type="checkbox" name="category_ids[]" value="' . $category['id'] . '" id="category_' . $category['id'] . '">';
                }
                $inputs .= '<label for="category_' . $category['id'] . '">' . $char . $category['name'] . '</label>';
                $inputs .= '</div>';

                $categories->forget($key);

                $inputs .= $this->showCategories($product, $categories, $category['id'], $char . $category['name'] . ' > ');
            }
        }
        return $inputs;
    }

    public function showCategoriesInSelectOption($categoryChosen, $categories, $parent_id = 0, $char = '')
    {
        $options = '';
        foreach ($categories as $key => $category) {
            if ($category['parent_id'] == $parent_id) {
                if ($category->id == $categoryChosen) {
                    $options .= '<option value="' . $category['id'] . '" selected>';
                } else {
                    $options .= '<option value="' . $category['id'] . '">';
                }
                $options .= $char . $category['name'];
                $options .= '</option>';
                $categories->forget($key);

                $options .= $this->showCategoriesInSelectOption($categoryChosen, $categories, $category['id'], $char . '|---');
            }
        }
        return $options;
    }

    public function updateStatusProduct(Request $request, $productId)
    {
        $isChecked = (boolean)$request->input('isChecked');

        $status = $isChecked ? 1 : 0;

        $product = Product::findOrFail($productId);
        $product->status = $status;
        $product->save();
    }

    public function copyProduct(Request $request)
    {
        $selectedItems = $request->input('selectedItemsCopy');
        $arraySelectedItems = explode(',', $selectedItems);
        $products = Product::whereIn('id', $arraySelectedItems)->orderBy('updated_at', 'asc')->get();

        if ($products) {
            foreach ($products as $product) {
                $newProduct = new Product();
                $newProduct->manufacturer_id = $product->manufacturer_id;
                $newProduct->name = $product->name;
                $newProduct->shortDesc = $product->shortDesc;
                $newProduct->detailDesc = $product->detailDesc;
                $newProduct->price = $product->price;
                $newProduct->quantity = $product->quantity;
                $newProduct->link_video = $product->link_video;
                $newProduct->status = false;

                $newProduct->save();
                foreach ($product->categories as $category) {
                    $newProduct->categories()->attach($category->id);
                }

                $image = Image::where('product_id', $product->id)->first();
                if ($image) {
                    $newImage = new Image();
                    $newImage->product_id = $newProduct->id;
                    $newImage->path = $image->path;

                    $newImage->save();
                }
            }

            return redirect()->route('products.index')->with('success', 'Product copied successfully');
        }

        return redirect()->route('products.index')->with('error', 'Product not found');
    }

    public function deleteProduct(Request $request)
    {
        $selectedItems = $request->input('selectedItemsDelete');
        $arraySelectedItems = explode(',', $selectedItems);
        $products = Product::whereIn('id', $arraySelectedItems)->get();

        if ($products) {
            foreach ($products as $product) {
                $image = Image::where('product_id', $product->id)->first();
                if ($image) {
                    $image->delete();
                }

                $product->delete();
            }
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        }
        return redirect()->route('products.index')->with('error', 'Product not found');
    }
}
