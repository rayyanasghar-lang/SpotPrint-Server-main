<?php
namespace App\Http\Controllers;

use App\Helpers\JsonHelper;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use DataTableTrait;

    private $validationRules = [
        'name'     => 'required',
        'email'    => 'required|email|max:100|unique:users,email',
        'password' => 'required|string',
        'role'     => 'required|string',
        'phone'    => 'required|string|max:15',
        'address'  => 'nullable|string',
    ];

    public function index(Request $request)
    {
        $filters = json_decode(request('filters')) ?? [];
        $query   = User::withoutRole('Root')->where('id', '!=', auth()->user()->id);

        // Check if role filter exists and apply it
        if (!empty($filters->role)) {
            $query->whereHas('roles', function ($query) use ($filters) {
                $query->where('name', $filters->role);
            });
            unset($filters->role);
        }

        // Check if status filter exists and apply it
        if (!empty($filters->status)) {
            $query->where('status', $filters->status);
            unset($filters->status);
        }

        $searchColumns = ['name', 'email', 'phone'];
        $users = $this->dataTable($query, $searchColumns, (array) $filters);

        return $this->successResponse($users, 'List of Users retrieved successfully', 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        return $this->successResponse($user, 'User retrieved successfully', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        $role = Role::where('name', $validated['role'])->first();
        if (! $role) {
            return $this->errorResponse(['role' => 'Provided Role is not available'], 'Provided Role is not available', 422);
        }

        if (is_string($validated['name'])) {
            $validated['name'] = json_decode($validated['name'], true);
        }

        $validated['profile'] = [
            'language'     => 'en',
            'image'        => '',
            'fcm_token'    => '',
            'timezone'     => 'Europe/London',
            'email_notify' => '1',
            'sms_notify'   => '1',
            'address'      => $validated['address'] ?? '',
        ];

        // Create the user
        $user = User::create($validated);
        $user->assignRole($request->input('role'));

        return $this->successResponse($user, 'User created successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (! $user) return $this->errorResponse(null, 'User not found', 404);

        $updatedRules = $this->validationRules;
        $updatedRules['email'] = 'required|email|max:100|unique:users,email,' . $id;
        $updatedRules['password'] = 'nullable|string|min:8';

        $validator = Validator::make($request->all(), $updatedRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();


        if (is_string($validated['name'])) $validated['name'] = json_decode($validated['name'], true);
        if (empty($validated['password'])) unset($validated['password']);


        $user->update($validated);
        $user->syncRoles($validated['role']);
        JsonHelper::update_json_index($user, 'profile', 'address', $validated['address'] ?? '');

        return $this->successResponse($user, 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        $user->delete();
        return $this->successResponse(null, 'User deleted successfully');
    }

    // Profile functions for user
    public function profile_update(Request $request)
    {
        $user_id = auth()->user()->id;
        $rules   = [
            'name'    => 'required',
            'email'   => 'required|email|max:100|unique:users,email,' . $user_id,
            'phone'   => 'required|string|max:15',
            'profile' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        }

        $validated = $validator->validated();

        $user = User::find($user_id);
        if (blank($user)) {
            return $this->errorResponse('', 'User does not exist', 401);
        }

        if ($user->update(attributes: $validated)) {
            // JsonHelper::update_json_index($user, column: column: 'profile', 'address', $validated['address']);
            return $this->successResponse($user, 'Successfully updated user!', 200);
        }
        return $this->errorResponse('', 'Provide proper details', 400);
    }

    public function update_password(Request $request, $uid = 0)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:8',
            'new_password'     => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        }

        $validated = $validator->validated();

        if ($uid == 0) {
            $uid = auth()->user()->id;
        }

        $user = User::find($uid);
        if (blank($user)) {
            return $this->errorResponse('', 'User does not exist', 401);
        }

        // Check if the current password matches the user's password
        if (! Hash::check($validated['current_password'], $user->password)) {
            return $this->errorResponse('', 'Current password does not match', 400);
        }

        $user->password = $validated['new_password'];
        $user->save();

        return $this->successResponse($user, 'Password updated successfully', 200);

    }

    public function updateStatus(Request $request, string $id)
    {
        $userData = User::find($id);
        $statusShaffling = [
            "Suspended" => "Active",
            "Active" => "Suspended",
            "inActive" => "Active"
        ];
        if ($userData) {
            $userData->status = $statusShaffling[$userData->status];
            $userData->save();
            return $this->successResponse($userData, "User status updated to $userData->status successfully.", 200);
        }
        return $this->successResponse(null, 'User not found.', 404);
    }

    /* private $validationRules = [
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|string|email|max:100',
        'phone' => 'required|string|max:20',
        'password' => 'required|string|min:8',
        'role' => 'required|string',
        'profile' => 'nullable',
    ];

    public function index()
    {
        $filters = json_decode(request('filters')) ?? [];
        $users = User::withoutRole('Root');

        $searchColumns = ['first_name', 'last_name', 'email', 'phone'];
        $usersData = $this->dataTable($users, $searchColumns, $filters);
        return $this->successResponse($usersData, 'Success', 200);
    }

    public function store(Request $request)
    {
        if ($request->input('role') === 'Driver'){
            $this->validationRules += [
                'vin' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'frontSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'backSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'rightSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'leftSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'vin_number' => 'required|numeric',
                'rate_per_min' => 'required|numeric',
            ];
        }else if($request->input('role') === 'Dispatcher'){
            $this->validationRules += [
                'emails' => 'required|string',
                'phones' => 'required|string',
            ];
        }

        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        $validated = $validator->validated();

        $row = User::role($validated['role'])->where('email', $validated['email'])->first();
        if ($row) return $this->errorResponse([$validated['email'] => 'Email already in use'], 'Not available', 422);

        $role = Role::where('name', $validated['role'])->first();
        if (!$role) return $this->errorResponse(['role' => 'Provided Role is not available'], 'Provided Role is not available', 400);


        $validated['profile'] = [
            'language' => 'en',
            'image' => '',
            'fcm_token' => '',
            'timezone' => 'Europe/London',
            'email_notify' => '1',
            'sms_notify' => '1',
        ];

        if ($request->input('role') === 'Driver') {
            $validated['profile']['vin_number'] = $validated['vin_number'];
            $validated['profile']['rate_per_min'] = $validated['rate_per_min'];

            $this->directoryPath = env('DIGITAL_OCEAN_BASEPATH').'drivers/';
            $allImages = ['vin', 'frontSide', 'backSide', 'rightSide', 'leftSide'];
            foreach ($allImages as $imageName){
                $image = $this->uploadImage($request, $imageName);
                if ($image == '500') {
                    return $this->errorResponse("The $imageName failed to upload.", "Upload Error", 500);
                } else {
                    $validated['profile'][$imageName] = $image;
                }
            }
        }else if ($request->input('role') === 'Dispatcher') {
            $validated['profile']['emails'] = $validated['emails'];
            $validated['profile']['phones'] = $validated['phones'];
        }

        $user = new User($validated);
        if ($user->save()) {
            if ($role) $user->assignRole($role);
            return $this->successResponse($user, 'Successfully created user!', 200);
        }
        return $this->errorResponse('', 'Provide proper details', 400);
    }

    public function show(String $id)
    {
        $record = User::where('id', $id)->first();
        if (!$record) return $this->errorResponse('', 'User not found', 404);

        return $this->successResponse($record, 'Success', 200);
    }

    public function update(Request $request, String $id)
    {
        if (!$request->input('password')) unset($this->validationRules['password']);
        if ($request->input('role') === 'Driver') {
            $this->validationRules += [
                'vin' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'frontSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'backSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'rightSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'leftSide' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'vin_number' => 'required|numeric',
                'rate_per_min' => 'required|numeric',
            ];
        } else if ($request->input('role') === 'Dispatcher') {
            $this->validationRules += [
                'emails' => 'required|string',
                'phones' => 'required|string',
            ];
        }

        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        $validated = $validator->validated();

        $user = User::find($id);
        if (!$user) return $this->errorResponse('', 'User not found', 404);


        $row = User::role($validated['role'])->where('email', $validated['email'])->first();
        if($row && $user->id != $row->id) return $this->errorResponse(['email' => 'Email already in use'], 'Email already in use', 400);



        $role = Role::where('name', $validated['role'])->first();
        if (!$role) return $this->errorResponse(['role' => 'Provided Role is not available'], 'Provided Role is not available', 400);

        if ($request->input('role') === 'Driver') {
            $validated['profile']['vin_number'] = $validated['vin_number'];
            $validated['profile']['rate_per_min'] = $validated['rate_per_min'];


            $this->directoryPath = env('DIGITAL_OCEAN_BASEPATH').'drivers/';
            $allImages = ['vin', 'frontSide', 'backSide', 'rightSide', 'leftSide'];
            foreach ($allImages as $imageName){
                $image = $this->uploadImage($request, $imageName);
                if ($image == '500') {
                    return $this->errorResponse("The $imageName failed to upload.", "Upload Error", 500);
                } else {
                    $validated['profile'][$imageName] = $image;
                }
            }
        }else if ($request->input('role') === 'Dispatcher') {
            $validated['profile']['emails'] = $validated['emails'];
            $validated['profile']['phones'] = $validated['phones'];
        }

        if ($user->update($validated)) {
            if ($role) $user->syncRoles($role);
            return $this->successResponse($user, 'Successfully updated user!', 200);
        }
        return $this->errorResponse('', 'Provide proper details', 400);
    }


    public function update_notificatios(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sms_notify' => 'nullable',
            'email_notify' => 'nullable',
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        $validated = $validator->validated();


        $user = User::find($id);
        if(blank($user)) return $this->errorResponse('', 'User does not exist', 401);

        JsonHelper::update_json_index($user, 'profile', 'sms_notify', $validated['sms_notify']);
        JsonHelper::update_json_index($user, 'profile', 'email_notify', $validated['email_notify']);

        return $this->successResponse($user,  'Notifications updated successfully', 200);
    }




    public function getUserStatus()
    {
        // Get the enum column definition
        $enumColumn = 'status';
        $enumValues = DB::select("SHOW COLUMNS FROM users WHERE Field = '$enumColumn'")[0]->Type;

        // Extract the enum values
        preg_match('/^enum\((.*)\)$/', $enumValues, $matches);
        $status = [];
        if (isset($matches[1])) {
            $values = explode(',', $matches[1]);
            $status = str_replace("'", '', $values);
        }
        return $status;
    }

    public function updateStatus(Request $request, String $id)
    {
        $user = User::find($id);
        $statusShaffling = [
            "Suspended" => "Active",
            "Active" => "Suspended",
            "inActive" => "Active"
        ];
        if ($user) {
            $user->status = $statusShaffling[$user->status];
            $user->save();
            return $this->successResponse(null, "User status updated to $user->status successfully.", 200);
        }
        return $this->successResponse(null, 'User not found.', 404);
    }



    public function getCustomers(){
        $data = User::role('Customer')->get();
        return $this->successResponse([
            "message" => "Customers get successfully",
            "data" => $data
        ], 'success');
    }


    public function getDrivers(){
        $data = User::role('Driver')->get();
        return $this->successResponse([
            "message" => "Drivers get successfully",
            "data" => $data
        ], 'success');
    }


    public function updateProfileImage(Request $request, $id){

        $userData = User::find($id);
        if (!$userData) return $this->errorResponse('', 'User not found', 404);

        $rules = [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        }
        $validated = $validator->validated();

        $this->directoryPath = env('DIGITAL_OCEAN_BASEPATH').'profile_images/';
        $image = $this->uploadImage($request, 'profile_image', $id);
        if ($image == '500') {
            return $this->errorResponse("The profile image failed to upload.", "Upload Error", 500);
        } else {
            $validated['profile']['profile_image'] = $image;
        }

        if ($userData->update($validated)) {
            return $this->successResponse($userData, 'Profile image has been changed!', 200);
        }
        return $this->errorResponse('', 'Provide proper details', 400);

    } */
}
