<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentService;
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

    public function store(Request $request){
        $user_id = ($request->user_id) ? $request->user_id : \Auth::user()->id;
        $count = Appointment::count();
        $code = 'PP-'.date('Y').'-'.str_pad(($count+1), 5, '0', STR_PAD_LEFT);  
        $data = Appointment::create(array_merge($request->all(), ['code' => $code, 'user_id' => $user_id,'status_id' => 19]));
        $carts = $request->cart;
        foreach($carts as $cart){
            $service = new AppointmentService;
            $service->price = $cart['price'];
            $service->service_id = $cart['id'];
            $service->status_id = 19;
            $service->appointment_id = $data->id;
            $service->save();
        }

        return back()->with([
            'data' => $data,
            'message' => 'Appointment added successfully.',
            'info' => '-',
            'status' => true,
        ]);
    }

    public function update(Request $request){
        $data = Appointment::findOrFail($request->id);
        $data->update($request->except('editable'));
        $data = Appointment::with('lists.service','lists.status','user.profile','status')->where('id',$request->id)->first();
        
        return back()->with([
            'data' => $data,
            'message' => 'Appointment updated successfully.',
            'info' => '-',
            'status' => true,
        ]);
    }
    
}
