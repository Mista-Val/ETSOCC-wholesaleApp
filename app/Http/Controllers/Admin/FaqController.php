<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateFaqRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Faq::get();
        return view('admin.faq.index',compact(['data']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = Category::where('status',1)->pluck('title','id');
        return view('admin.faq.create',compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFaqRequest $request)
    {
        $input = $request->all();
        Faq::create($input);
        flashMessage('success','Faq created successfully!');
        return redirect('admin/faq');
    }

 

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Faq::where(['id' => base64_decode($id) ])->first();
		return view('admin.faq.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::where('status',1)->pluck('title','id');
        $data = Faq::where(['id' => base64_decode($id) ])->first();
		return view('admin.faq.edit', compact('data','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateFaqRequest $request, string $id)
    {
        $input = $request->all();
        $emailTemplate = Faq::findOrFail(base64_decode($id));
 
        if($emailTemplate){
            $emailTemplate->fill($input)->save();
            flashMessage('success','Faq updated successfully!');
        }else{
            flashMessage('error','Internal server error. Please try again!');
        }
        
        return redirect()->to("admin/faq");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = base64_decode($id);
		$delete = Faq::where('id',$id)->delete();
		if($delete){
            flashMessage('success','Faq deleted successfully!');
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

        $emailTemplate = Faq::find($id);
        
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
