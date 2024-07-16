<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpdateModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
class ChatController extends Controller
{
    public function getData(Request $request)
    {
        // console.info(['here']);
        $phonenumber = file_get_contents('php://input');
        $decode_phonenumber = json_decode($phonenumber, true);
        $phonenumber_decode = $decode_phonenumber['phonenumber'];
        // $data = "aaa";        
        $data = UpdateModel::where('phonnumber', $phonenumber_decode)->get();
        return response()->json($data); // Return the fetched data as JSON response
    }

    public function getPhoneNumbers()
    {
        //  console.info(['here']);
        $phone_number = UpdateModel::select('phonnumber')->distinct()->get();
        // $data = UpdateModel::where('phonnumber', $phone_number)->get();
        return response()->json($phone_number); // Return the fetched data as JSON response
    }

    public function getDownload(Request $request)
    {   
        $filename = $request->input('filename');
        // PDF file is stored under project/public/download/info.pdf
        // return response()->download($file, $filename);

        // $headers = [
        //     'Content-Type' => 'image/jpg',
        //  ];
        
        //  return response()->json($filename);
        // return response()->download($file, $filename, $headers);
        return response()->json(Storage::exists("uploads/".$filename));
        // return Storage::download("uploads/".$filename);
    }
}
