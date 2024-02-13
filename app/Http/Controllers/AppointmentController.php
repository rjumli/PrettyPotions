<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dropdown;
use Illuminate\Http\Request;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\DropdownResource;

class AppointmentController extends Controller
{
    public function index(Request $request){
        switch($request->option){
            case 'lists':
                return $this->lists($request);
            break;
            default: 
                return inertia('Modules/Appointments/Index',[
                    'categories' => DropdownResource::collection(Dropdown::with('services')->where('classification','Category')->get()),
                ]);
        }
    }

    public function lists($request){
        $data = DefaultResource::collection(
            Appointment::query()
            ->with('user','status','lists.service','lists.status','lists.aesthetician')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%");
            })
            ->paginate($request->counts)
        );
        return $data;
    }
    
}
