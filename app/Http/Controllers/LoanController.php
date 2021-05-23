<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\LoanPayment;
use App\Models\LoanApplication;
use App\Http\Requests\LoanApplication as LoanApplicationRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoanController extends Controller
{
    //

    public function applyLoan(LoanApplicationRequest $request)
    {   
        try{
            $loanApplication=new LoanApplication();
            $loanApplication->reference_id=time() . rand(0, 9);
            $loanApplication->amount=$request->amount;
            $loanApplication->loan_term=$request->loan_term;
            $loanApplication->status=LoanApplication::STATUS_PENDING;
            //get user from authenticated request
            $loanApplication->user_id=$request->user()->id;
            $loanApplication->save();

            return response(['message'=>'Loan Application Submited','reference_id'=>$loanApplication->reference_id],200);

        }
        catch(Exception $e)
        {   
            Log::info('loan application storage error',[$e->getMessage()]);
            $error=['message'=>'Unable to process data, please try again later!'];
            return response($error,500);
        }

    }


    public function changeLoanApplicationStatus(Request $request)
    {
        $request->validate([
            'loan_application_id'=>'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        try{

            $errors=[];
            $application=LoanApplication::where('reference_id',$request->loan_application_id)->first();
            if(empty($application))
            {   
                $errors[]=['loan_application_id'=>'Application does not exist'];
            }
            else if($application->status!=LoanApplication::STATUS_PENDING)
                $error[]=['Status'=>'Status not Valid'];

            if(!in_array($request->status,[LoanApplication::STATUS_CANCELLED,LoanApplication::STATUS_APPROVED]) )
                $errors[]=['Status'=>'Status not valid'];

            if(count($errors)>0)
                return response(['message'=>'The given data was invalid.','errors'=>$errors],422);

            $application->status=$request->status;
            if(!empty($request->remarks))
            {
                $application->remarks=$request->remarks;
            }
            $application->save();

            return response(['message'=>'Status Updated'],200);


        }
        catch(Exception $e)
        {
            Log::info('loan application status change error',[$e->getMessage()]);
            $error=['message'=>'Unable to process data, please try again later!'];
            return response($error,500);
        }
    }


    public function weeklyLoanPayments(Request $request)
    {
        $request->validate([
            'loan_application_id'=>'required|string',
            'amount' => 'required|integer|gt:0',
            'weekly_payment_amount'=>'nullable|integer|gt:0',
        ]);

        $errors=[];
        try{
            $application=LoanApplication::where('reference_id',$request->loan_application_id)->first();
            if(empty($application))
            {   
                $errors[]=['loan_application_id'=>'Application does not exist'];
            }
            if(!empty($application) && $application->status==LoanApplication::STATUS_APPROVED){

                $lastDateForPayment=Carbon::parse($application->updated_at)->addYear($application->loan_term);
             
                if(!(Carbon::now()->lessThanOrEqualTo($lastDateForPayment)))
                {
                    $error[]=["payment"=>['Term duration to pay payments has passed away,payment request declined.']];
                }
                //Only user who has made application is authorized for payment
                if($request->user()->id!=$application->user_id)
                    return response(['message'=>'Unauthorized'],401);
                
                if(!empty($request->weekly_payment_amount) && $request->weekly_payment_amount>$request->amount)
                    $errors[]=["payment"=>['Payment amount must be equal to greater than weekly payment amount']];

            }
            else{
                $errors[]=['payment'=>'Payment cannot be done against application with status as '.$application->status];
            }

            if(count($errors)>0)
                    return response(['message'=>'The given data was invalid.','errors'=>$errors],422);


            //Assuming Payment is always completed
            $loanPayment=new LoanPayment();
            $loanPayment->reference_id=time(). rand(0, 9);
            $loanPayment->loan_application_id=$application->id;
            $loanPayment->amount=$request->amount;
            $loanPayment->status=LoanPayment::STATUS_COMPLETED;
            $loanPayment->save();

            //Here we can also check Remaning balance of payment after interest and internally change the status of loan application as completed
            return response(['message'=>'Payment Completed','reference_id'=>$loanPayment->reference_id],200);

        }
        catch(Exception $e){
            Log::info('loan application status change error',[$e->getMessage()]);
            $error=['message'=>'Unable to process data, please try again later!'];
            return response($error,500);
        }
    }
    
}
