<?php

namespace App\Http\Controllers\FrontEnd;

use App\Mail\MerchantEmailNotificationForParcelReceive;
use App\Mail\MerchantRegistrationEmail;
use App\Mail\ParcelStatusUpdateEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Agent;
use App\Parcel;
use App\Pickup;
use App\Deliverycharge;
use App\Deliveryman;
use App\Merchant;
use App\Parcelnote;
use App\Parceltype;
use App\History;
use App\Exports\AgentParcelExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use mysql_xdevapi\Exception;
use Session;
use DB;
class AgentController extends Controller
{
  public function view(){
    $id = Session::get('agentId');
    $agentInfo = Agent::find($id);
     $parcels = DB::table('parcels')
         ->join('merchants', 'merchants.id','=','parcels.merchantId')
         ->join('agents', 'parcels.agentId','=','agents.id')
         ->where('parcels.agentId', $id)
         ->orderBy('parcels.id','DESC')
         ->select('parcels.*','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress','merchants.companyName','merchants.status as mstatus','merchants.id as mid')
         ->get();
     $totalamount=Parcel::where(['agentId'=>$id,'status'=>4])
     ->sum('merchantDue');
     $unpaidamount=Parcel::where(['agentId'=>$id,'status'=>4])
     ->sum('merchantDue');
     return view('frontEnd.layouts.pages.agent.view',compact('agentInfo','parcels','totalamount','unpaidamount'));
 }
    public function loginform(){
        return view('frontEnd.layouts.pages.agent.login');
    }
    public function login(Request $request){
        $this->validate($request,[
            'email' => 'required',
            'password' => 'required',
        ]);
       $checkAuth =Agent::where('email',$request->email)
       ->first();
        if($checkAuth){
          if($checkAuth->status == 0){
             Toastr::warning('warning', 'Opps! your account has been suspends');
             return redirect()->back();
         }else{
          if(password_verify($request->password,$checkAuth->password)){
              $agentId = $checkAuth->id;
               Session::put('agentId',$agentId);
               Toastr::success('success', 'Thanks , You are login successfully');
              return redirect('/agent/dashboard');
            
          }else{
              Toastr::error('Opps!', 'Sorry! your password wrong');
              return redirect()->back();
          }

           }
        }else{
          Toastr::error('Opps!', 'Opps! you have no account');
          return redirect()->back();
        } 
    }
    public function dashboard(){
    	  $totalparcel=Parcel::where(['agentId'=>Session::get('agentId')])->count();
          $totaldelivery=Parcel::where(['agentId'=>Session::get('agentId'),'status'=>4])->count();
          $totalhold=Parcel::where(['agentId'=>Session::get('agentId'),'status'=>5])->count();
          $totalcancel=Parcel::where(['agentId'=>Session::get('agentId'),'status'=>9])->count();
          $returntohub=Parcel::where(['agentId'=>Session::get('agentId'),'status'=>7])->count();
          $returnmerchant=Parcel::where(['agentId'=>Session::get('agentId'),'status'=>8])->count();
          $totalamount=Parcel::where(['agentId'=>Session::get('agentId'),'status'=>4])
            ->sum('cod');
        
            
          return view('frontEnd.layouts.pages.agent.dashboard',compact('totalparcel','totaldelivery','totalhold','totalcancel','returntohub','returnmerchant','totalamount'));
    }
    public function parcels(Request $request){
       $filter = $request->filter_id;
       if($request->trackId!=NULL){
        $allparcel = DB::table('parcels')
        ->join('merchants', 'merchants.id','=','parcels.merchantId')
        ->join('nearestzones', 'nearestzones.id', '=', 'parcels.reciveZone')
        ->join('deliverycharges', 'deliverycharges.id','=','parcels.orderType')
        ->where('parcels.agentId',Session::get('agentId'))
        ->where('parcels.trackingCode',$request->trackId)
        ->select('parcels.*','deliverycharges.title','nearestzones.zonename','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress','merchants.companyName','merchants.status as mstatus','merchants.id as mid')
        ->orderBy('id','DESC')
        ->get();
       }elseif($request->phoneNumber!=NULL){
        $allparcel = DB::table('parcels')
        ->join('merchants', 'merchants.id','=','parcels.merchantId')
        ->join('deliverycharges', 'deliverycharges.id','=','parcels.orderType')
        ->join('nearestzones', 'nearestzones.id', '=', 'parcels.reciveZone')
        ->where('parcels.agentId',Session::get('agentId'))
        ->where('parcels.recipientPhone',$request->phoneNumber)
        ->select('parcels.*','deliverycharges.title','nearestzones.zonename','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress','merchants.companyName','merchants.status as mstatus','merchants.id as mid')
        ->orderBy('id','DESC')
        ->get();
       }elseif($request->startDate!=NULL && $request->endDate!=NULL){
        $allparcel = DB::table('parcels')
        ->join('merchants', 'merchants.id','=','parcels.merchantId')
        ->join('deliverycharges', 'deliverycharges.id','=','parcels.orderType')
        ->join('nearestzones', 'nearestzones.id', '=', 'parcels.reciveZone')
        ->where('parcels.agentId',Session::get('agentId'))
        ->whereBetween('parcels.created_at',[$request->startDate, $request->endDate])
        ->select('parcels.*','deliverycharges.title','nearestzones.zonename','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress','merchants.companyName','merchants.status as mstatus','merchants.id as mid')
        ->orderBy('id','DESC')
        ->get();
       }elseif($request->phoneNumber!=NULL || $request->phoneNumber!=NULL && $request->startDate!=NULL && $request->endDate!=NULL){
        $allparcel = DB::table('parcels')
        ->join('merchants', 'merchants.id','=','parcels.merchantId')
        ->join('deliverycharges', 'deliverycharges.id','=','parcels.orderType')
        ->join('nearestzones', 'nearestzones.id', '=', 'parcels.reciveZone')
        ->where('parcels.agentId',Session::get('agentId'))
        ->where('parcels.recipientPhone',$request->phoneNumber)
        ->whereBetween('parcels.created_at',[$request->startDate, $request->endDate])
        ->select('parcels.*','deliverycharges.title','nearestzones.zonename','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress','merchants.companyName','merchants.status as mstatus','merchants.id as mid')
        ->orderBy('id','DESC')
        ->get();
       }else{
        $allparcel = DB::table('parcels')
        ->join('merchants', 'merchants.id','=','parcels.merchantId')
        ->join('deliverycharges', 'deliverycharges.id','=','parcels.orderType')
        ->join('nearestzones', 'nearestzones.id', '=', 'parcels.reciveZone')
        ->where('parcels.agentId',Session::get('agentId'))
        ->select('parcels.*', 'deliverycharges.title','nearestzones.zonename','merchants.companyName','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress')
        ->orderBy('parcels.id','DESC')
        ->get();
       }

       $aparceltypes = Parceltype::limit(3)->get();
    //   return $allparcel;
      return view('frontEnd.layouts.pages.agent.parcels',compact('allparcel','aparceltypes'));
  }
  
  public function parcelstatus($slug){

      $parceltype = Parceltype::where('slug',$slug)->first();
      $allparcel = DB::table('parcels')
        ->join('merchants', 'merchants.id','=','parcels.merchantId')
                ->join('deliverycharges', 'deliverycharges.id','=','parcels.orderType')
        ->join('nearestzones', 'nearestzones.id', '=', 'parcels.reciveZone')
        ->where('parcels.agentId',Session::get('agentId'))
        ->where('parcels.status',$parceltype->id)
        ->select('parcels.*','deliverycharges.title','nearestzones.zonename','merchants.companyName','merchants.firstName','merchants.lastName','merchants.phoneNumber','merchants.emailAddress')
        ->orderBy('parcels.id','DESC')
        ->get();

      //dd($allparcel);

        return view('frontEnd.layouts.pages.agent.parcels', compact('allparcel'));
  }

  public function parcelReceive(Request $request){

      if (is_array($request->parcels)){
          foreach($request->parcels as $parcel){
              $dbData = Parcel::findOrFail($parcel);

              $dbData->status = 3;
              $dbData->save();

              $parcelNote = new Parcelnote();
              $parcelNote->parcelId = $dbData->id;
              $parcelNote->note = 'Arrived Delivery Facility';
              $parcelNote->save();

              //send email to this
              try {
                  $merchant = Merchant::find($dbData->merchantId);
                  $agent = Agent::find(Session::get('agentId'));

                  Mail::to($merchant->emailAddress)->send(new MerchantEmailNotificationForParcelReceive($merchant, $dbData, $agent));

                  Log::info('Success: Parcel Received by agent and sent mail notification to Merchant Success');
              } catch (\Exception $exception) {
                  Log::info('Error: Parcel Received by agent and sent mail notification to Merchant : '. $exception->getMessage());
              }
          }
          return response()->json(['success' => 'success'], 200); // Status code here
      }

      return response()->json(['error' => 'invalid'], 401); // Status code here

  }
  
   public function invoice($id){
    $show_data = DB::table('parcels')
    ->join('merchants', 'merchants.id','=','parcels.merchantId')
    ->where('parcels.agentId',Session::get('agentId'))
    ->join('nearestzones', 'parcels.reciveZone','=','nearestzones.id')
    ->where('parcels.id',$id)
    ->join('deliverycharges', 'deliverycharges.id', '=', 'nearestzones.state')
    ->select('parcels.*','deliverycharges.title', 'nearestzones.zonename','nearestzones.state', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
    ->first();
        if($show_data!=NULL){
        	return view('frontEnd.layouts.pages.agent.invoice',compact('show_data'));
        }else{
          Toastr::error('Opps!', 'Your process wrong');
          return redirect()->back();
        }
    }
  public function delivermanasiagn(Request $request){
      $this->validate($request,[
        'deliverymanId'=>'required',
      ]);
      $parcel = Parcel::find($request->hidden_id);
      $parcel->deliverymanId = $request->deliverymanId;
      $parcel->save();
      
      
              //Save to History table
        
        $deliveryman = Agent::where('id', session('agentId'))->first();

     $history               = new History();
     $history->name         = $parcel->recipientName;
     $history->parcel_id    = $request->hidden_id;
     $history->done_by         = $deliveryman->name;
     $history->status         =  'DeliveryMan Asign From Agent.';
     $history->note         = $request->note;
     $history->date         =  $parcel->updated_at;
     $history->save();

      Toastr::success('message', 'A deliveryman asign successfully!');
      return redirect()->back();
      $deliverymanInfo = Deliveryman::find($parcel->deliverymanId);
      $merchantinfo =Agent::find($parcel->merchantId);
      $data = array(
       'contact_mail' => $merchantinfo->email,
       'ridername' => $deliverymanInfo->name,
       'riderphone' => $deliverymanInfo->phone,
       'codprice' => $parcel->cod,
       'trackingCode' => $parcel->trackingCode,
      );
      $send = Mail::send('frontEnd.emails.percelassign', $data, function($textmsg) use ($data){
       $textmsg->from('info@aschi.com.bd');
       $textmsg->to($data['contact_mail']);
       $textmsg->subject('Percel Assign Notification');
      });  
  }

  public function bulkdeliverymanAssign(Request $request) {

    $parcels_id = $request->parcel_id;
    $asigntype  = $request->asigntype;

    if ($asigntype == 1) {

        foreach ($parcels_id as $parcel_id) {
            $parcel              = Parcel::find($parcel_id);
            $parcel->pickupmanId = $request->deliverymanId;
            $parcel->save();

            $note           = new Parcelnote();
	        $note->parcelId = $parcel_id;
	        $note->note     = "Pickup Man Asign";
	        $note->save();
        }

    } else {

        foreach ($parcels_id as $parcel_id) {
            $parcel                = Parcel::find($parcel_id);
            $parcel->deliverymanId = $request->deliverymanId;
            $parcel->status        = 3;
            $parcel->save();

            $note           = new Parcelnote();
	        $note->parcelId = $parcel_id;
	        $note->note     = "Assigned To Delivery Man";
	        $note->save();
        }

    }

    // if ($asigntype == 1) {
    //     $note           = new Parcelnote();
    //     $note->parcelId = $parcel_id;
    //     $note->note     = "Pickup Man Asign";
    //     $note->save();
    // } else {
    //     $note           = new Parcelnote();
    //     $note->parcelId = $parcel_id;
    //     $note->note     = "Delivery Man Asign";
    //     $note->save();
    // }

    return redirect()->back();

}
  
  public function statusupdate(Request $request){
    //   return $request->all();
      $this->validate($request,[
        'status'=>'required',
      ]); 
      $parcel = Parcel::find($request->hidden_id);
      $parcel->status = $request->status;
      $parcel->updated_at = Carbon::now();
      $parcel->save();

       $pnote = Parceltype::find($request->status);
        $note = new Parcelnote();
        $note->parcelId = $request->hidden_id;
        $note->note = $request->note;
        // $note->note = "Your parcel ".$pnote->title;
        $note->save();
    
    
        
        $deliverymanInfo =Deliveryman::where(['id'=>$parcel->deliverymanId])->first();
         if($request->status==2 && $deliverymanInfo!=NULL){
            $merchantinfo =Agent::find($parcel->merchantId);
            $data = array(
             'contact_mail' => $merchantinfo->email,
             'ridername' => $deliverymanInfo->name,
             'riderphone' => $deliverymanInfo->phone,
             'codprice' => $parcel->cod,
             'trackingCode' => $parcel->trackingCode,
            );
            $send = Mail::send('frontEnd.emails.percelassign', $data, function($textmsg) use ($data){
             $textmsg->from('info@aschi.com.bd');
             $textmsg->to($data['contact_mail']);
             $textmsg->subject('Percel Assign Notification');
            });
        }
        if($request->status==3){
            $codcharge=0;
            $parcel->merchantAmount=($parcel->merchantAmount)-($codcharge);
            $parcel->merchantDue=($parcel->merchantAmount)-($codcharge);
            $parcel->codCharge=$codcharge;
            $parcel->save();
        }elseif($request->status==4){
            $merchantinfo = Merchant::find($parcel->merchantId);
            $data = array(
             'contact_mail' => $merchantinfo->emailAddress,
             'trackingCode' => $parcel->trackingCode,
            );
             
            //  $send = Mail::send('frontEnd.emails.percelassign', $data, function($textmsg) use ($data){
            //  $textmsg->from('info@aschi.com.bd');
            //  $textmsg->to($data['contact_mail']);
            //  $textmsg->subject('Percel Assign Notification');
            // });

        } elseif ($request->status == 6) {
            if($parcel->payment_option == 2){
                $charge = Deliverycharge::find($parcel->orderType);
                $codcharge = ($request->partial_payment * $charge->cod) / 100;
                $parcel->cod = $request->partial_payment;
                
                $amount = $request->partial_payment - ($codcharge + $parcel->deliveryCharge);
                
                $parcel->merchantAmount = $amount;
                $parcel->merchantDue    = $amount;
                $parcel->codCharge      = $codcharge;
                $parcel->save();
                
            }
        }elseif($request->status==8){
            $parcel = Parcel::find($request->hidden_id);
            $returncharge = $parcel->deliveryCharge/2;
            $parcel->merchantAmount=$parcel->merchantAmount-$returncharge;
            $parcel->merchantDue=$parcel->merchantAmount-$returncharge;
            $parcel->deliveryCharge= $parcel->deliveryCharge+$returncharge;
            $parcel->save();
        }
        
        
        
              $pstatus = Parceltype::find($request->status);
        
        $pstatus = $pstatus->title;
        
        
        
        //Save to History table
        
        $agent = Agent::where('id', session('agentId'))->first();

     $history               = new History();
     $history->name         = $parcel->recipientName;
     $history->parcel_id    = $request->hidden_id;
     $history->done_by         = $agent->name;
     $history->status         =  $pstatus;
     $history->note         = $request->note;
     $history->date         =  $parcel->updated_at;
     $history->save();

      try {
          $validMerchant = Merchant::find($parcel->merchantId);
          if (!empty($validMerchant)) {
              \Illuminate\Support\Facades\Mail::to([
                  $validMerchant->emailAddress
              ])->send(new ParcelStatusUpdateEmail($validMerchant, $parcel, $history));
          }
      } catch (\Exception $exception) {
          Log::info('Agent Parcel status update mail error: '. $exception->getMessage());
      }
        
        
        
        
        
      Toastr::success('message', 'Parcel information update successfully!');
      return redirect()->back();
    }
  public function logout(){
      Session::flush();
      Toastr::success('Success!', 'Thanks! you are logout successfully');
      return redirect('agent/logout');
  }
 public function pickup(){
      $show_data = DB::table('pickups')
      ->where('pickups.agent',Session::get('agentId'))
      ->orderBy('pickups.id','DESC')
      ->select('pickups.*')
      ->get();
      $deliverymen = Deliveryman::where('status',1)->get();
      return view('frontEnd.layouts.pages.agent.pickup',compact('show_data','deliverymen'));
    }
    public function pickupdeliverman(Request $request){
        $this->validate($request,[
          'deliveryman'=>'required',
        ]);
        $pickup = Pickup::find($request->hidden_id);
        $pickup->deliveryman = $request->deliveryman;
        $pickup->save();

        Toastr::success('message', 'A deliveryman asign successfully!');
        return redirect()->back();
        $deliverymanInfo = Deliveryman::find($parcel->deliverymanId);
        $agentInfo =Agent::find($parcel->merchantId);
        $data = array(
         'contact_mail' => $agentInfo->email,
         'ridername' => $deliverymanInfo->name,
         'riderphone' => $deliverymanInfo->phone,
         'codprice' => $pickup->cod,
        );
        $send = Mail::send('frontEnd.emails.percelassign', $data, function($textmsg) use ($data){
         $textmsg->from('info@aschi.com.bd');
         $textmsg->to($data['contact_mail']);
         $textmsg->subject('Pickup Assign Notification');
        });
          
    }
     public function pickupstatus(Request $request){
      $this->validate($request,[
        'status'=>'required',
      ]);
      $pickup = Pickup::find($request->hidden_id);
      $pickup->status = $request->status;
      $pickup->save();
    
        if($request->status==2){
            $deliverymanInfo =Deliveryman::where(['id'=>$pickup->deliveryman])->first();
            // $data = array(
            //  'name' => $deliverymanInfo->name,
            //  'companyname' => $merchantInfo->companyName,
            //  'phone' => $deliverymanInfo->phone,
            //  'address' => $merchantInfo->pickLocation,
            // );
            // $send = Mail::send('frontEnd.emails.pickupdeliveryman', $data, function($textmsg) use ($data){
            //  $textmsg->from('info@aschi.com.bd');
            //  $textmsg->to($data['contact_mail']);
            //  $textmsg->subject('Pickup request update');
            // });
        }
      Toastr::success('message', 'Pickup status update successfully!');
      return redirect()->back();
    }
   public function passreset(){
      return view('frontEnd.layouts.pages.agent.passreset');
    }
    public function passfromreset(Request $request){
      $this->validate($request,[
            'email' => 'required',
        ]);
        $validAgent =Agent::Where('email',$request->email)
       ->first();
        if($validAgent){
             $verifyToken=rand(111111,999999);
             $validAgent->passwordReset  = $verifyToken;
             $validAgent->save();
             Session::put('resetAgentId',$validAgent->id);
             
             $data = array(
             'contact_mail' => $validAgent->email,
             'verifyToken' => $verifyToken,
            );
            $send = Mail::send('frontEnd.layouts.pages.agent.forgetemail', $data, function($textmsg) use ($data){
             $textmsg->from('support@zuri.express');
             $textmsg->to($data['contact_mail']);
             $textmsg->subject('Forget password token');
            });
          return redirect('agent/resetpassword/verify');
        }else{
              Toastr::error('Sorry! You have no account', 'warning!');
             return redirect()->back();
        }
    }
    public function saveResetPassword(Request $request){
       $validAgent =Agent::find(Session::get('resetAgentId'));
        if($validAgent->passwordReset==$request->verifyPin){
           $validAgent->password   = bcrypt(request('newPassword'));
           $validAgent->passwordReset  = NULL;
             $validAgent->save();
             
             Session::forget('resetAgentId');
             Session::put('agentId',$validAgent->id);
             Toastr::success('Wow! Your password reset successfully', 'success!');
             return redirect('agent/dashboard');
        }else{
            Toastr::error('Sorry! Your process something wrong', 'warning!');
             return redirect()->back();
        }
       
    }
    public function resetpasswordverify(){
        if(Session::get('resetAgentId')){
        return view('frontEnd.layouts.pages.agent.passwordresetverify');
        }else{
            Toastr::error('Sorry! Your process something wrong', 'warning!');
            return redirect('forget/password');
        }
    }
    public function export( Request $request ) {
        return Excel::download( new AgentParcelExport(), 'parcel.xlsx') ;
    
    }
}
