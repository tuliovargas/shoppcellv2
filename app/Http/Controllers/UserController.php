<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use UploadTrait;
    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request)
    {
        $query = new User();

        $search = isset($request['search']) ? $request['search'] : '';
        $onlySellers = isset($request['onlySellers']) ? true : false;
        $onlyTechnicians = isset($request['onlyTechnicians']) ? true : false;
        $onlyCaixas = isset($request['onlyCaixas']) ? true : false;
        $withAdmin = isset($request['withAdmin']) ? true : false;

        $roles = [];

        if ($withAdmin) {
            $roles[] = 'admin';
        }
        if ($onlySellers) {
            $roles[] = 'vendedor';
        } elseif ($onlyTechnicians) {
            $roles[] = 'técnico';
        } elseif ($onlyCaixas) {
            $roles[] = 'caixa';
        }

        $query = $query->when(!empty($roles), function ($query) use ($roles) {
            $query = $query->whereHas('roles', function (Builder $query) use ($roles) {
                $query->whereIn('name', $roles);
            });
        })->get();

        if ($request->paginate === 'false') {
            return $query->all();
        }

        if ($request->expectsJson()) {
            return response()->json($query->all());
        }

        return view('users.index')->with('users', $query->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['password'] = bcrypt($data['password']);
        $data['is_active'] = isset($data['is_active']) && $data['is_active'] ? true : false;

        if ($request->hasFile('photo')) {
            $data['avatar'] = $this->uploadFile($data['photo'], 'public/user_photos');
        }

        $user = User::create($data);
        $user->assignRole($data['role']);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $userRole = $user->roles->pluck('name');

        $roles = Role::all();

        return view('users.edit', compact('user', 'userRole', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $user = User::findOrFail($id);

        $data['is_active'] = isset($data['is_active']) && $data['is_active'] ? true : false;

        if ($data['password'] && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {
            $oldAvatar = $user->avatar;

            $data['avatar'] = $this->uploadFile($data['photo'], 'public/user_photos');

            if($data['avatar'] && $oldAvatar){
                try{
                    unlink(storage_path('app/public/' . $oldAvatar));
                } catch(\Exception $e){
                    \Log::error($e);
                }
            }
        }

        $user->update($data);

        // alterando a role
        $role = $data['role'];
        $user->syncRoles([$role]);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     */
    public function delete($id)
    {

        $user = User::findOrFail($id);

        return view('users.delete', compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        return redirect()->route('users.index');
    }

    public function getUsersByRole($role){

    }

    public function current(){
        return response()->json(Auth::user());
    }

    public function userHasRole($role){
        $hasRole = Auth::user()->hasRole($role);
        return response()->json($hasRole);
    }

    
}
