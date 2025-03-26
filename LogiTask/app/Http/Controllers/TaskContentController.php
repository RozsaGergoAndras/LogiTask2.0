<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Task_content;
use App\Http\Requests\StoreTask_contentRequest;
use App\Http\Requests\UpdateTask_contentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TaskContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTask_contentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task_content $task_content)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task_content $task_content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTask_contentRequest $request, Task_content $task_content)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id'=>'required|exists:task_contents,id'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error'=>$e->getMessage()], 400);
        }
        
        $content = Task_content::find($request->id);
        $path = 'uploads/'. $content->link;
        
        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        $content->delete();
        return response()->json(["success" => true, 'message'=>'Content deleted successfuly, file removed.'], 200);
    }

    public function uploadFile(Request $request)
    {
        // Validation for the uploaded file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,png,pdf,docx,txt,zip,rar|max:2048',
            'taskId' =>'required|integer|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $task = Task::find($request->taskId);
        if($task == null){
            return response()->json(['error' => 'Invalid Task ID'], 400);
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Base name without extension
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            
            // Generate a file name
            $newName = $baseName . '.' . $extension;
    
            // Check if a file with the same name exists and add a suffix if necessary
            $counter = 1;
            while (Storage::exists('uploads/' . $newName)) {
                $newName = $baseName . '-' . $counter . '.' . $extension;
                $counter++;
            }
            
    
            // Store the file
            $path = $file->storeAs('uploads', $newName, 'local'); 
            $newContent = Task_content::create([
                'link' => $newName,
                'task_id' => $task->id,
            ]);
            $newContent->save();
    
            return response()->json([
                'message' => 'File uploaded successfully',
                'path' => $path
            ]);
        }

        // Handle the file upload
        /*if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Store the file
            $path = $file->store('uploads', 'local'); 

            // You can return a URL to access the file
            $url = Storage::url($path);
            $newContent = Task_content::create([
                'link' => $url,
                'task_id' => $task->id,
            ]);
            $newContent->save();

            return response()->json([
                'message' => 'File uploaded successfully',
                'file_url' => $url
            ], 200);
        }*/

        return response()->json(['error' => 'No file provided'], 400);
    }

    public function downloadFile($filename)
    {
        // Check if the file exists in the storage
        $filePath = storage_path('app/public/uploads/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Return the file as a download response
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    public function getSignedUrl($filename)
    {
        // Generate a signed URL that expires in 5 minutes
        $filePath = 'uploads/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Generate the signed URL with a 5-minute expiration
        $url = Storage::temporaryUrl($filePath, Carbon::now()->addMinutes(5));

        return response()->json(['url' => $url]);
    }
}
