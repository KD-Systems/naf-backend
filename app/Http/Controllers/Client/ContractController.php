<?php

namespace App\Http\Controllers\Client;

use App\Models\Company;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ContractResource;

class ContractController extends Controller
{
    public function show(Company $company)
    {
        return CompanyResource::make($company);
    }
}
