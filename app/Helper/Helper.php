<?php

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
use App\Events\StockDispatched;


function flashMessage($type,$message)
{
    return toastr()->$type($message,'',['closeButton' => true]);
}


function uploadFile($image, $dir, $existsImage = '')
{
    if ($existsImage && stripos($existsImage, 'uploads') === true) {
        $existsFile = $existsImage;
        $baseUrl = asset('');
        $publicPath = public_path('/');
        $url = str_replace($baseUrl, $publicPath, $existsFile);
        if (file_exists($url)) {
            unlink($url);
        }
    }
    $name = time() . rand(1, 99) . '.' . $image->getClientOriginalExtension();
    $image->storeAs($dir, $name, $disk = 'local');
    return $name;
}

function globalSetting($slug)
{ 
    $global =  GlobalSetting::where('slug',$slug)->first();
    return $global->value;
}
function sendEmail($body,$subject,$to){
   
    Mail::html($body, function ($message) use ($to, $subject) {
    $message->to($to)->subject($subject);
    });

}
        function attachEmailTemplate($slug,$values){
        $template = EmailTemplate::where('slug', $slug)->first();
        $options = explode(',', $template->options);
        $subject = $template->subject;
        $body = $template->content;
        // echo "<pre>";print_r($template);
        // print_r($values);

        foreach($options as $option) {
        // echo "<pre>";
        // print_r($option);
        $body = str_replace('{{' . $option . '}}', $values[$option], $body);
        }

        return ["body" => $body, "subject"=>$subject];

        }
    function sendNotification($stockId, $warehouseId, $message = null) {
        event(new StockDispatched($stockId, $warehouseId));
    }

    
function PermissionCheck($permission, $guard = null)
{ 
    $user = auth()->guard($guard)->user();
        if (!$user || !$user->can($permission)) {
            abort(403, 'Unauthorized');
        }
}

       