<?php

namespace App\Http\Controllers\Admins\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\WhatsappNumber\WhatsappNumberCreateRequest;
use App\Http\Requests\Whatsapp\WhatsappNumber\WhatsappNumberUpdateRequest;
use App\Interfaces\Whatsapp\WhatsappInterface;
use App\Models\Whatsapp\WhatsappNumber;

class WhatsappNumberController extends Controller
{
    protected WhatsappInterface $repository;

    public function __construct(WhatsappInterface $whatsapp)
    {
        $this->middleware('generate_fetch_query_params')->only('index');
        $this->repository = $whatsapp;
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
    public function store(WhatsappNumberCreateRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(WhatsappNumber $number)
    {
        return $this->repository->show($number);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WhatsappNumberUpdateRequest $request, WhatsappNumber $number)
    {
        return $this->repository->update($request, $number);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhatsappNumber $number)
    {
        return $this->repository->destroy($number);
    }
}
