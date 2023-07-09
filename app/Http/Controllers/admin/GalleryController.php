<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Gallery;
use App\Salon;
use DB;

class GalleryController extends Controller
{
    public function index()
    {
        $salon = Salon::where('owner_id', Auth()->user()->id)->first();
        if(isset($salon->salon_id))
        {
            $gallery = Gallery::where('salon_id', $salon->salon_id)->orderBy('gallery_id', 'DESC')
            ->paginate(10);
        }
        else
        {
            $gallery = Gallery::paginate(10);
        }
        return view('admin.pages.gallery', compact('gallery'));
    }

    public function create()
    {
        return view('admin/gallery/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'bail|required',
        ]);

        $gallery = new Gallery();
        $salon = Salon::where('owner_id', Auth()->user()->id)->first();

        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $name = 'Gallery_'.uniqid().'.'. $image->getClientOriginalExtension();
            $destinationPath = public_path('/storage/images/gallery');
            $image->move($destinationPath, $name);
            $gallery->image = $name;
        }
        $gallery->salon_id = $salon->salon_id;

        $gallery->save();
        return redirect('/admin/gallery');
    }
    
    public function show($id)
    {
        $data['gallery'] = Gallery::find($id);
        return response()->json(['success' => true,'data' => $data, 'msg' => 'Gallery show'], 200);
    }

    public function destroy($id)
    {
        $gallery = Gallery::find($id);
        \File::delete(public_path('/storage/images/gallery/'. $gallery->image));
        $gallery->delete();
        return redirect('/admin/gallery');
    }
    
    public function hideGallery(Request $request)
    {
        $gallery = Gallery::find($request->galleryId);
        if ($gallery->status == 0) 
        {   
            $gallery->status = 1;
            $gallery->save();
        }
        else if($gallery->status == 1)
        {
            $gallery->status = 0;
            $gallery->save();
        }
    }
}