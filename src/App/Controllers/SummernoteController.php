<?php

namespace KillerQuote\App\Controllers;


use Illuminate\Http\Request;

class SummernoteController extends Controller
{
    public function uploadImage(Request $request)
    {
        $dir = "public/summernote";

        $path = $request->file('image')->store($dir);
        $name = str_replace("$dir/", '', $path);
        $response = [
            'message' => 'ok',
            'response' => asset('storage/summernote/' . $name)
        ];

        return response()->json($response);
    }
}
