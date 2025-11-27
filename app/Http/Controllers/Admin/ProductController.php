<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;


class ProductController extends Controller
{
    public function index()
    {
        // $products = Product::latest()->paginate(10);
        return view('admin.products.index');
    }

    public function create()
    {
        return view('admin.products.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'  => 'required|string|max:255',
    //         'sku'   => 'required|string|max:255|unique:products',
    //         'image' => 'nullable|image|mimes:jpeg,jpg,png,svg',
    //         'stock' => 'required|integer',
    //         'status'=> 'required|boolean'
    //     ]);

    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('products', 'public');
    //     }

    //     Product::create([
    //         'name' => ucfirst($request->name),
    //         'sku' => $request->sku,
    //         'image' => $imagePath,
    //         'stock' => $request->stock,
    //         'status' => $request->status,
    //         'min_price' => $request->min_price,
    //         'max_price' => $request->max_price,
    //         'category' => $request->category,
    //         'unit' => $request->unit,
    //         'destination' => $request->destination,
    //         'remarks' => $request->remarks,  // Optional, can be null
    //     ]);


    //     return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    // }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'      => 'required|string|max:255',
    //         'sku'       => 'required|string|max:255|unique:products,sku',
    //         'image'     => 'required|image|mimes:jpeg,jpg,png,svg',
    //         'status'    => 'required|boolean',
    //         'min_price' => ['required', 'integer', 'min:0'],
    //         'max_price' => ['required', 'integer', 'gte:min_price', 'min:0'],
    //         'category'  => 'required|in:category1,category2,category3,category4',
    //         'remarks'   => 'nullable|string|max:500',
    //         'product_package' => 'required'
    //     ]);



    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('products', 'public');
    //     }

    //     Product::create([
    //         'name'        => ucfirst($request->name),
    //         'sku'         => $request->sku,
    //         'image'       => $imagePath,
    //         'stock'       => $request->stock,
    //         'status'      => $request->status,
    //         'min_price'   => $request->min_price,
    //         'max_price'   => $request->max_price,
    //         'category'    => $request->category,
    //         'unit'        => $request->unit,
    //         // 'destination' => $request->destination,
    //         'remarks'     => $request->remarks,
    //         'product_package'=> $request->product_package
    //     ]);

    //     return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    // }



    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'sku'              => 'required|string|max:255|unique:products,sku',
            'image'            => 'required|image|mimes:jpeg,jpg,png,svg',
            'status'           => 'required|boolean',
            'min_price'        => ['required', 'integer', 'min:0'],
            'max_price'        => ['required', 'integer', 'gte:min_price', 'min:0'],
            'category'         => 'required|in:category1,category2,category3,category4',
            'remarks'          => 'nullable|string|max:500',
            'product_package'  => 'required|in:pouches,jar,trays,packs,boxes',
            'package_quantity' => 'required|integer|min:1|digits_between:1,6'
        ], [
            'package_quantity.integer' => 'The package quantity must be a valid number.',
            'package_quantity.min' => 'The package quantity must be at least 1.',
            'package_quantity.digits_between' => 'The package quantity cannot be more than 6 digits.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name'             => ucfirst($request->name),
            'sku'              => $request->sku,
            'image'            => $imagePath,
            'stock'            => $request->stock,
            'status'           => $request->status,
            'min_price'        => $request->min_price,
            'max_price'        => $request->max_price,
            'category'         => $request->category,
            'unit'             => $request->unit,
            'remarks'          => $request->remarks,
            'product_package'  => $request->product_package,
            'package_quantity' => $request->package_quantity
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    // public function update(Request $request, $id)
    // {
    //     $product = Product::findOrFail($id);

    //     $request->validate([
    //         'name'        => 'required|string|max:255',
    //         'sku'         => 'required|string|max:255|unique:products,sku,' . $product->id,
    //         'image'       => 'nullable|image|mimes:jpeg,jpg,png,svg',
    //         // 'stock'       => 'required|integer|min:0',
    //         'status'      => 'required|boolean',
    //         'min_price'   => 'required|numeric|min:0',
    //         'max_price'   => 'required|numeric|gte:min_price',
    //         'category'    => 'required|in:category1,category2,category3,category4',
    //         // 'unit'        => 'required|string|max:50',
    //         // 'destination' => 'required|in:warehouse,outlet',
    //         'remarks'     => 'nullable|string|max:500',
    //         'product_package'     => 'required',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         if ($product->image && Storage::disk('public')->exists($product->image)) {
    //             Storage::disk('public')->delete($product->image);
    //         }

    //         $product->image = $request->file('image')->store('products', 'public');
    //     }

    //     $product->name        = ucfirst($request->name);
    //     $product->sku         = $request->sku;
    //     // $product->stock       = $request->stock;
    //     $product->status      = $request->status;
    //     $product->min_price   = $request->min_price;
    //     $product->max_price   = $request->max_price;
    //     $product->category    = $request->category;
    //     // $product->unit        = $request->unit;
    //     // $product->destination = $request->destination;
    //     $product->remarks     = $request->remarks;
    //     $product->product_package = $request->product_package;

    //     $product->save();


    //     return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    // }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'sku'              => 'required|string|max:255|unique:products,sku,' . $product->id,
            'image'            => 'nullable|image|mimes:jpeg,jpg,png,svg',
            'status'           => 'required|boolean',
            'min_price'        => ['required', 'numeric', 'min:0'],
            'max_price'        => ['required', 'numeric', 'gte:min_price', 'min:0'],
            'category'         => 'required|in:category1,category2,category3,category4',
            'remarks'          => 'nullable|string|max:500',
            'product_package'  => 'required|in:pouches,jar,trays,packs,boxes',
            'package_quantity' => 'required|integer|min:1|digits_between:1,6'
        ], [
            'package_quantity.integer' => 'The package quantity must be a valid number.',
            'package_quantity.min' => 'The package quantity must be at least 1.',
            'package_quantity.digits_between' => 'The package quantity cannot be more than 6 digits.',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->name             = ucfirst($request->name);
        $product->sku              = $request->sku;
        $product->status           = $request->status;
        $product->min_price        = $request->min_price;
        $product->max_price        = $request->max_price;
        $product->category         = $request->category;
        $product->remarks          = $request->remarks;
        $product->product_package  = $request->product_package;
        $product->package_quantity = $request->package_quantity;

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    //bulk upload
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        Excel::import(new ProductImport, $file);

        return redirect()->route('admin.products.index')->with('success', 'Products imported successfully!');
    }
}
