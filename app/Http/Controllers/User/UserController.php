<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Stats\UserStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect;


class UserController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Tabs/Users/Index', [
            'filters' => $request->all('search', 'role'),
            'sorts' => ["sortBy"=>$request->sortBy??"created_at","sortType"=>$request->sortType??"desc"],
            'paginate' => User::orderByAll($request->sortBy,$request->sortType)
                                ->filter($request->only('search','role'))
                                ->paginate($request->per_page?$request->per_page:10)
                                ->withQueryString()
                                ->through(fn ($user) => [
                                    'id' => $user->id,
                                    'image' => $user->image,
                                    'full_name' => $user->full_name,
                                    'email' => $user->email,
                                    'role' => $user->roles[0]->name,
                                    'admin' => $user->admin
                                ]),
        ]);        
    }

    public function create(){
        // sleep(2);
        return Inertia::render('Tabs/Users/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'email' => 'required|max:50|email|unique:users',
            'password' => 'required|string|confirmed|min:8|max:30',
            'image' => 'nullable|image',
        ]);

        $url=null;
        if($request->file('image')){
            $image = $request->file('image');
            $stored_image = $image->store('/public/users');
            $url = Storage::url($stored_image); 
            $url = env('APP_URL').$url;       
        }

        $user= User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
                'image' => $url,
            ]);
        $user->assignRole('admin');
        UserStats::increase(1, $user->created_at);

        return to_route('users.index')->with('success', 'user_created');
    }

    public function edit(User $user)
    {
        return Inertia::render('Tabs/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'image' => $user->image 
                // 'image' => $user->image ? URL::route('image', ['path' => $user->image, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
            ],
        ]);
    }

    public function update(Request $request,User $user)
    {
        $request->validate([
            'first_name' => ['required', 'max:30'],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable','confirmed','min:8','max:30'],
            'image' => ['nullable', 'image'],
        ]);

        $user->update($request->only('first_name', 'last_name', 'email'));

        $url=null;
        if($request->file('image')){
            $userImage = str_replace(env('APP_URL').'/storage', '', $user->image);
            if(Storage::exists('/public' . $userImage) && $userImage!='/admin.png'){
                Storage::delete('/public' . $userImage);
            }            
            $image = $request->file('image');
            $stored_image = $image->store('/public/users');
            $url = Storage::url($stored_image);  
            $url = env('APP_URL').$url;
            $user->update(['image' => $url]);      
        }
        if ($request->password) {
            $user->update(['password' => $request->password]);
        }

        return to_route('users.index')->with('success', 'user_updated');
    }    

    public function destroy($id)
    {
        $user=User::find($id);
        $user->delete();
        return to_route('users.index')->with('success', 'user_deleted');
    } 
}
