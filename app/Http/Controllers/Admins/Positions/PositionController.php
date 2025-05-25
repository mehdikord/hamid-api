<?php
namespace App\Http\Controllers\Admins\Positions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Positions\PositionCreateRequest;
use App\Http\Requests\Positions\PositionUpdateRequest;
use App\Interfaces\Positions\PositionInterface;
use App\Models\Position;

class PositionController extends Controller
{
    protected PositionInterface $repository;

    public function __construct(PositionInterface $position)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $position;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->index();
    }

    public function all()
    {
        return $this->repository->all();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        return $this->repository->show($position);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionUpdateRequest $request,Position $position)
    {
        return $this->repository->update($request,$position);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        return $this->repository->destroy($position);
    }

}
