<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    //
    public function loadModalContent(Request $request)
    {
        $filePath = $request->input('filePath', ''); // Get the file path from the request

        // Return the modal content view with the provided file path
        return view('modal-content', ['filePath' => $filePath])->render();
    }
}
