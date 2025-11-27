<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Requests\Admin\CreateCmsPageRequest;
use App\Http\Requests\Admin\UpdateCmsPageRequest;
use App\Models\CmsPage;

class CmsPageController extends Controller
{
  /**
   * Display a listing of the resource.
  */
  public function index(Request $request)
  {
    $data = CmsPage::orderBy('id', 'desc')->get();
    return view('admin.cms-page.index', compact('data'));
  }

  /**
   * Show the form for creating a new resource.
  */
  public function create()
  {
    return view('admin/cms-page.create');
  }

  /**
  * Store a newly created resource in storage.
  */
  public function store(CreateCmsPageRequest $request)
  {

    $input = $request->all();
    if ($request->hasFile('image') && $request->file('image')->isValid()) {

      $fileName = uploadFile($request->file('image'), 'uploads/cms-page', '');
      
      if ($fileName) {
        $input['image'] = $fileName;
      } else {
        return redirect()->back()->with('error', 'Failed to upload file.');
      }
    } 

    $slug = $this->slugify($request->input('title'));

        $input['slug'] = $slug;
  
    CmsPage::create($input);

    flashMessage('success','Cms Page created successfully!');
    return redirect('admin/cms-page');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $data = CmsPage::where(['id' => base64_decode($id) ])->first();
    return view('admin.cms-page.view', compact('data'));
  }

  /**
  * Show the form for editing the specified resource.
  */
  public function edit($id)
  {

    $data = CmsPage::where(['id' => base64_decode($id) ])->first();
		return view('admin.cms-page.edit', compact('data'));

  }

  /**
   * Update the specified resource in storage.
  */
  public function update(UpdateCmsPageRequest $request, $id)
  {
 
    $record = CmsPage::findOrFail(base64_decode($id));
    $input = $request->all();
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      
      $fileName = uploadFile($request->file('image'), 'uploads/cms-page', '');
      
      if ($fileName) {
        $input['image'] = $fileName;
      } else {
        return redirect()->back()->with('error', 'Failed to upload file.');
      }
    }
    $record->fill($input)->save();
    flashMessage('success','Cms Page updated successfully!');
    return redirect()->to("admin/cms-page");
  }

  /**
  * Remove the specified resource from storage.
  */
  public function destroy($id)
  {
    $id = base64_decode($id);
    $data = CmsPage::find($id);
    if ($data->image) {
      @unlink(public_path() . '/uploads/cms-page/' . $data->image);
    }
    
    if($data->delete()){
      flashMessage('success','Cms Page deleted successfully!');
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

    $emailTemplate = CmsPage::find($id);
    
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


  static public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    // check slug exist in database
    $slugData = Cmspage::where('slug',$text)->first();

    if($slugData) {
         $text =  $text.'-'.rand(10,100);
      }
    return $text;
  }


}
