<?php

namespace App\Http\Controllers;

use App\Models\Dropdown;
use Illuminate\Http\Request;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\DropdownResource;

class HomeController extends Controller
{
    public function index(){
        $role = \Auth::user()->role;

        switch($role){
            case 'Client':
                return inertia('Modules/Home/Client/Index',[
                    'categories' => DropdownResource::collection(Dropdown::with('services')->where('classification','Category')->get())
                ]);
            break;
            default: 
                return inertia('Modules/Home/Dashboard/Index');
        }
    }
}
