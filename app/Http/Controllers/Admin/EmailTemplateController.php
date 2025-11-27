<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateEmailTemplateRequest;
use App\Http\Requests\Admin\UpdateEmailTemplateRequest;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = EmailTemplate::get();
        return view('admin.email-template.index',compact(['data']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.email-template.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEmailTemplateRequest $request)
    {
       
        $input = $request->all();
        $input['slug'] = $this->slugify($request->input('title'));
        EmailTemplate::create($input);
        flashMessage('success','Email Template created successfully!');
        return redirect('admin/email-templates');
    }

 

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = EmailTemplate::where(['id' => base64_decode($id) ])->first();
		return view('admin.email-template.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = EmailTemplate::where(['id' => base64_decode($id) ])->first();
		return view('admin.email-template.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmailTemplateRequest $request, string $id)
    {
        $input = $request->all();
        $emailTemplate = EmailTemplate::findOrFail(base64_decode($id));
 
        if($emailTemplate){
            $emailTemplate->fill($input)->save();
            flashMessage('success','Email Template updated successfully!');
        }else{
            flashMessage('error','Internal server error. Please try again!');
        }
        
        return redirect()->to("admin/email-templates");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = base64_decode($id);
		$delete = EmailTemplate::where('id',$id)->delete();
		if($delete){
            flashMessage('success','Email Template deleted successfully!');
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
		$slugData = EmailTemplate::where('slug',$text)->first();
	    if($slugData) {
			$text = 	$text.'-'.rand(10,100);
		}
		return $text;
	}

    /**
     * Change Status
    */
    public function changeStatus(string $id){

        $id = base64_decode($id);

        $emailTemplate = EmailTemplate::find($id);
        

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
