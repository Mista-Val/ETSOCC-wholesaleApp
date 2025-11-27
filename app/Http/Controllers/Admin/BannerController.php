<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Requests\Admin\CreateBannerRequest;
use Image;



class BannerController extends Controller
{
  /**
   * Display a listing of the resource.
  */
  public function index(Request $request)
  {
    $data = Banner::orderBy('id', 'desc')->get();
    return view('admin.banner.index', compact('data'));
  }

  /**
   * Show the form for creating a new resource.
  */
  public function create()
  {
    return view('admin/banner.create');
  }

  /**
  * Store a newly created resource in storage.
  */
  public function store(CreateBannerRequest $request)
  {



    $input = $request->all();
    if ($request->hasFile('file') && $request->file('file')->isValid()) {

      $fileName = uploadFile($request->file('file'), 'uploads/banner', '');
      
      if ($fileName) {
        $input['banner_image'] = $fileName;
      } else {
        return redirect()->back()->with('error', 'Failed to upload file.');
      }

    } 
  
    Banner::create($input);

    flashMessage('success','Banner created successfully!');
    return redirect('admin/banners');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $data = Banner::where(['id' => base64_decode($id) ])->first();
    return view('admin.banner.view', compact('data'));
  }

  /**
  * Show the form for editing the specified resource.
  */
  public function edit($id)
  {

    $data = Banner::where(['id' => base64_decode($id) ])->first();
		return view('admin.banner.edit', compact('data'));

  }

  /**
   * Update the specified resource in storage.
  */
  public function update(CreateBannerRequest $request, $id)
  {
 
    $record = Banner::findOrFail(base64_decode($id));
    $input = $request->all();
    if ($request->hasFile('file') && $request->file('file')->isValid()) {
      
      $fileName = uploadFile($request->file('file'), 'uploads/banner', '');
      
      if ($fileName) {
        $input['banner_image'] = $fileName;
      } else {
        return redirect()->back()->with('error', 'Failed to upload file.');
      }

    }
    $record->fill($input)->save();
    flashMessage('success','Banner updated successfully!');
    return redirect()->to("admin/banners");
  }

  /**
  * Remove the specified resource from storage.
  */
  public function destroy($id)
  {
    $id = base64_decode($id);
    $data = Banner::find($id);
    if ($data->banner_image) {
      @unlink(public_path() . '/uploads/banner/' . $data->banner_image);
    }
    
    if($data->delete()){
      flashMessage('success','Banner deleted successfully!');
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

    $emailTemplate = Banner::find($id);
    
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
