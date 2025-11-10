<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    //
    public function loadModalContent($filePath)
    {
        // $filePath = $request->input('f`ilePath', ''); // Get the file path from the request

        // Return the modal content view with the provided file path
        return view('modal-content', ['filePath' => $filePath])->render();
    }
}
