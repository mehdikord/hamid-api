<?php

namespace App\Repositories\Projects;

use App\Interfaces\Projects\ProjectMessageInterface;
use App\Models\Projects\Project_Message;

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
        $data = $project->messages()->create([
            'message' => $request->message,
            'title' => $request->title,
        ]);

        return helper_response_fetch($data);
    }

    public function show($project,$item)
    {
        return helper_response_fetch($item);
    }

    public function update($project,$request,$item)
    {
        $item->update([
            'title' => $request->title,
            'message' => $request->message,
        ]);
        return helper_response_updated($item);
    }

    public function destroy($project,$item)
    {
        $item->delete();
        return helper_response_deleted();
    }

}
