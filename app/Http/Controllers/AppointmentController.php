<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Dropdown;
use Illuminate\Http\Request;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\DropdownResource;
use GuzzleHttp\Client;

class AppointmentController extends Controller
{
    public function index(Request $request){
        switch($request->option){
            case 'lists':
                return $this->lists($request);
            break;
            case 'reports':
                return $this->reports($request);
            break;
            default: 
                return inertia('Modules/Appointments/Index',[
                    'categories' => DropdownResource::collection(Dropdown::with('services')->where('classification','Category')->get()),
                ]);
        }
    }
    public function notify($request){
        dd('wew');
    }

    public function lists($request){
        $data = DefaultResource::collection(
            Appointment::query()
            ->with('user.profile','status','lists.service','lists.status','lists.aesthetician','review')
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
        if($request->option == 'notify'){
            $list = $request->list;
           
            $name = $list['user']['profile']['firstname'].' '.$list['user']['profile']['lastname'];
            $mobile = $list['user']['profile']['mobile'];
            // $mobile = '09171531652';
            $date = $list['date'];
            $content = 'Appointment Reminder: Hello '.$name.' from Pretty Potions! Your appointment is scheduled on '.$date.'. Please be there. See you soon!';
            
            $client = new Client();
            $result = $client->request('GET', 'http://gateway.onewaysms.ph:10001/api.aspx', [
                'query' => [
                    'apiusername' => 'APIJLHNMMIQBJ',
                    'apipassword' => 'APIJLHNMMIQBJJLHNM',
                    'senderid' => 'TEST',
                    'mobileno' => $mobile,
                    'message' => $content,
                    'languagetype' => 1
                ]
            ]);
            $response = json_decode($result->getBody()->getContents());
                dd($response);
            return back()->with([
                'data' => $response,
                'message' => 'Message sent successfully.',
                'info' => '-',
                'status' => true,
            ]);
        }else{
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

    public function reports($request){
        $current_year = $request->year; $years = []; 
        $laboratory = $request->laboratory;

        $programs = [
            ['name' => 'Completed Appointments', 'value' => 20],
            ['name' => 'Cancelled Appointments', 'value' => 21],
        ];
        $prog = []; 
        foreach($programs as $program){
            $data = []; $year = $current_year - 20;
            for($year; $year <= $current_year; $year++){
                $years[] = $year; $p = $program['value'];
                $data[] = Appointment::where('status_id',$p)->whereYear('created_at',$year)->count();
            }
            $arr[] = [
                'name' => $program['name'],
                'data' => $data  
            ];
            
        }

        return $y =[
            'categories' => $years,
            'lists' => $arr
        ];
    }
    
}
