<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::get();
        return view('admin.category.index',compact(['data']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request)
    {
        $input = $request->all();
        Category::create($input);
        flashMessage('success','Category created successfully!');
        return redirect('admin/categories');
    }

 

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Category::where(['id' => base64_decode($id) ])->first();
		return view('admin.category.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Category::where(['id' => base64_decode($id) ])->first();
		return view('admin.category.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $input = $request->all();
        $emailTemplate = Category::findOrFail(base64_decode($id));
 
        if($emailTemplate){
            $emailTemplate->fill($input)->save();
            flashMessage('success','Category updated successfully!');
        }else{
            flashMessage('error','Internal server error. Please try again!');
        }
        
        return redirect()->to("admin/categories");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = base64_decode($id);
		$delete = Category::where('id',$id)->delete();
		if($delete){
            flashMessage('success','Category deleted successfully!');
		}else{
            flashMessage('error','Internal server error. Please try again!');
		}
		return redirect()->back();
    }


    /**
     * Change Status
    */
    public function changeStatus(string $id){

        $id = base64_decode($id);

        $emailTemplate = Category::find($id);
        

        if($emailTemplate){
            $status = $emailTemplate->status == 1?false:true;
            $emailTemplate->status = $status;
            $emailTemplate->save();
            flashMessage('success','Status updated successfully!');
		}else{
            flashMessage('error','Internal server error. Please try again!');
		}
		return redirect()->back();


    }
}
