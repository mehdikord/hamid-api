<?php

namespace App\Http\Controllers\Admins\Activities;

use App\Http\Controllers\Controller;
use App\Http\Requests\Activities\ActivityStoreRequest;
use App\Http\Requests\Activities\ActivityUpdateRequest;
use App\Interfaces\Activities\ActivityInterface;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    protected ActivityInterface $repository;

    public function __construct(ActivityInterface $activity)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $activity;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->index();
    }



    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        return $this->repository->show($activity);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        return $this->repository->destroy($activity);
    }


}
