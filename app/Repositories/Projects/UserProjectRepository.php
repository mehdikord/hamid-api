<?php
namespace App\Repositories\Projects;
use App\Http\Resources\Projects\Projects\ProjectShortResource;
use App\Interfaces\Projects\UserProjectInterface;

class UserProjectRepository implements UserProjectInterface
{
    public function all()
    {
        $result=[];
        $unique_ids = [];
        $data = auth('users')->user()->projects;
        foreach ($data as $item) {
            if (!in_array($item->project->id, $unique_ids, true)) {
                $result[] = $item->project;
                $unique_ids[] = $item->project->id;
            }
        }
        return helper_response_fetch(ProjectShortResource::collection($result));
    }
}
