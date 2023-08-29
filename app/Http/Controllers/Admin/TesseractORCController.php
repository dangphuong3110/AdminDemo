<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class TesseractORCController extends Controller
{
    public function index()
    {
        $result = [''];
        return view('admin.option.scan-img', compact('result'));
    }

    public function processImage(Request $request)
    {
        $image = $request->file('img');
        if ($image) {
            $extension = $image->getClientOriginalExtension();

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowedExtensions)) {
                $imageName = 'test.jpg';
                $image->move(public_path('assets/image/product'), $imageName);
                $imagePath = public_path('assets/image/product/' . $imageName);
                try {
                    $text = new TesseractOCR();
                    $text->image($imagePath);
                    $text->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
                    $text->lang('vie');
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
