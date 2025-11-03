<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AplikasiController extends Controller
{
    public function index()
    {
        $releasePath = public_path('release');
        $files = [];

        if (File::exists($releasePath) && File::isDirectory($releasePath)) {
            $fileList = File::files($releasePath);

            foreach ($fileList as $file) {
                $files[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'path' => asset('release/' . $file->getFilename()),
                ];
            }

            // Sort by modified time (newest first)
            usort($files, function($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }

        return view('aplikasi.index', compact('files'));
    }
}
