<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            $tasks = Task::all();
            $tasks = $user->tasks()->orderBy('created_at', 'asc')->paginate(10);
            
            return view('welcome',[
                'tasks' => $tasks,
            ]);
        } return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;
        return view('tasks.create',[
            'task' => $task,    
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'status' => 'required|max:10',
        ]);
        
        $task = new Task;
        $task->content = $request->content;
        $task->status = $request->status;
        $task->user_id = $request->user()->id;
        $task->save();

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $task = Task::findOrFail($id);
        
        if(\Auth::id() != $task->user_id){
            return redirect('/');
        }
        //$this->authorize('view',$task);
        
        return view('tasks.show',[
            'task' => $task, 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        if(\Auth::id() != $task->user_id){
            return redirect('/');
        }
        //$this->authorize('edit',$task);
        
        return view('tasks.edit',[
            'task' => $task, 
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
            'status' => 'required|max:10',
        ]);
        
        $task = Task::findOrFail($id);
        
        if(\Auth::id() != $task->user_id){
            return redirect('/');
        }
        
        $task->content = $request->content;
        $task->status = $request->status;
        $task->save();
        
        $this->authorize('update',$task);
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        if(\Auth::id() != $task->user_id){
            return redirect('/');
        }
        $task->delete();
        
        return redirect('/');
    }
}
