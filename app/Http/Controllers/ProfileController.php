<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get profile of current user by id
     * 
     * @param Request $request
     * @param $id
     * 
     * @return view
     */
    public function getProfile(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        return (!$user) ? abort(404) : view('profile.profile', compact('user'));
    }

    /**
     * Render HTML web-page
     * 
     * @return view
     */
    public function StoreEdit()
    {
        return view('edit.profile-edit');
    }

    /**
     * Updates users profile
     * 
     * @param Request $request
     * @return redirect
     */
    public function postEdit(Request $request)
    {
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255','alpha'],
            'surname' => ['required', 'string', 'max:255','alpha'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name_specialty' => ['required', 'string', 'max:255','regex:/^([a-zа-яё]\s?)+$/iu'],
            'course' => ['required', 'integer'],
        ]);
        
        Auth::user()->update([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'), 
            'email' => $request->input('email'),
            'name_specialty' => $request->input('name_specialty'),
            'course' => $request->input('course'),
        ]);

        return redirect()->route('edit.profile-edit');
    }

    // ---------------------------------------------------------------------

    /**
     * Get profile of current user by id
     * 
     * @param Request $request
     * @param $id
     * 
     * @return view
     */
    public function getProfileAPI(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        return (!$user) ? response()->json(['error' => 'User doesn\'t exist'], 404) : response()->json(['message' => $user]);
    }

    /**
     * Render HTML web-page
     * 
     * @return view
     */
    // public function StoreEditAPI()
    // {
    //     return view('edit.profile-edit');
    // }

    /**
     * Updates users profile
     * 
     * @param Request $request
     * @return redirect
     */
    public function postEditAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['string', 'max:255','regex:/^[a-zа-я]+$/iu'],
            'surname' => ['string', 'max:255','regex:/^[a-zа-я]+$/iu'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'name_specialty' => ['string', 'max:255','regex:/^([a-zа-яё]\s?)+$/iu'],
            'course' => ['integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        
        $user = auth()->user();
        $user->fill($request->all())->save();

        return response()->json(['success' => auth()->user()]);
    }

}
