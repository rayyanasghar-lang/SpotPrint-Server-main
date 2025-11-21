<?php
namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'Root')->get();
        return $this->successResponse($roles, 'Roles retrieved successfully');

    }
}
