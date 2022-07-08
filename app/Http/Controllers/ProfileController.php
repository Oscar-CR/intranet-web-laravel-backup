<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Publications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;


class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $user = User::all()->where('id', $id);
        $publications = Publications::where('user_id', $id)->orderBy('created_at', 'desc')->get();

        return view('profile.index', compact('user', 'publications'));
    }


    public function change(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);

        if ($request->hasFile('image')) {

            File::delete(Auth::user()->image);
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = time() .  $filename . '.' . $extension;
            $path = 'storage/profile/200x300' . $fileNameToStore;

            $request->file('image')->move('storage/profile/', $fileNameToStore);
            Image::make(public_path("storage/profile/{$fileNameToStore}"))->fit(200, 300)->save(public_path("storage/profile/200x300{$fileNameToStore}"));
            Image::make(public_path("storage/profile/{$fileNameToStore}"))->fit(300, 300)->save(public_path("storage/profile/300x300{$fileNameToStore}"));
            File::delete(public_path("storage/profile/{$fileNameToStore}"));
        } else {
            return redirect()->action([ProfileController::class, 'index']);
        }

        $id = Auth::user()->id;

        DB::table('users')->where('id', $id)->update(['image' => $path]);
        return redirect()->action([ProfileController::class, 'index']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '.' . $extension;
            $path = $request->file('image')->move('storage/post/', $fileNameToStore);
        } else {
            $path = null;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(User $prof)
    {
        $publications = Publications::where('user_id', $prof->id)->orderBy('created_at', 'desc')->get();

        if (auth()->user()->id == $prof->id) {
            return redirect()->action([ProfileController::class, 'index']);
        }
        $user[0] = $prof;
        return view('profile.view', compact('user', 'publications'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
