<?php

namespace App\Repositories\Activities;

use App\Http\Resources\Activities\ActivityIndexResource;
use App\Http\Resources\Activities\ActivitySingleResource;
use App\Interfaces\Activities\ActivityInterface;
use App\Models\Activity;

class ActivityRepository implements ActivityInterface
{
    public function index()
    {
        $data = Activity::with(['admin', 'user', 'project', 'customer'])->orderBy(request('sort_by'),request('sort_type'));

        return helper_response_fetch(
            ActivityIndexResource::collection($data->paginate(request('per_page', 15)))->resource
        );

    }

    public function show($item)
    {
        $item->load(['admin', 'user', 'project', 'customer']);
        return helper_response_fetch(new ActivitySingleResource($item));
    }

    public function destroy($item)
    {
        $item->delete();
        return helper_response_deleted();
    }

}
