<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Media;

class VideoController extends Controller
{
    public function index()
    {
        $medias = Media::orderBy('id', 'DESC')->paginate(5);
        return view('admin.pages.video', compact('medias'));
    }

   
}