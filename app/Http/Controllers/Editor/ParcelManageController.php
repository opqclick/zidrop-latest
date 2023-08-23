<?php

namespace App\Http\Controllers\Editor;
use App\Agent;
use App\Codcharge;
use App\Deliverycharge;
use App\Deliveryman;
use App\Exports\MerchantPaymentExport;
use App\History;
use App\Http\Controllers\Controller;
use App\Mail\ParcelStatusUpdateEmail;
use App\Merchant;
use App\Merchantpayment;
use App\Nearestzone;
use App\Parcel;
use App\Parcelnote;
use App\Parceltype;
use App\RemainTopup;
use Auth;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel as MatwebsiteExcel;

class ParcelManageController extends Controller {

    public function merchantpaymentlist() {

        if (request()->filter_id == 1) {
            $show_data = Merchant::select(['id', 'companyName', 'paymentMethod'])->with(['parcels' => function ($query) {
                return $query->whereDate('updated_at', '>=', request()->startDate)->whereDate('updated_at', '<=', request()->endDate)->get();
            },
            ])->get();
        } else {

            $show_data = Merchant::select(['id', 'companyName', 'paymentMethod'])->with(['parcels' => function ($query) {
                return $query->get();
            },
            ])->get();
        }

        return view('backEnd.parcel.merchantpayment', compact('show_data'));
    }

    public function exportMerchantPaymentList()
    {
        $selected_merchants = json_decode(request()->merchants);
        if (request()->filter_id == 1) {
            $show_data = Merchant::with(['parcels' => function ($query) {
                return $query->whereDate('updated_at', '>=', request()->startDate)->whereDate('updated_at', '<=', request()->endDate)->get();
            },
            ])
                ->whereIn('id', $selected_merchants)
                ->get();
        } else {

            $show_data = Merchant::with(['parcels' => function ($query) {
                return $query->get();
            },
            ])
                ->whereIn('id', $selected_merchants)
                ->get();
        }
        if(request()->type == 'csv') {
            return MatwebsiteExcel::download(new MerchantPaymentExport($show_data), 'bulk-transfer- ZiDrop Merchant Payment - ' . Carbon::now()->format('Y-m-d') . '.csv');
        } else {
            return MatwebsiteExcel::download(new MerchantPaymentExport($show_data), 'bulk-transfer- ZiDrop Merchant Payment - ' . Carbon::now()->format('Y-m-d') . '.xlsx');
        }

    }

    public function merchantreturnlist() {
        $parceltype = Parceltype::where('slug', 'return-to-merchant')->first();
        $marchents  = Merchant::select(['id', 'companyName', 'paymentMethod'])->with(['parcels' => function ($query) use ($parceltype) {
            return $query->where('status', '=', $parceltype->id)->where('deliveryCharge', '>', 0)->where('pay_return', 0);
        },
        ])->get();

        foreach ($marchents as $parcel) {
            $charge = 0;

            if (count($parcel->parcels) > 0 && $parcel->pay_return == 0) {

                foreach ($parcel->parcels as $p) {
                    $charge += $p->deliveryCharge;
                }

                $parcel['charge'] = $charge;
            }

        }

        $marchents = $marchents->where('charge', '>', 0);
        // dd($marchents->toArray());

        return view('backEnd.parcel.merchantReturnPayment', compact('marchents'));
    }

    public function merchantconfirmpayment(Request $request) {
        /*
        if ($request->startDate && $request->endDate) {
        $parcels = Parcel::whereIn('merchantId', $request->parcel_id)->whereDate('updated_at', '>=', request()->startDate)
        ->whereDate('updated_at', '<=', request()->endDate)->where('merchantpayStatus', null)->where('status', 4)->get();

        foreach ($parcels as $parcel) {
        if ($parcel->status == 4 || $parcel->status == 6 || $parcel->status == 10) {
        $due                       = $parcel->merchantDue;
        $parcel->merchantDue       = 0;
        $parcel->merchantpayStatus = 1;
        $parcel->merchantPaid      = $due;
        $parcel->save();

        $payment = new Merchantpayment();
        $payment->merchantId = $parcel->merchantId;
        $payment->parcelId = $parcel->id;
        $payment->done_by = auth()->user()->name;
        $payment->save();
        }
        }

        } else {
        $parcels = Parcel::whereIn('merchantId', $request->parcel_id)->where('merchantpayStatus', null)->where('status', 4)->get();

        foreach ($parcels as $parcel) {
        if ($parcel->status == 4 || $parcel->status == 6 || $parcel->status == 10) {
        $due                       = $parcel->merchantDue;
        $parcel->merchantDue       = 0;
        $parcel->merchantpayStatus = 1;
        $parcel->merchantPaid      = $due;
        $parcel->save();

        $payment = new Merchantpayment();
        $payment->merchantId = $parcel->merchantId;
        $payment->parcelId = $parcel->id;
        $payment->done_by = auth()->user()->name;
        $payment->save();
        }
        }

        }
         */

        $parcels = Parcel::whereIn('merchantId', $request->parcel_id)->where('merchantpayStatus', null)->get();

        foreach ($parcels as $parcel) {

            if ($parcel->status == 4 || $parcel->status == 6 || $parcel->status == 10) {
                $due                       = $parcel->merchantDue;
                $parcel->merchantDue       = 0;
                $parcel->merchantpayStatus = 1;
                $parcel->merchantPaid      = $due;
                $parcel->save();

                $payment             = new Merchantpayment();
                $payment->merchantId = $parcel->merchantId;
                $payment->parcelId   = $parcel->id;
                $payment->done_by    = auth()->user()->name;
                $payment->save();
            }

        }

        Toastr::success('message', 'Merchant Due Paid.');

        return back();

    }

    public function merchantPaymentDetails($id) {
        $data             = [];
        $data['merchant'] = Merchant::where('id', $id)->with('parcels')->first();

        return view('backEnd.parcel.mercant-payment-details', $data);
    }

    public function parcel(Request $request) {
        $parceltype = Parceltype::where('slug', $request->slug)->first();
        if ($request->slug == 'return-to-merchant') {
            $canEdit = false;
        } else {
            $canEdit = true;
        }
        if ($request->trackId != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.trackingCode', $request->trackId)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->merchantId != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.merchantId', $request->merchantId)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->cname != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.recipientName', $request->cname)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->address != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.recipientAddress', $request->address)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->phoneNumber != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.recipientPhone', $request->phoneNumber)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->startDate != NULL && $request->endDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->whereBetween('parcels.created_at', [Carbon::parse($request->startDate)->startOfDay(), Carbon::parse($request->endDate)->endOfDay()])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->upstartDate != NULL && $request->upendDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->whereBetween('parcels.updated_at', [Carbon::parse($request->upstartDate)->startOfDay(), Carbon::parse($request->upendDate)->endOfDay()])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->phoneNumber != NULL && $request->startDate != NULL && $request->endDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.recipientPhone', $request->phoneNumber)
                ->whereBetween('parcels.created_at', [$request->startDate, $request->endDate])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->merchantId != NULL && $request->startDate != NULL && $request->endDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->where('parcels.merchantId', $request->merchantId)
                ->whereBetween('parcels.created_at', [$request->startDate, $request->endDate])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } else {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.status', $parceltype->id)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(50);
        }

        $agents = DB::table('agents')->get();

        return view('backEnd.parcel.parcel', compact('show_data', 'parceltype', 'canEdit', 'agents'));
    }

    public function allparcel(Request $request) {

        if ($request->trackId != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.trackingCode', $request->trackId)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->merchantId != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.merchantId', $request->merchantId)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->cname != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.recipientName', $request->cname)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->address != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.recipientAddress', $request->address)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->phoneNumber != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.recipientPhone', $request->phoneNumber)
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->startDate != NULL && $request->endDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->whereBetween('parcels.created_at', [Carbon::parse($request->startDate)->startOfDay(), Carbon::parse($request->endDate)->endOfDay()])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->upstartDate != NULL && $request->upendDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->whereBetween('parcels.updated_at', [Carbon::parse($request->upstartDate)->startOfDay(), Carbon::parse($request->upendDate)->endOfDay()])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->phoneNumber != NULL && $request->startDate != NULL && $request->endDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.recipientPhone', $request->phoneNumber)
                ->whereBetween('parcels.created_at', [$request->startDate, $request->endDate])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } elseif ($request->merchantId != NULL && $request->startDate != NULL && $request->endDate != NULL) {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->where('parcels.merchantId', $request->merchantId)
                ->whereBetween('parcels.created_at', [$request->startDate, $request->endDate])
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(500);
        } else {
            $show_data = DB::table('parcels')
                ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
                ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
                ->join('deliverycharges', 'deliverycharges.id', '=', 'parcels.orderType')
                ->orderBy('id', 'DESC')
                ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
                ->paginate(50);
        }

        //dd($show_data);

        return view('backEnd.parcel.allparcel', compact('show_data'));
    }

    public function parceldelete(Request $request, $id) {
        $parcel = Parcel::findOrFail($id);
        $parcel->delete();
        Toastr::success('message', 'Parcel deleted successfully!');

        return redirect()->back();
    }

    public function invoice($id) {
        $show_data = DB::table('parcels')
            ->join('merchants', 'merchants.id', '=', 'parcels.merchantId')
            ->join('nearestzones', 'parcels.reciveZone', '=', 'nearestzones.id')
            ->where('parcels.id', $id)
            ->join('deliverycharges', 'deliverycharges.id', '=', 'nearestzones.state')
            ->select('parcels.*', 'deliverycharges.title', 'nearestzones.zonename', 'nearestzones.state', 'merchants.firstName', 'merchants.lastName', 'merchants.phoneNumber', 'merchants.emailAddress', 'merchants.companyName', 'merchants.status as mstatus', 'merchants.id as mid')
            ->first();

        return view('backEnd.parcel.invoice', compact('show_data'));
    }

    public function agentasign(Request $request) {
        $this->validate($request, [
            'agentId' => 'required',
        ]);
        $parcel          = Parcel::find($request->hidden_id);
        $parcel->agentId = $request->agentId;
        $parcel->save();

        //Save to History table

        $pstatus = Parceltype::find($parcel->status);

        $pstatus = $pstatus->title;

        $agentInfo = Agent::find($parcel->agentId);

        $history            = new History();
        $history->name      = "Customer: " . $parcel->recipientName . "<br><b>(Agent: )</b>" . $agentInfo->name;
        $history->parcel_id = $request->hidden_id;
        $history->done_by   = auth()->user()->name;
        $history->status    = '';
        $history->note      = $request->note;
        $history->date      = $parcel->updated_at;
        $history->save();

        // agent commision
        $agentInfo = Agent::find($parcel->agentId);

        if ($agentInfo->commisiontype == 1) {
            $commision = $agentInfo->commision;
        } else {
            $commision = ($agentInfo->commision * $parcel->deliveryCharge) / 100;
        }

        $parcel->agentAmount = $commision;
        $parcel->save();

        if ($request->note) {
            $note           = new Parcelnote();
            $note->parcelId = $request->hidden_id;
            $note->note     = $request->note;
            $note->save();
        }

        Toastr::success('message', 'A agent asign successfully!');

        return redirect()->back();
    }

    public function pickupmanasign(Request $request) {
        $this->validate($request, [
            'pickupmanId' => 'required',
        ]);
        $parcel              = Parcel::find($request->hidden_id);
        $parcel->pickupmanId = $request->pickupmanId;
        $parcel->save();

        $pstatus = Parceltype::find($request->status);
        $pstatus = "Same as previous status.";

        //Save to History table

        $pstatus = Parceltype::find($parcel->status);

        $pstatus = $pstatus->title;

        $deliverymanInfo = Deliveryman::find($parcel->pickupmanId);

        $history            = new History();
        $history->name      = "Customer: " . $parcel->recipientName . "<br><b>(Pickupman: )</b>" . $deliverymanInfo->name;
        $history->parcel_id = $request->hidden_id;
        $history->done_by   = auth()->user()->name;
        $history->status    = '';
        $history->note      = $request->note;
        $history->date      = $parcel->updated_at;
        $history->save();

        if ($request->note) {
            $note           = new Parcelnote();
            $note->parcelId = $request->hidden_id;
            $note->note     = $request->note;
            $note->save();
        }

        Toastr::success('message', 'A Pickupman asign successfully!');

        return redirect()->back();
        $deliverymanInfo = Deliveryman::find($parcel->pickupmanId);

    }

    public function deliverymanasign(Request $request) {
        $this->validate($request, [
            'deliverymanId' => 'required',
        ]);
        $parcel                = Parcel::find($request->hidden_id);
        $parcel->deliverymanId = $request->deliverymanId;
        $parcel->status        = 3;
        $parcel->save();

        // agent commision
        $deliverymanInfo = Deliveryman::find($parcel->deliverymanId);

        if ($deliverymanInfo->commisiontype == 1) {
            $commision = $deliverymanInfo->commision;
        } else {
            $commision = ($deliverymanInfo->commision * $parcel->deliveryCharge) / 100;
        }

        $parcel->deliverymanAmount = $commision;
        $parcel->save();

        if ($request->note) {
            $note           = new Parcelnote();
            $note->parcelId = $request->hidden_id;
            $note->note     = $request->note;
            $note->save();
        }

        //Save to History table

        $pstatus = Parceltype::find($parcel->status);

        $pstatus = $pstatus->title;

        $deliverymanInfo = Deliveryman::find($request->deliverymanId);

        $history            = new History();
        $history->name      = "Customer: " . $parcel->recipientName . "<br><b>(Deleveryman: )</b>" . $deliverymanInfo->name;
        $history->parcel_id = $request->hidden_id;
        $history->done_by   = auth()->user()->name;
        $history->status    = '';
        $history->note      = $request->note;
        $history->date      = $parcel->updated_at;
        $history->save();

        Toastr::success('message', 'A deliveryman asign successfully!');

        return redirect()->back();
    }

    public function bulkdeliverymanAssign(Request $request) {
    $parcels_id = $request->parcel_id;
    $asigntype  = $request->asigntype;

    if($request->btn == "rider"){
        if ($asigntype == 1) {

            foreach ($parcels_id as $parcel_id) {
                $parcel              = Parcel::find($parcel_id);
                $parcel->pickupmanId = $request->deliverymanId;
                $parcel->save();
            }

        } else {

            foreach ($parcels_id as $parcel_id) {
                $parcel                = Parcel::find($parcel_id);
                $parcel->deliverymanId = $request->deliverymanId;
                $parcel->status        = 3;
                $parcel->save();
            }

        }

        if ($asigntype == 1) {
            $note           = new Parcelnote();
            $note->parcelId = $parcel_id;
            $note->note     = "Pickup Man Asign";
            $note->save();
        } else {
            $note           = new Parcelnote();
            $note->parcelId = $parcel_id;
            $note->note     = "Delivery Man Asign";
            $note->save();
        }

        return redirect()->back();
    }elseif($request->btn == "agent"){

        foreach ($parcels_id as $parcel_id) {
                $parcel                = Parcel::find($parcel_id);
                $parcel->agentId = $request->agentId;
                $parcel->status        = 2;
                $parcel->save();

                $note           = new Parcelnote();
                $note->parcelId = $parcel_id;
                $note->note     = "In Transit To Delivery Facility";
                $note->save();
            }

           

            return redirect()->back();
    }

    }

    public function statusupdate(Request $request) {

        $this->validate($request, [
            'status' => 'required',
        ]);
        $parcel         = Parcel::find($request->hidden_id);
        $parcel->status = $request->status;
        $parcel->updated_at = Carbon::now();
        $parcel->save();

        if ($request->note) {
            $note           = new Parcelnote();
            $note->parcelId = $request->hidden_id;
            $note->note     = $request->note;
            $note->save();
        }

        if ($request->status == 2) {
            $deliverymanInfo = Deliveryman::where(['id' => $parcel->deliverymanId])->first();
            $merchantinfo    = Merchant::find($parcel->merchantId);

            $url  = "http://premium.mdlsms.com/smsapi";
            $data = [
                "api_key"  => "C20005455f867568bd8c02.20968541",
                "type"     => "text",
                "contacts" => "0$parcel->recipientPhone",
                "senderid" => "8809612440738",
                "msg"      => "message",
                // "msg"      => "Dear $parcel->recipientName, We have received your parcel from $merchantinfo->companyName. Your Tracking ID is $parcel->trackingCode. Please click the link to track your parcel:" . url('track/parcel/') . '/' . $parcel->trackingCode . " Thanks for being with Zuri Express.",
            ];
            //   return $data;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

        } elseif ($request->status == 3) {
            // $codcharge=$request->customerpay/100;
            $codcharge              = 0;
            $parcel->merchantAmount = ($parcel->merchantAmount) - ($codcharge);
            $parcel->merchantDue    = ($parcel->merchantAmount) - ($codcharge);
            $parcel->codCharge      = $codcharge;
            $parcel->save();

            $validMerchant = Merchant::find($parcel->merchantId);
            $deliveryMan   = Deliveryman::find($parcel->deliverymanId);
            $readytaka     = $parcel->cod;
            $url           = "http://premium.mdlsms.com/smsapi";
            $data          = [
                "api_key"  => "C20005455f867568bd8c02.20968541",
                "type"     => "text",
                "contacts" => "0$parcel->recipientPhone",
                "senderid" => "8809612440738",
                // "msg"      => "Dear $parcel->recipientName \r\n your parcel is being delivered by $deliveryMan->name phone number 0$deliveryMan->phone.  Please get ready with the cash amount of $readytaka. \r\n Thanks for being with Zuri Express.",
                "msg"      => "message",
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        } elseif ($request->status == 4) {
            // $codcharge=$request->customerpay/100;
            $codcharge              = 0;
            $parcel->merchantAmount = ($parcel->merchantAmount) - ($codcharge);
            $parcel->merchantDue    = ($parcel->merchantAmount) - ($codcharge);
            $parcel->codCharge      = $codcharge;
            $parcel->save();
            $validMerchant = Merchant::find($parcel->merchantId);
            $url           = "http://premium.mdlsms.com/smsapi";
            $data          = [
                "api_key"  => "C20005455f867568bd8c02.20968541",
                "type"     => "text",
                "contacts" => "0$validMerchant->phoneNumber",
                "senderid" => "8809612440738",
                // "msg"      => "Dear $validMerchant->firstName, Your Parcel ID $parcel->trackingCode has been delivered successfully to the customer.\r\n Thanks for being with Zuri Express",
                "msg"      => "message",
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        } elseif ($request->status == 5) {
            // $codcharge=$request->customerpay/100;
            $codcharge              = 0;
            $parcel->merchantAmount = ($parcel->merchantAmount) - ($codcharge);
            $parcel->merchantDue    = ($parcel->merchantAmount) - ($codcharge);
            $parcel->codCharge      = $codcharge;
            $parcel->save();
            $validMerchant = Merchant::find($parcel->merchantId);
            $url           = "http://premium.mdlsms.com/smsapi";
            $data          = [
                "api_key"  => "C20005455f867568bd8c02.20968541",
                "type"     => "text",
                "contacts" => "0$validMerchant->phoneNumber",
                "senderid" => "8809612440738",
                // "msg"      => "Dear $validMerchant->firstName, Your Parcel ID $parcel->trackingCode is on hold. Another attempt will be taken the next day. \r\n Thanks for being with Zuri Express.",
                "msg"      => "message",
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        } elseif ($request->status == 6) {

            if ($parcel->payment_option == 2) {
                $charge      = Deliverycharge::find($parcel->orderType);
                $codcharge   = ($request->partial_payment * $charge->cod) / 100;
                $parcel->cod = $request->partial_payment;

                $amount = $request->partial_payment - ($codcharge + $parcel->deliveryCharge);

                $parcel->merchantAmount = $amount;
                $parcel->merchantDue    = $amount;
                $parcel->codCharge      = $codcharge;
                $parcel->save();

            }

            $validMerchant = Merchant::find($parcel->merchantId);
            $url           = "http://premium.mdlsms.com/smsapi";
            $data          = [
                "api_key"  => "C20005455f867568bd8c02.20968541",
                "type"     => "text",
                "contacts" => "0$validMerchant->phoneNumber",
                "senderid" => "8809612440738",
                // "msg"      => "Dear $validMerchant->firstName, Your Parcel ID $parcel->trackingCode will be return within 48 hours. \r\n Thanks for being with Zuri Express",
                "msg"      => "message",
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        } elseif ($request->status == 8) {

// $returncharge = $parcel->deliveryCharge/2;

// $parcel->merchantAmount=$parcel->merchantAmount-$returncharge;

// $parcel->merchantDue=$parcel->merchantAmount-$returncharge;

// $parcel->deliveryCharge= $parcel->deliveryCharge+$returncharge;
            // $parcel->save();

            $codcharge              = 0;
            $parcel->merchantAmount = ($parcel->merchantAmount) - ($codcharge);
            $parcel->merchantDue    = ($parcel->merchantAmount) - ($codcharge);
            $parcel->codCharge      = $codcharge;
            $parcel->cod            = $codcharge;
            $parcel->save();

        } elseif ($request->status == 9) {

            // $merchantinfo =Merchant::find($parcel->merchantId);

            $codcharge                 = 0;
            $parcel->merchantAmount    = $parcel->merchantAmount + $parcel->codCharge;
            $parcel->merchantDue       = $codcharge;
            $parcel->merchantpayStatus = 1;
            $parcel->merchantPaid      = $parcel->merchantAmount + $parcel->codCharge;
            $parcel->save();

//  $data = array(

//  'contact_mail' => $merchantinfo->emailAddress,

//  'trackingCode' => $parcel->trackingCode,

// );

//  $send = Mail::send('frontEnd.emails.percelcancel', $data, function($textmsg) use ($data){

//  $textmsg->from('info@zuri.express');

//  $textmsg->to($data['contact_mail']);

//  $textmsg->subject('Percel Cancelled Notification');
            // });
        }

        $pstatus = Parceltype::find($request->status);

        $pstatus = $pstatus->title;

        //Save to History table

        $history            = new History();
        $history->name      = $parcel->recipientName;
        $history->parcel_id = $request->hidden_id;
        $history->done_by   = auth()->user()->name;
        $history->status    = $pstatus;
        $history->note      = $request->note;
        $history->date      = $parcel->updated_at;
        $history->save();

        try {
            $validMerchant = Merchant::find($parcel->merchantId);
            if (!empty($validMerchant)) {
                Mail::to([
                    $validMerchant->emailAddress
                ])->send(new ParcelStatusUpdateEmail($validMerchant, $parcel, $history));
            }
        } catch (\Exception $exception) {
            Log::info('Parcel status update mail error: '. $exception->getMessage());
        }

        Toastr::success('message', 'Parcel information update successfully!');

        return redirect()->back();
    }

    public function create() {
        $merchants = Merchant::orderBy('id', 'DESC')->get();
        $delivery  = Deliverycharge::where('status', 1)->get();
        // $packages = Deliverycharge::where('status', 1)->get();

        return view('backEnd.addparcel.create_new', compact('merchants', 'delivery'));
    }

    public function parcelstore(Request $request) {
        // return $request->all();
        $this->validate($request, [
            'percelType'     => 'required',
            'name'           => 'required',
            'address'        => 'required',
            'phonenumber'    => 'required',
            'productName'    => 'required',
            'productQty'     => 'required',
            'cod'            => 'required',
            'payment_option' => 'required',
            'weight'         => 'required',
            'note'           => 'required',
            'reciveZone'     => 'required',
            'package'        => 'required',
        ]);

        $charge = Deliverycharge::find($request->package);
        $area   = Nearestzone::find($request->reciveZone);

        if ($request->weight > 1 || $request->weight != NULL) {
            $extraweight    = $request->weight - 1;
            $deliverycharge = ($charge->deliverycharge + $area->extradeliverycharge) + ($extraweight * $charge->extradeliverycharge);
            $weight         = $request->weight;
        } else {
            $deliverycharge = $charge->deliverycharge + $area->extradeliverycharge;
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
            $merchant = Merchant::find($request->merchantId);

            if ($merchant->balance < $deliverycharge) {
                session()->flash('message', 'Wallet Balance is low. Please
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
        $store_parcel->merchantId       = $request->merchantId;
        $store_parcel->payment_option   = $request->payment_option;
        $store_parcel->percelType       = $request->percelType;
        $store_parcel->cod              = $request->cod;
        $store_parcel->recipientName    = $request->name;
        $store_parcel->recipientAddress = $request->address;
        $store_parcel->recipientPhone   = $request->phonenumber;
        $store_parcel->productWeight    = $weight;
        $store_parcel->productName      = $request->productName;
        $store_parcel->productQty       = $request->productQty;
        $store_parcel->productColor     = $request->productColor;
        $store_parcel->trackingCode     = 'ZD' . mt_rand(111111, 999999);
        $store_parcel->note             = $request->note;
        $store_parcel->deliveryCharge   = $deliverycharge;
        $store_parcel->codCharge        = $codcharge;
        $store_parcel->reciveZone       = $request->reciveZone;
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
                'merchant_id'   => $store_parcel->merchantId,
                'amount'        => $deliverycharge,
            ]);
        }

        $history            = new History();
        $history->name      = "Customer: " . $store_parcel->recipientName . "<br><b>(Created By: )</b>" . auth()->user()->name;
        $history->parcel_id = $request->hidden_id;
        $history->done_by   = auth()->user()->name;
        $history->status    = 'Parcel Created By ' . auth()->user()->name;
        $history->note      = $request->note;
        $history->date      = $store_parcel->updated_at;
        $history->save();

        Toastr::success('Success!', 'Thanks! your parcel add successfully');
        session()->flash('open_url', url('/editor/parcel/invoice/' . $store_parcel->id));
        return redirect()->back();
    }

    public function parceledit($id) {
        $edit_data = Parcel::find($id);
        $merchants = Merchant::orderBy('id', 'DESC')->get();
        $delivery  = Deliverycharge::where('status', 1)->get();

        return view('backEnd.addparcel.edit_new', compact('edit_data', 'merchants', 'delivery'));
    }

    public function parcelupdate(Request $request) {

// return $request->all();
        // dd($request->toArray());
        $this->validate($request, [
            'percelType'     => 'required',
            'name'           => 'required',
            'address'        => 'required',
            'phonenumber'    => 'required',
            'productName'    => 'required',
            'productQty'     => 'required',
            'cod'            => 'required',
            'payment_option' => 'required',
            'weight'         => 'required',
            'note'           => 'required',
            'reciveZone'     => 'required',
        ]);

        $charge = Deliverycharge::find($request->orderType);
        $area   = Nearestzone::find($request->reciveZone);

        if ($request->weight > 1 || $request->weight != NULL) {
            $extraweight    = $request->weight - 1;
            $deliverycharge = ($charge->deliverycharge + $area->extradeliverycharge) + ($extraweight * $charge->extradeliverycharge);
            $weight         = $request->weight;
        } else {
            $deliverycharge = $charge->deliverycharge + $area->extradeliverycharge;
            $weight         = 1;
        }

        if ($request->payment_option == 2) {
            $state = Deliverycharge::find($request->orderType);

            if ($state) {
                $codcharge = ($request->cod * $state->cod) / 100;
            } else {
                $codcharge = 0;
            }

            $merchantAmount = ($request->cod) - ($deliverycharge + $codcharge);
            $merchantDue    = ($request->cod) - ($deliverycharge + $codcharge);
        } else {
            $merchant = Merchant::find($request->merchantId);

            if ($merchant->balance < $deliverycharge) {
                session()->flash('message', 'Wallet Balance is low. Please
                top up.');

                return redirect()->back();
            }

            $merchant->balance = $merchant->balance - $deliverycharge;
            $merchant->save();
            $codcharge      = 0;
            $merchantAmount = 0;
            $merchantDue    = 0;
        }

        // old
        $update_parcel = Parcel::find($request->hidden_id);

        if ($request->payment_option == 1) {
            $merchant = Merchant::find($request->merchantId);

            if ($merchant->balance < $request->deliveryCharge) {
                session()->flash('message', 'Wallet Balance is low. Please
                top up.');

                return redirect()->back();
            }

            $merchant->balance = $merchant->balance - $request->deliveryCharge;
            $merchant->save();
        }

        $update_parcel->invoiceNo        = $request->invoiceno;
        $update_parcel->merchantId       = $request->merchantId;
        $update_parcel->cod              = $request->cod;
        $update_parcel->percelType       = $request->percelType;
        $update_parcel->recipientName    = $request->name;
        $update_parcel->recipientAddress = $request->address;
        $update_parcel->recipientPhone   = $request->phonenumber;
        $update_parcel->productName      = $request->productName;
        $update_parcel->productQty       = $request->productQty;
        $update_parcel->productColor     = $request->productColor;
        $update_parcel->productWeight    = $request->weight;
        $update_parcel->reciveZone       = $request->reciveZone;
        $update_parcel->note             = $request->note;
        $update_parcel->deliveryCharge   = \str_replace(",", "", $request->deliveryCharge);
        $update_parcel->codCharge        = $request->codCharge;
        $update_parcel->merchantAmount   = (int) $request->cod - ((int) \str_replace(",", "", $request->deliveryCharge) + (int) $request->codCharge);
        $update_parcel->merchantDue      = ((int) $request->cod) - ((int) \str_replace(",", "", $request->deliveryCharge) + (int) $request->codCharge);
        $update_parcel->orderType        = $request->orderType;
        $update_parcel->save();

        if ($request->payment_option == 1) {
            RemainTopup::create([
                'parcel_id'     => $update_parcel->id,
                'parcel_status' => 1,
                'merchant_id'   => $update_parcel->merchantId,
                'amount'        => $request->deliveryCharge,
            ]);
        }

        //Save to History table
        $parcel = Parcel::find($request->hidden_id);

        $history            = new History();
        $history->name      = $parcel->recipientName;
        $history->parcel_id = $request->hidden_id;
        $history->done_by   = auth()->user()->name;
        $history->status    = 'Parcel Edited By ' . auth()->user()->name;
        $history->note      = $request->note;
        $history->date      = $parcel->updated_at;
        $history->save();

        Toastr::success('Success!', 'Thanks! your parcel update successfully');

        return back();
    }

    /**
     * Parcel return to marchent
     */
    public function merchantconfirmreturnpayment(Request $request) {
        $parceltype = Parceltype::where('slug', 'return-to-merchant')->first();

        $parcels = Parcel::whereIn('merchantId', $request->marchent_id)->where('status', $parceltype->id)->where('pay_return', 0)->update([
            'pay_return' => 1,
        ]);
        Toastr::success('message', 'Merchant Returns Paid.');

        return back();
    }

    public function merchantInvoice($id) {
        // return $id;
        $parceltype = Parceltype::where('slug', 'return-to-merchant')->first();
        $marchent   = Merchant::find($id);
        $parcels    = Parcel::where('merchantId', $id)->where('status', $parceltype->id)->where('pay_return', 0)->get();
        // dd($marchent->toArray(), $parcels->toArray());

        return view('backEnd.parcel.invoice_return_to_merchat', compact('parcels', 'marchent'));
    }

}
