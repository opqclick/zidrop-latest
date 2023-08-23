<?php

namespace App\Http\Controllers\FrontEnd;

use App\Codcharge;
use App\Deliverycharge;
use App\Deliveryman;
use App\Disclamer;
use App\Exports\ParcelExport;
use App\Http\Controllers\Controller;
use App\Imports\ParcelImport;
use App\Mail\MerchantRegisterAlertMailable;
use App\Mail\MerchantRegistrationEmail;
use App\Mail\MerchantResetPasswordEmail;
use App\Mail\NewPickupRequestEmail;
use App\Merchant;
use App\Merchantpayment;
use App\Nearestzone;
use App\Notice;
use App\Parcel;
use App\Parcelnote;
use App\Parceltype;
use App\Pickup;
use App\RemainTopup;
use Brian2694\Toastr\Facades\Toastr;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

use Session;

class MerchantController extends Controller {

    public function registerpage() {
        return view('frontEnd.layouts.pages.register');
    }

    public function register(Request $request) {
        $this->validate($request, [
            'companyName'  => 'required',
            'firstName'    => 'required',
            'phoneNumber'  => 'required',
            'emailAddress' => 'unique:merchants',
            'password'     => 'required|same:confirmed',
            'confirmed'    => 'required',
        ]);
        //return $request->all();
        $marchentCheck = Merchant::where('phoneNumber', $request->phoneNumber)->where('emailAddress', $request->emailAddress)->first();

        if ($marchentCheck) {
            Toastr::error('message', 'Opps! your credential already used');

            return redirect()->back();
        } else {
            $store_data               = new Merchant();
            $verifyToken              = rand(111111, 999999);
            $store_data->companyName  = $request->companyName;
            $store_data->firstName    = $request->firstName;
            $store_data->lastName     = $request->lastName;
            $store_data->phoneNumber  = $request->phoneNumber;
            $store_data->emailAddress = $request->emailAddress;
            $store_data->agree        = $request->agree;
            $store_data->password     = bcrypt(request('password'));
            $store_data->verifyToken = 1;
            $store_data->status      = 0;
            $store_data->save();

            /*$url  = "https://sms.solutionsclan.com/api/sms/send";
            $data = [
                "apiKey"         => "A00003133467cc6-219a-4dfd-882e-0bcf8836ebc3",
                "contactNumbers" => $request->phoneNumber,
                "senderId"       => "8809612440632",
                "textBody"       => "Dear $request->companyName\r\nSuccessfully boarded your account. Your verified token is	$verifyToken .\r\nRegards,\r\n Zuri Express",
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch);
            echo "$response";
            curl_close($ch);

            Session::put('phoneverify', $store_data->phoneNumber);*/

            try {
                Mail::to($store_data->emailAddress)->send(new MerchantRegistrationEmail($store_data));
            } catch (\Exception $exception) {
                Log::info('Merchant-Register mail error: '. $exception->getMessage());
            }

            // Send an email to Admin to notify about the new merchant registration
            $receiverEmail = 'e-tailing@zidrop.com';
            try {
                Mail::to($receiverEmail)->send(new MerchantRegisterAlertMailable($store_data));
            } catch (\Exception $exception) {
                Log::info('Merchant-Register-Alert mail error: '. $exception->getMessage());
            }
            Toastr::success('message', 'Thank you for signing up with us! A dedicated representative will be reaching out to you shortly.');
            
//          return redirect('merchant/phone-verify');
            return redirect('merchant/login');
        }

    }

    public function loginpage() {
        if(Session::get('merchantId')) {
            return redirect('/merchant/dashboard');
        }
        $globNotice = Notice::where('published', 1)->first();
        return view('frontEnd.layouts.pages.login')->with(compact('globNotice'));
    }

    public function login(Request $request) {
        $this->validate($request, [
            'phoneOremail' => 'required',
            'password'     => 'required',
        ]);

        $merchantChedk = Merchant::orWhere('emailAddress', $request->phoneOremail)
            ->orWhere('phoneNumber', $request->phoneOremail)
            ->first();

        if ($merchantChedk) {

            if ($merchantChedk->status == 0 || $merchantChedk->verifyToken == 0) {
                Toastr::warning('warning', 'Your account is currently undergoing a review process. During this time, you might not be able to access your account.');

                return redirect()->back();
            } else {

                if (password_verify($request->password, $merchantChedk->password)) {
                    $merchantId = $merchantChedk->id;
                    Session::put('merchantId', $merchantId);
                    Toastr::success('success', 'Thanks , You are login successfully');

                    return redirect('/merchant/dashboard');

                } else {
                    Toastr::error('Opps!', 'Sorry! your password wrong');

                    return redirect()->back();
                }

            }

        } else {
            Toastr::error('Opps!', 'Opps! you have no account');

            return redirect()->back();
        }

    }

    public function phoneVerifyForm() {
        $phoneverify = Session::get('phoneverify');

        if ($phoneverify == !NULL) {
            return view('frontEnd.layouts.pages.merchant.verify');
        } else {
            Toastr::error('!Opps', 'Your process is invalid');

            return redirect('/');
        }

    }

    public function phoneresendcode(Request $request) {
        $merchantInfo              = Merchant::where('phoneNumber', Session::get('phoneverify'))->first();
        $verifyToken               = rand(1111, 9999);
        $merchantInfo->verifyToken = $verifyToken;
        $merchantInfo->save();
        $url  = "http://premium.mdlsms.com/smsapi";
        $data = [
            "api_key"  => "C20005455f867568bd8c02.20968541",
            "type"     => "Text",
            "contacts" => '0' . $merchantInfo->phoneNumber,
            "senderid" => "8809612440738",
            "msg"      => "Your verify Token is $verifyToken ,Thanks for using our services",
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        Toastr::success('!Done', 'We send a OTP in your phone');

        return redirect('merchant/phone-verify');

    }

    public function phoneVerify(Request $request) {
        $this->validate($request, [
            'verifyToken' => 'required',
        ]);
        $verified = Merchant::where('phoneNumber', Session::get('phoneverify'))->first();
        // dd($verified);
        $verifydbtoken   = $verified->verifyToken;
        $verifyformtoken = $request->verifyToken;

        if ($verifydbtoken == $verifyformtoken) {
            $verified->verifyToken = 1;
            $verified->status      = 1;
            $verified->save();
            Session::put('merchantId', $verified->id);
            Session::forget('phoneverify');
            Toastr::success('Your account is verified', 'success!');

            return redirect('merchant/dashboard');
        } else {
            Toastr::error('sorry your verify token wrong', 'Opps!');

            return redirect()->back();
        }

    }

    // Merchant Login Function End

    public function dashboard() {
        $data = [];
        //this month
        $data['m_pending']         = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 1)->count();
        $data['m_pick']            = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 2)->count();
        $data['m_await']           = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 3)->count();
        $data['m_deliver']         = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 4)->count();
        $data['m_partial_deliver'] = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 6)->count();
        $data['m_return']          = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 8)->count();
        $data['m_da']              = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 10)->count();
        $data['m_hold']            = Parcel::where('merchantId', Session::get('merchantId'))->whereYear('updated_at', now())->whereMonth('updated_at', now())->where('status', 5)->count();
        $data['m_wallet']          = RemainTopup::where('merchant_id', Session::get('merchantId'))->sum('amount');

        //total
        $data['t_pending']         = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 1)->count();
        $data['t_pick']            = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 2)->count();
        $data['t_await']           = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 3)->count();
        $data['t_deliver']         = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 4)->count();
        $data['t_partial_deliver'] = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 6)->count();
        $data['t_return']          = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 8)->count();
        $data['t_da']              = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 10)->count();
        $data['t_hold']            = Parcel::where('merchantId', Session::get('merchantId'))->where('status', 5)->count();

        $data['parcels'] = Parcel::where('merchantId', Session::get('merchantId'))->orderBy('updated_at', 'DESC')->limit(50)->with('merchant', 'parcelnote')
            ->get();

        $data['notice'] = Disclamer::find(1);

        $data['merchant'] = Merchant::find(Session::get('merchantId'));

        return view('frontEnd.layouts.pages.merchant.dashboard', $data);
    }

    // Merchant Dashboard
    public function profile() {
        $profileinfos = Merchant::all();

        return view('frontEnd.layouts.pages.merchant.profile', compact('profileinfos'));

    }

    public function profileEdit() {
        $profileinfos = Merchant::all();
        $nearestzones = Nearestzone::where('status', 1)->get();

        return view('frontEnd.layouts.pages.merchant.profileedit', compact('nearestzones'));

    }

    public function support() {
        return view('frontEnd.layouts.pages.merchant.support');
    }

    // Merchant Profile Edit
    public function profileUpdate(Request $request) {
        $update_merchant = Merchant::find(Session::get('merchantId'));

        $update_image = $request->file('logo');

        if ($update_image) {
            $file       = $request->file('logo');
            $name       = $file->getClientOriginalName();
            $uploadPath = 'uploads/merchant/';
            File::delete(public_path() . 'uploads/merchant', $update_merchant->logo);
            $file->move($uploadPath, $name);
            $fileUrl = $uploadPath . $name;
        } else {
            $fileUrl = $update_merchant->logo;
        }

        $update_merchant->logo             = $fileUrl;
        $update_merchant->phoneNumber      = $request->phoneNumber;
        $update_merchant->pickLocation     = $request->pickLocation;
        $update_merchant->nearestZone      = $request->nearestZone;
        $update_merchant->pickupPreference = $request->pickupPreference;
        $update_merchant->paymentMethod    = $request->paymentMethod;
        $update_merchant->withdrawal       = $request->withdrawal;
        $update_merchant->nameOfBank       = $request->nameOfBank;
        $update_merchant->beneficiary_bank_code       = $request->beneficiary_bank_code;
        $update_merchant->bankBranch       = $request->bankBranch;
        $update_merchant->bankAcHolder     = $request->bankAcHolder;
        $update_merchant->bankAcNo         = $request->bankAcNo;
        $update_merchant->bkashNumber      = $request->bkashNumber;
        $update_merchant->roketNumber      = $request->roketNumber;
        $update_merchant->nogodNumber      = $request->nogodNumber;
        $update_merchant->save();

        return redirect()->back()->with('success', 'Your account update successfully');
    }

    // Merchant Profile Update
    public function logout() {
        Session::flush();
        Toastr::success('Success!', 'Thanks! you are logout successfully');

        return redirect('/merchant/login');
    }

// Merchant Logout

    //Parcel Oparation
    public function parcelcreate() {
        $packages = Deliverycharge::where('status', 1)->get();
        Session::forget('codpay');
        Session::forget('pdeliverycharge');
        Session::forget('pcodecharge');

        return view('frontEnd.layouts.pages.merchant.parcelcreate', compact('packages'));
    }

    public function parcelstore(Request $request) {
        $this->validate($request, [
            'percelType'  => 'required',
            'name'        => 'required',
            'address'     => 'required',
            'phonenumber' => 'required',
            'productName' => 'required',
            'productQty'  => 'required',
            'cod'          => 'required',
            'payment_option'=> 'required',
            'weight'        => 'required',
            'note'          => 'required',
            'reciveZone'    => 'required',
            'package'       => 'required'
        ]);

        $state = Deliverycharge::find($request->package);
        $area  = Nearestzone::find($request->reciveZone);
        if ($request->weight > 1 || $request->weight != NULL) {
            $extraweight    = $request->weight - 1;
            $deliverycharge = $state->deliverycharge + $area->extradeliverycharge + ($extraweight * $state->extradeliverycharge);
            $weight         = $request->weight;
        } else {
            $deliverycharge = $state->deliverycharge + $area->extradeliverycharge;
            $weight         = 1;
        }

        if ($request->payment_option == 2) {
            $state = Deliverycharge::find($request->package);

            if ($state) {
                $codcharge = ($request->cod * $state->cod) / 100;
            } else {
                $codcharge = 0;
            }

            $merchantAmount = ($request->cod) - ($deliverycharge + $codcharge);
            $merchantDue    = ($request->cod) - ($deliverycharge + $codcharge);

        } else {
            $merchant = Merchant::find(Session::get('merchantId'));

            if ($merchant->balance < $deliverycharge) {
                session()->flash('message','Wallet Balance is low. Please
                top up.');

                return redirect()->back();
            }

            $merchant->balance = $merchant->balance - $deliverycharge;
            $merchant->save();
            $codcharge      = 0;
            $merchantAmount = 0;
            $merchantDue    = 0;
        }


        $store_parcel                   = new Parcel();
        $store_parcel->invoiceNo        = $request->invoiceno;
        $store_parcel->merchantId       = Session::get('merchantId');
        $store_parcel->cod              = $request->cod;
        $store_parcel->percelType       = $request->percelType;
        $store_parcel->payment_option   = $request->payment_option;
        $store_parcel->recipientName    = $request->name;
        $store_parcel->recipientAddress = $request->address;
        $store_parcel->recipientPhone   = $request->phonenumber;
        $store_parcel->productWeight    = $weight;
        $store_parcel->trackingCode     = 'ZD' . mt_rand(111111, 999999);
        $store_parcel->note             = $request->note;
        $store_parcel->deliveryCharge   = $deliverycharge;
        $store_parcel->codCharge        = $codcharge;
        $store_parcel->reciveZone       = $request->reciveZone;
        $store_parcel->productPrice     = $request->productPrice;
        $store_parcel->productName      = $request->productName;
        $store_parcel->productQty       = $request->productQty;
        $store_parcel->productColor     = $request->productColor;
        $store_parcel->merchantAmount   = $merchantAmount;
        $store_parcel->merchantDue      = $merchantDue;
        $store_parcel->orderType        = $request->package;
        $store_parcel->codType          = 1;
        $store_parcel->status           = 1;
        $store_parcel->save();

        if ($request->payment_option == 1) {
            RemainTopup::create([
                'parcel_id'     => $store_parcel->id,
                'parcel_status' => 1,
                'merchant_id'   => $merchant->id,
                'amount'        => $deliverycharge,
            ]);
        }

        $note           = new Parcelnote();
        $note->parcelId = $store_parcel->id;
        $note->note     = 'parcel create successfully';

//  $note->save();

// $data = array(

//     'trackingCode' =>  $store_parcel->trackingCode,

//     'subject' => 'New Parcel Place',

//    );

//     // return $data;

//     $send = Mail::send('frontEnd.emails.parcelplace', $data, function($textmsg) use ($data){

//     $textmsg->to('contact@8809612440738.com.bd');

//     $textmsg->subject($data['subject']);
        //    });

        Toastr::success('Success!', 'Thanks! your parcel add successfully');

        session()->flash('open_url', url('/merchant/parcel/invoice/' . $store_parcel->id));
        return redirect()->back();
    }

    public function pickuprequest(Request $request) {
        $this->validate($request, [
            'pickupAddress' => 'required',
        ]);

        $date       = date('Y-m-d');
        $findpickup = Pickup::where('date', $date)->Where('merchantId', Session::get('merchantId'))->count();

        if ($findpickup) {
            Toastr::error('Opps!', 'Sorry! your pickup request already pending');

            return redirect()->back();
        } else {
            $store_pickup                = new Pickup();
            $store_pickup->merchantId    = Session::get('merchantId');
            $store_pickup->pickuptype    = $request->pickuptype;
            $store_pickup->area          = $request->area??1;
            $store_pickup->pickupAddress = $request->pickupAddress;
            $store_pickup->note          = $request->note;
            $store_pickup->date          = $date;
            $store_pickup->estimedparcel = $request->estimedparcel;
            $store_pickup->save();
            Toastr::success('Success!', 'Thanks! your pickup request send  successfully');

            try {
                $merchant = Merchant::find(Session::get('merchantId'));
                Mail::to([
                    'e-tailing@zidrop.com'
                ])->send(new NewPickupRequestEmail($merchant, $store_pickup));

            } catch (\Exception $exception) {
                Log::info('New Pickup Request Mail Error: '.$exception->getMessage());
            }

            return redirect()->back();
        }

    }

    public function pickup() {
        $show_data = DB::table('pickups')
            ->where('pickups.merchantId', Session::get('merchantId'))
            ->orderBy('pickups.id', 'DESC')
            ->select('pickups.*')
            ->get();
        $deliverymen = Deliveryman::where('status', 1)->get();

        return view('frontEnd.layouts.pages.merchant.pickup', compact('show_data', 'deliverymen'));
    }

    public function parcels(Request $request) {
        $filter = $request->filter_id;

        if ($request->trackId != NULL) {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->where('parcels.trackingCode', $request->trackId)
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('id', 'DESC')
                ->get();
        } elseif ($request->phoneNumber != NULL) {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->where('parcels.recipientPhone', $request->phoneNumber)
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('id', 'DESC')
                ->get();
        } elseif ($request->startDate != NULL && $request->endDate != NULL) {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->whereBetween('parcels.created_at', [$request->startDate, $request->endDate])
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('id', 'DESC')
                ->get();
        } elseif ($request->phoneNumber != NULL || $request->phoneNumber != NULL && $request->startDate != NULL && $request->endDate != NULL) {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->where('parcels.recipientPhone', $request->phoneNumber)
                ->whereBetween('parcels.created_at', [$request->startDate, $request->endDate])
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('id', 'DESC')
                ->get();
        }

        return view('frontEnd.layouts.pages.merchant.parcels', compact('allparcel'));
    }

    public function parcelstatus($slug) {
        $parceltype = Parceltype::where('slug', $slug)->first();

        if (request()->month) {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->where('parcels.status', $parceltype->id)
                ->whereMonth('parcels.updated_at', now())
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('updated_at', 'DESC')
                ->get();
        } else {
            $allparcel = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->where('parcels.merchantId', Session::get('merchantId'))
                ->where('parcels.status', $parceltype->id)
                ->select('parcels.*', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->orderBy('id', 'DESC')
                ->get();
        }

        return view('frontEnd.layouts.pages.merchant.allparcel', compact('allparcel'));
    }

    public function parceldetails($id) {
        $parceldetails = DB::table('parcels')
            ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
            ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
            ->where(['parcels.merchantId' => Session::get('merchantId'), 'parcels.id' => $id])
            ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename')
            ->first();
        $trackInfos = Parcelnote::where('parcelId', $id)->orderBy('id', 'ASC')->with('notes')->get();

        return view('frontEnd.layouts.pages.merchant.parceldetails', compact('parceldetails', 'trackInfos'));
    }

    public function invoice($id) {
        $show_data = DB::table('parcels')
            ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
            ->where(['parcels.merchantId' => Session::get('merchantId'), 'parcels.id' => $id])
            ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
            ->where('parcels.id', $id)
            ->join('deliverycharges', 'deliverycharges.id', '=', 'nearestzones.state')
            ->select('parcels.*','deliverycharges.title', 'nearestzones.zonename','nearestzones.state', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
            ->first();

        if ($show_data != NULL) {
            return view('frontEnd.layouts.pages.merchant.invoice', compact('show_data'));
        } else {
            Toastr::error('Opps!', 'Your process wrong');

            return redirect()->back();
        }

    }

    public function parceledit($id) {
        $parceledit = Parcel::where(['merchantId' => Session::get('merchantId'), 'id' => $id])->first();

        if ($parceledit != NULL) {
            $ordertype = Deliverycharge::find($parceledit->orderType);
            $codcharge = Codcharge::find($parceledit->codType);
            $areas     = Nearestzone::where('status', 1)->get();
            Session::put('codpay', $parceledit->cod);
            Session::put('pcodecharge', $parceledit->codCharge);
            Session::put('pdeliverycharge', $parceledit->deliveryCharge);

            return view('frontEnd.layouts.pages.merchant.parceledit', compact('ordertype', 'codcharge', 'parceledit', 'areas'));
        } else {
            Toastr::error('Opps!', 'Your process wrong');

            return redirect()->back();
        }

    }

    public function parcelupdate(Request $request) {
        $this->validate($request, [
            'percelType'  => 'required',
            'name'        => 'required',
            'address'     => 'required',
            'phonenumber' => 'required',
            'productName' => 'required',
            'productQty'  => 'required',
            'cod'          => 'required',
            'payment_option'=> 'required',
            'weight'        => 'required',
            'note'          => 'required',
            'reciveZone'    => 'required',
            'package'       => 'required'
        ]);

// fixed delivery charge
        $extradeliverycharge = Nearestzone::find($request->reciveZone);

        if ($request->weight > 1 || $request->weight != NULL) {
            $extraweight    = $request->weight - 1;
            $deliverycharge = (Session::get('deliverycharge') * 1) + ($extraweight * Session::get('extradeliverycharge')) + $extradeliverycharge->extradeliverycharge;
            $weight         = $request->weight;
        } else {
            $deliverycharge = (Session::get('deliverycharge')) + $extradeliverycharge->extradeliverycharge;
            $weight         = 1;
        }

        $state = Deliverycharge::find($request->package);

        if ($state) {
            $codcharge = ($request->cod * $state->cod) / 100;
        } else {
            $codcharge = 0;
        }

// $codtype = Merchantcharge::where(['merchantId' => Session::get('merchantId'), 'packageId' => $request->package])->first();

// if ($codtype->codpercent == 1) {

//     $codcharge = ($request->cod * $codtype->cod) / 100;

// } else {

//     $codcharge = $codtype->cod;
        // }

        $update_parcel                   = Parcel::find($request->hidden_id);
        $update_parcel->invoiceNo        = $request->invoiceno;
        $update_parcel->merchantId       = Session::get('merchantId');
        $update_parcel->cod              = $request->cod;
        $update_parcel->percelType       = $request->percelType;
        $update_parcel->recipientName    = $request->name;
        $update_parcel->recipientAddress = $request->address;
        $update_parcel->recipientPhone   = $request->phonenumber;
        $update_parcel->productWeight    = $weight;
        $update_parcel->note             = $request->note;
        $update_parcel->reciveZone       = $request->reciveZone;
        $update_parcel->deliveryCharge   = $deliverycharge;
        $update_parcel->codCharge        = $codcharge;
        $update_parcel->merchantAmount   = ($request->cod) - ($deliverycharge + $codcharge);
        $update_parcel->merchantDue      = ($request->cod) - ($deliverycharge + $codcharge);
        $update_parcel->orderType        = $request->package;
        $update_parcel->codType          = 1;
        $update_parcel->save();
        Toastr::success('Success!', 'Thanks! your parcel update successfully');

        return redirect()->back();
    }

    public function singleservice(Request $request) {
        $data = [
            'contact_mail' => 'info@8809612440738.com.bd',
            'address'      => $request->address,
            'area'         => $request->area,
            'note'         => $request->note,
            'estimate'     => $request->estimate,
        ];
        $send = Mail::send('frontEnd.emails.singleservice', $data, function ($textmsg) use ($data) {
            $textmsg->to($data['contact_mail']);
            $textmsg->subject('A Single Service Request');
        });
        Toastr::success('Success!', 'Thanks! your  request send successfully');

        return redirect()->back();
    }

    public function payments() {
        /*$merchantInvoice =DB::table('merchantpayments')
            ->join('parcels','parcels.id','merchantpayments.parcelId')
            ->selectRaw('count(merchantpayments.id) as total_parcel,sum(parcels.merchantPaid) as total, merchantpayments.updated_at, merchantpayments.parcelId')
            ->groupBy('merchantpayments.updated_at')
            ->where('merchantpayments.merchantId', Session::get('merchantId'))
            ->orderBy('updated_at', 'DESC')
            ->get();*/

        $merchantInvoice = DB::table('merchantpayments')
            ->join('parcels','parcels.id','merchantpayments.parcelId')
            ->where('merchantpayments.merchantId', Session::get('merchantId'))
            ->groupBy(['updated_at'])
            ->selectRaw('DATE(merchantpayments.updated_at) as date, count(merchantpayments.id) as total_parcel,sum(parcels.merchantPaid) as total, merchantpayments.updated_at, merchantpayments.parcelId, merchantpayments.merchantId')
            ->orderBy('updated_at', 'DESC')
            ->get();


        return view('frontEnd.layouts.pages.merchant.payments', compact('merchantInvoice'));
    }

    public function inovicedetails(Request $request) {
        $update = $request->update;
        $parcelId = Merchantpayment::where('updated_at', $update)
            ->where('merchantId', Session::get('merchantId'))
            ->pluck('parcelId')
            ->toArray();
        $parcels   = DB::table('parcels')->whereIn('id', $parcelId)->get();
        $merchantInfo = Merchant::find($parcels->first()->merchantId);
        return view('frontEnd.layouts.pages.merchant.inovicedetails', compact('parcels','merchantInfo'));
    }

    public function passreset() {
        return view('frontEnd.layouts.pages.passreset');
    }

    public function Oldpassfromreset(Request $request) {
        $this->validate($request, [
            'email' => 'required',
        ]);
        $validMerchant = Merchant::Where('emailAddress', $request->phoneNumber)
            ->first();

        if ($validMerchant) {

            $verifyToken                  = rand(111111, 999999);
            $validMerchant->passwordReset = $verifyToken;
            $validMerchant->save();
            Session::put('resetCustomerId', $validMerchant->id);

//  $data = array(

//  'contact_mail' => $validMerchant->phoneNumber,

//  'verifyToken' => $verifyToken,

// );

// $send = Mail::send('frontEnd.emails.passwordreset', $data, function($textmsg) use ($data){

//  $textmsg->from('info@8809612440738.com.bd');

//  $textmsg->to($data['contact_mail']);

//  $textmsg->subject('Forget password token');

// });

//   $url = "http://premium.mdlsms.com/smsapi";

//   $data = [

//     "api_key" => "C20005455f867568bd8c02.20968541",

//     "type" => "text",

//     "contacts" => $validMerchant->phoneNumber,

//     "senderid" => "8809612440738",

//     "msg" => "Dear $validMerchant->firstName, \r\n Your password reset token is $verifyToken. Enjoy our services. If any query call us 01711132240\r\nRegards\r\nZuri Express ",

//   ];

//   $ch = curl_init();

//   curl_setopt($ch, CURLOPT_URL, $url);

//   curl_setopt($ch, CURLOPT_POST, 1);

//   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//   $response = curl_exec($ch);
            //   curl_close($ch);

            $url  = "https://sms.solutionsclan.com/api/sms/send";
            $data = [
                "apiKey"         => "A00003133467cc6-219a-4dfd-882e-0bcf8836ebc3",
                "contactNumbers" => $request->phoneNumber,
                "senderId"       => "8809612440632",
                "textBody"       => "Dear $validMerchant->firstName, \r\n Your password reset token is $verifyToken. Enjoy our services. If any query call us 01711132240\r\nRegards\r\nZuri Express ",
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch);
            echo "$response";
            curl_close($ch);

            return redirect('/merchant/resetpassword/verify');
        } else {
            Toastr::error('Sorry! You have no account', 'warning!');

            return redirect()->back();
        }

    }

    public function passfromreset(Request $request) {
        $this->validate($request, [
            'email' => 'required',
        ]);
        $validMerchant = Merchant::Where('emailAddress', $request->email)
            ->first();

        if ($validMerchant) {

            $verifyToken                  = rand(111111, 999999);
            $validMerchant->passwordReset = $verifyToken;
            $validMerchant->save();
            Session::put('resetCustomerId', $validMerchant->id);

            try {
                Mail::to($validMerchant->emailAddress)->send(new MerchantResetPasswordEmail($validMerchant));
            } catch (\Exception $exception) {
                Log::info('Merchant Forget password mail error: '.$exception->getMessage());
            }

            return redirect('/merchant/resetpassword/verify');
        } else {
            Toastr::error('Sorry! You have no account', 'warning!');

            return redirect()->back();
        }

    }

    public function resetpasswordverify() {

        if (Session::get('resetCustomerId')) {
            return view('frontEnd.layouts.pages.passwordresetverify');
        } else {
            Toastr::error('Sorry! Your process something wrong', 'warning!');

            return redirect('forget/password');
        }

    }

    public function saveResetPassword(Request $request) {
        $validMerchant = Merchant::find(Session::get('resetCustomerId'));

        if ($validMerchant->passwordReset == $request->verifyPin) {
            $validMerchant->password      = bcrypt(request('newPassword'));
            $validMerchant->passwordReset = NULL;
            $validMerchant->save();

            Session::forget('resetCustomerId');
            Session::put('merchantId', $validMerchant->id);
            Toastr::success('Wow! Your password reset successfully', 'success!');

            return redirect('/merchant/dashboard');
        } else {
            Toastr::error('Sorry! Your process something wrong', 'warning!');

            return redirect()->back();
        }

    }

    public function parceltrack(Request $request) {
        $trackparcel = DB::table('parcels')
            ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
            ->where('parcels.trackingCode', 'LIKE', '%' . $request->trackid . "%")
            ->select('parcels.*', 'nearestzones.zonename')
            ->orderBy('id', 'DESC')
            ->first();

        if ($trackparcel) {
            $trackInfos = Parcelnote::where('parcelId', $trackparcel->id)->orderBy('id', 'ASC')->with('notes')->get();

            return view('frontEnd.layouts.pages.merchant.trackparcel', compact('trackparcel', 'trackInfos'));
        } else {
            return redirect()->back();
        }

    }

    public function import(Request $request) {
        Excel::import(new ParcelImport(), request()->file('excel'));
        Toastr::success('Wow! Bulk uploaded', 'success!');

        return redirect()->back();
    }

    public function export(Request $request) {
        return Excel::download(new ParcelExport(), 'parcel.xlsx');

    }

    public function index() {
        return view('frontEnd.layouts.pages.merchant.changepass');
    }

    public function changepassword(Request $request) {
        $this->validate($request, [
            'old_password'          => 'required',
            'new_password'          => 'required',
            'password_confirmation' => 'required_with:new_password|same:new_password|',
        ]);

        $user     = Merchant::find(Session::get('merchantId'));
        $hashPass = $user->password;

        if (Hash::check($request->old_password, $hashPass)) {

            $user->fill([
                'password' => Hash::make($request->new_password),
            ])->save();

            Toastr::success('message', 'Password changed successfully!');

            return back();
        } else {
            Toastr::error('message', 'Old password not match!');

            return back();
        }

    }

}
