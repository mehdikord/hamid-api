<?php

namespace App\Http\Controllers\Users\Customers;

use App\Http\Controllers\Controller;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Interfaces\Users\UserCustomerInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected UserCustomerInterface $repository;
    protected CustomerSettingsStatusInterface $setting_repository;
    public function __construct(UserCustomerInterface $customer,CustomerSettingsStatusInterface $customerSettingsStatus)
    {
        $this->middleware('generate_fetch_query_params')->only('index');

        $this->repository = $customer;
        $this->setting_repository = $customerSettingsStatus;
    }

    public function index()
    {
        return $this->repository->users_index(auth('users')->user());
    }

    public function statuses()
    {
        return $this->setting_repository->all();
    }

}
