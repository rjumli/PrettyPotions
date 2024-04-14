<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
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
                    'categories' => DropdownResource::collection(Dropdown::with('services')->where('classification','Category')->get()),
                    'appointments' => Appointment::with('lists.service','lists.status','user.profile','status','review')->where('user_id',\Auth::user()->id)->whereIn('status_id',[19,20,22,23])->get()
                ]);
            break;
            case 'Staff':
                return inertia('Modules/Home/Management/Index',[
                    'categories' => DropdownResource::collection(Dropdown::with('services')->where('classification','Category')->get()),
                    'counts' => $this->counts(),
                    'appointments' => $this->appointments()
                ]);
            break;
            case 'Receptionist':
                return inertia('Modules/Home/Management/Index',[
                    'categories' => DropdownResource::collection(Dropdown::with('services')->where('classification','Category')->get()),
                    'counts' => $this->counts(),
                    'appointments' => $this->appointments()
                ]);
            break;
            default: 
                return inertia('Modules/Home/Dashboard/Index');
        }
    }

    public function counts(){
        return [
            [
                'name' => 'Pending Appointments',
                'icon' => 'ri-calendar-line',
                'color' => 'text-success',
                'money' => false,
                'total' => Appointment::where('status_id',19)->count()
            ],
            [
                'name' => 'Incoming Appointments',
                'icon' => 'ri-calendar-todo-line',
                'color' => 'text-warning',
                'money' => false,
                'total' => Appointment::where('status_id',22)->count()
            ],
            [
                'name' => 'Total Appointments',
                'icon' => 'ri-calendar-check-fill',
                'color' => 'text-danger',
                'money' => false,
                'total' => Appointment::where('status_id',20)->count()
            ],
            [
                'name' => 'Daily Income',
                'icon' => 'ri-wallet-3-line',
                'color' => 'text-danger',
                'money' => true,
                'total' => Appointment::where('status_id',20)->whereDate('created_at',now())->sum('total')
            ],
            [
                'name' => 'Total Income',
                'icon' => 'ri-safe-fill',
                'color' => 'text-danger',
                'money' => true,
                'total' => Appointment::where('status_id',20)->sum('total')
            ]
        ];
    }

    public function appointments(){
        $pending = Appointment::with('lists.service','lists.status','user.profile','status')->where('status_id',19)->orderBy('created_at','ASC')->get();
        $incoming = Appointment::with('lists.service','lists.status','user.profile','status')->where('status_id',22)->orderBy('created_at','ASC')->get();
        $ongoing = Appointment::with('lists.service','lists.status','user.profile','status')->where('status_id',23)->orderBy('created_at','ASC')->get();
        $completed = Appointment::with('lists.service','lists.status','user.profile','status')->where('status_id',20)->where('is_rated',0)->get();
        $rated = Appointment::with('lists.service','lists.status','user.profile','status')->where('status_id',20)->where('is_rated',1)->get();

        return [
            'pending' => $pending,
            'incoming' => $incoming,
            'ongoing' => $ongoing,
            'completed' => $completed,
            'rated' => $rated
        ];
    }
}
