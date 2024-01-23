<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\HttpResponses;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use HttpResponses
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TaskResource::collection(
            Task::where('user_id',Auth::user()->id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'name'=> $request->name,
            'description'=> $request->description,
            'priority'=> $request->priority,
            'user_id'=> Auth::user()->id
        ]);
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return  is_Not_Auth($task) ? is_Not_Auth($task) : new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            $this->error('','you are not authorized to make this request',401);
         }
        $task->update($request->all());
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        return is_Not_Auth($task) ? is_Not_Auth($task) : $task->delete();
    }

    public function is_Not_Auth($task){
        if (Auth::user()->id !== $task->user_id) {
            $this->error('','you are not authorized to make this request',401);
         }
    }
}
