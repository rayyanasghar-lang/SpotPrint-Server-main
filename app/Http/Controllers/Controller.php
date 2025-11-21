<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function infoResponse($data = null, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'alert',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($data = null, $message = null, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function file_upload(Request $request, string $fileKey, string $path = 'orders')
    {
        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            $fileName = time() . '_' . rand(99, 1000) . '.' . $file->getClientOriginalExtension();

            /* if ($file->isValid()) {
            // Store directly in the `public` folder
            $filePath = $file->storeAs($path, $fileName, ['disk' => 'public']);
            // Return the public URL path
            return 'storage/' . $filePath;
            } */

            $path = 'uploads/' . $path;

            if ($file->isValid()) {
                // Directly save the file to the `public` folder without using the storage folder
                $publicPath = public_path($path); // Get full path to the public folder
                if (!file_exists($publicPath)) {
                    mkdir($publicPath, 0755, true); // Create the directory if it doesn't exist
                }
                $file->move($publicPath, $fileName); // Move file to the public path
                return $fileName;      // Return the relative path
            }
        }
        return 'File is not valid';
    }

    public function file_upload_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1',                          // Validate files array
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,webp,ai,indd,psd|max:100000', // Validate each file
            'path' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                // Create a temporary request object for each file
                $tempRequest = new \Illuminate\Http\Request();
                $tempRequest->files->set('file', $file); // Simulate single file upload
                $uploadedFiles[$index] = $this->file_upload($tempRequest, 'file', $request->path);
            }
        }

        return $this->successResponse($uploadedFiles, 'Files uploaded successfully', 200);
    }

    public function delete_file_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string', // Full path relative to the public folder
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $filePath = public_path($request->file_path); // Get the full path of the file

        if (file_exists($filePath)) {
            if (unlink($filePath)) { // Delete the file
                return $this->successResponse([], '', 200);
            } else {
                return $this->errorResponse([], '', 500);
            }
        } else {
            return $this->successResponse([], '');
        }

    }

    public function getEnumValues($table, $column)
    {
        // Get the enum column definition for the given table and column
        $enumValues = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '$column'")[0]->Type;

        // Extract the enum values
        preg_match('/^enum\((.*)\)$/', $enumValues, $matches);
        $values = [];
        if (isset($matches[1])) {
            $values = explode(',', $matches[1]);
            $values = str_replace("'", '', $values);
        }

        return $this->successResponse($values, 'success', 200);
    }

    public function downloadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 422);
        }

        $filePath = public_path($request->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($filePath);
    }

}
