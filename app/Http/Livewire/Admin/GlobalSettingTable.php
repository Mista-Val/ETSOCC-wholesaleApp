<?php

namespace App\Http\Livewire\Admin;

use App\Models\GlobalSetting;
use Livewire\Component;
use Livewire\WithFileUploads;


class GlobalSettingTable extends Component
{
    use WithFileUploads;

    public $slugName = '';
    public $tab = 1;
    
     protected $messages = [
        'adminEmail.required' => 'Admin receive email address is required',
        'adminEmail.email' => 'Please enter valid email address',
        'emailFrom.required' => 'Admin email is required',
        'emailFrom.email' => 'Please enter valid email address',
    ];

    public function mount()
    {
        $setting = GlobalSetting::orderBy('id','asc')->get();

        foreach ($setting as $key => $value) {
            $this->{$value->slug} = $value->value;
        }
    }

    public function render()
    {
        $setting = GlobalSetting::orderBy('id','asc')->get();
    
        return view('livewire.global-setting',['data' => $setting]);
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function updateSetting($slug,$title,$type)
    {
        
        $this->slugName = $slug;
        $this->validate($this->rules($slug));

        if($type === 'file'){
            $folderName = $slug === 'favicon'?'favicon':'logo';
            // Save original file
            $data = uploadFile($this->{$slug}, 'uploads/'.$folderName, '');
             $imageURL = '/uploads/' . $folderName . '/' . $data;
        $this->dispatchBrowserEvent('name-updated', ['url' => $imageURL,'slug'=>$slug]);
        }else{
            $data = $this->{$slug};
        }
        // if ($imageURL) {
        // $imageURL = '/uploads/'.$folderName.'/'.$data;
        // }
        GlobalSetting::where('slug',$slug)->update(['value'=> $data]);
        // $this->dispatchBrowserEvent('name-updated', ['url' => $imageURL]);
        flashMessage('success', $title. ' updated successfully!');
    }



    protected function rules($slug)
    {
        $validation = [
           
        ];

        if($slug === 'contactNo'){
            $validation[$slug] = ['required', 'regex:/^\+?[0-9\s\-()]*$/'];
        }elseif($slug === 'copyRightText'){
            $validation[$slug] = 'required|string|max:255';
        }elseif($slug === 'instagram' || $slug === 'twitter' || $slug === 'facebook'){
            $validation[$slug] = 'required|url';
        }elseif($slug === 'emailFrom' || $slug === 'adminEmail'){
            $validation[$slug] = 'required|email';
        }elseif($slug === 'favicon' || $slug === 'logo'){
            $validation[$slug] = 'required|mimes:jpeg,jpg,png|max:4096';
        }else{
            $validation[$slug] = 'required';
        }
        return $validation;
    }
}
