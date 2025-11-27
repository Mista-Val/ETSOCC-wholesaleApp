<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Http\Requests\Admin\CreateTestimonialRequest;
use Image;



class TestimonialController extends Controller
{
  /**
   * Display a listing of the resource.
  */
  public function index(Request $request)
  {
    $data = Testimonial::orderBy('id', 'desc')->get();
    return view('admin.testimonial.index', compact('data'));
  }

  /**
   * Show the form for creating a new resource.
  */
  public function create()
  {
    return view('admin/testimonial.create');
  }

  /**
  * Store a newly created resource in storage.
  */
  public function store(CreateTestimonialRequest $request)
  {



    $input = $request->all();
    if ($request->hasFile('file') && $request->file('file')->isValid()) {

      $fileName = uploadFile($request->file('file'), 'uploads/testimonial', '');
      
      if ($fileName) {
        $input['profile_image'] = $fileName;
      } else {
        return redirect()->back()->with('error', 'Failed to upload file.');
      }

    } 
  
    Testimonial::create($input);

    flashMessage('success','Testimonial created successfully!');
    return redirect('admin/testimonials');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $data = Testimonial::where(['id' => base64_decode($id) ])->first();
    return view('admin.testimonial.view', compact('data'));
  }

  /**
  * Show the form for editing the specified resource.
  */
  public function edit($id)
  {

    $data = Testimonial::where(['id' => base64_decode($id) ])->first();
		return view('admin.testimonial.edit', compact('data'));

  }

  /**
   * Update the specified resource in storage.
  */
  public function update(CreateTestimonialRequest $request, $id)
  {
 
    $record = Testimonial::findOrFail(base64_decode($id));
    $input = $request->all();
    if ($request->hasFile('file') && $request->file('file')->isValid()) {
      
      $fileName = uploadFile($request->file('file'), 'uploads/testimonial', '');
      
      if ($fileName) {
        $input['profile_image'] = $fileName;
      } else {
        return redirect()->back()->with('error', 'Failed to upload file.');
      }

    }
    $record->fill($input)->save();
    flashMessage('success','Testimonial updated successfully!');
    return redirect()->to("admin/testimonials");
  }

  /**
  * Remove the specified resource from storage.
  */
  public function destroy($id)
  {
    $id = base64_decode($id);
    $data = Testimonial::find($id);
    if ($data->profile_image) {
      @unlink(public_path() . '/uploads/testimonial/' . $data->profile_image);
    }
    
    if($data->delete()){
      flashMessage('success','Testimonial deleted successfully!');
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

    $emailTemplate = Testimonial::find($id);
    
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
