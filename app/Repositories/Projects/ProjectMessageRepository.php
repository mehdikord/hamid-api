<?php

namespace App\Repositories\Projects;

use App\Interfaces\Projects\ProjectMessageInterface;
use App\Models\Projects\Project_Message;
use Illuminate\Support\Facades\Storage;

class ProjectMessageRepository implements ProjectMessageInterface
{
    public function index($project)
    {
        $data = $project->messages();
        $data->where('project_id',$project->id);
        $data->orderBy(request('sort_by'),request('sort_type'));
        return helper_response_fetch($data->paginate(request('per_page')));
    }
    public function all($project)
    {
        $data = $project->messages();
        $data->where('project_id',$project->id);
        $data->orderByDesc('id');
        return helper_response_fetch($data->get());
    }

    public function store($project,$request)
    {
        $file_url = null;
        if($request->hasFile('file')){
            $file_path = Storage::put('public/projects/'.$project->id.'/messages', $request->file('file'),'public');
            $file_url = Storage::url($file_path);
        }
        $data = $project->messages()->create([
            'message' => $request->message,
            'title' => $request->title,
            'file' => $file_url,
            'buttons' => $request->buttons ? json_encode($request->buttons) : null,
        ]);

        // activity log
        helper_activity_create(null,null,$project->id, null,'ایجاد پیام','ایجاد پیام '.$data->title);
        return helper_response_fetch($data);
    }

    public function show($project,$item)
    {
        return helper_response_fetch($item);
    }

    public function update($project,$request,$item)
    {
        $file_url = $item->file;
        if($request->hasFile('file')){
            $file_path = Storage::put('public/projects/'.$project->id.'/messages', $request->file('file'),'public');
            $file_url = Storage::url($file_path);
        }
        $item->update([
            'title' => $request->title,
            'message' => $request->message,
            'file' => $file_url,
            'buttons' => $request->buttons ? json_encode($request->buttons) : null,
        ]);
        // activity log
        helper_activity_create(null,null,$project->id,null,'ویرایش پیام',"ویرایش پیام ".$item->title);
        return helper_response_updated($item);
    }

    public function destroy($project,$item)
    {
        if($item->file){
            Storage::delete($item->file);
        }
        // activity log
        helper_activity_create(null,null,$project->id,null,'حذف پیام','حذف پیام '.$item->title);
        $item->delete();
        return helper_response_deleted();
    }

}
