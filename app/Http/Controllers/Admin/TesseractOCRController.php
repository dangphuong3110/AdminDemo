<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class TesseractOCRController extends Controller
{
    public function index()
    {
        $result = [''];
        return view('admin.option.scan-img', compact('result'));
    }

    public function processImage(Request $request)
    {
        $language = $request->input('language');
        $image = $request->file('img');
        if ($image) {
            $extension = $image->getClientOriginalExtension();

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowedExtensions)) {
                $imageName = 'test.jpg';
                try {
                    $image->move(public_path('assets/image/product'), $imageName);
                    $imagePath = public_path('assets/image/product/' . $imageName);
                    $text = new TesseractOCR($imagePath);
                    if ($language == 1) {
                        $text->lang('eng');
                    } else {
                        $text->lang('vie');
                    }
                    $result = explode("\n", $text->run());
                } catch (\Exception $exception) {
                    $result = [];
                    return view('admin.option.scan-img', compact('result'));
                }
                $oldImagePath = public_path('assets/image/product/test');
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                return view('admin.option.scan-img', compact('result'))->with('success', 'Image has been scanned successfully.');
            }
        }
        return redirect()->route('option.index')->with('failure', 'The uploaded file must be in the correct image format (jpg, jpeg, png, gif).');
    }
}
