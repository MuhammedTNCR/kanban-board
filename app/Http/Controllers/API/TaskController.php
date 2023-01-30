<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = $request->user()->all_tasks;

        return $this->sendResponse(TaskResource::collection($tasks), 'Tasks retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'status' => [
                'nullable', Rule::in(Task::statuses())
            ],
            'priority' => [
                'nullable', Rule::in(Task::priorities())
            ],
            'assigned_user_id' => [
                'nullable', 'exists:users,id'
            ]
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? Task::TO_DO,
            'priority' => $request->priority ?? Task::LOW,
            'creator_user_id' => $request->user()->id,
            'assigned_user_id' => $request->assigned_user_id
        ]);

        return $this->sendResponse(new TaskResource($task), 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $task = Task::find($id);

        if (is_null($task)) {
            return $this->sendError('Task not found.');
        }

        return $this->sendResponse(new TaskResource($task), 'Task retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'status' => [
                'nullable', Rule::in(Task::statuses())
            ],
            'priority' => [
                'nullable', Rule::in(Task::priorities())
            ],
            'assigned_user_id' => [
                'nullable', 'exists:users,id'
            ]
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $task->name = $input['name'];
        $task->description = $input['description'];
        $task->status = $input['status'] ?? Task::TO_DO;
        $task->description = $input['priority'] ?? Task::LOW;
        $task->assigned_user_id = $input['assigned_user_id'] ?? null;
        $task->save();

        return $this->sendResponse(new TaskResource($task), 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return $this->sendResponse([], 'Task deleted successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function sync(Request $request): JsonResponse
    {
        $input = $request->all();

        /*$validator = Validator::make($input, [
            'status' => [
                Rule::in(Task::statuses())
            ],
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }*/

        foreach ($input['columns'] as $status) {
            foreach ($status['tasks'] as $i => $task) {
                $order = $i + 1;
                if ($task['status'] !== $status['name'] || $task['order'] !== $order) {
                    $request->user()->my_tasks()
                        ->find($task['id'])
                        ->update(['status' => $status['name'], 'order' => $order]);
                }
            }
        }

        return $this->sendResponse(new TaskResource($request->user()->all_tasks), 'Task updated successfully.');
    }
}
