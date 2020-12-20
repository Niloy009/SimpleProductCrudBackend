<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('JWT', ['except' => ['index', 'show']]);
    }

    /**
     *
     * Display all products
     *
     * @return Product[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Product::with('users')->get();
    }


    /**
     * Store a newly created product in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required',
//            'image_url' => 'required|image|max:1024'
            ]
//            ['image_url.required' => 'Image is required']
        );

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['created_by'] = auth()->user()->id;

        //make the slug unique
        if (Product::whereSlug($data['slug'])->withTrashed()->exists()) {
            $lastProductId = Product::latest()->first()->id;
            $data['slug'] = "{$data['slug']}-{$lastProductId}";
        }
        if ($request->hasFile('image_url')) {
            $image = $request->file('image_url');
            $name = time() . '.' . $image->getClientOriginalExtension(); //getting the extension
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $data['image_url'] = $name;
        }
        $product = Product::create($data);

        return response()->json(['message' => ' Product Created Successfully', 'Product' => $product], 201);

    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return Product
     */
    public function show(Product $product)
    {
        return $product;
    }


    /**
     * Update the specified product in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Product $product)
    {
        if ($product->created_by == auth()->user()->id) {

            $this->validate($request, [
                    'title' => 'string|max:255',
                    'description' => 'string',
//            'image_url' => 'required|image|max:1024'
                ]
//            ['image_url.required' => 'Image is required']
            );

            $data = $request->all();
            $data['slug'] = Str::slug($request->title);
            $data['created_by'] = auth()->user()->id;

            //make the slug unique
            if (Product::whereSlug($data['slug'])->withTrashed()->exists()) {
                $lastProductId = Product::latest()->first()->id;
                $data['slug'] = "{$data['slug']}-{$lastProductId}";
            }

            if ($request->has('image_url')) {

                $file_path = public_path('/images/' . $product->image_url);
                if (file_exists($file_path) && !empty($product->image_url)) {
                    unlink($file_path);
                }

                $file = $request->file('image_url');
                $extension = $file->getClientOriginalExtension(); //getting file extension
                $filename = time() . '.' . $extension;
                $file->move('images/', $filename);
                $data['image_url'] = $filename;
            }


            $product->update($data);

            return response()->json(['message' => 'Product Updated Successfully', 'updated Product' => $product]);
        } else {
            return response()->json(['message' => 'This is not your product', 'code' => '403'], 403);
        }
    }

    /**
     * Remove the specified product from storage
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        if ($product->created_by == auth()->user()->id) {
            $product->delete();
            return response()->json(['message' => 'Deleted Successfully', 'Deleted Product' => $product], 201);
        } else {
            return response()->json(['message' => 'This is not your product', 'code' => '403'], 403);
        }
    }
}
