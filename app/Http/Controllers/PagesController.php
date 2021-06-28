<?php

namespace App\Http\Controllers;

use App\Pin;
use App\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function home()
	{		
				
		return view('pages.index');
	}

	public function runValidate($request)
    {
        $this->validate($request, [
            'pin' => 'required',
            'reg_number' => 'required',
            'serial_number' => 'required',                                
            ]);     
    } 

    public function check(Request $request)
    {
        $this->runValidate($request);

        // Start transaction!
        //DB::beginTransaction();

        $pin = Pin::where('pin', $request->pin)
		    ->where('serial_number', $request->serial_number)		   
		    ->first();
		$student = Result::where('exam_number', $request->reg_number)->first();
        if($pin) {
        	if($pin->use_status == '' || ($pin->use_status == 'used' && $pin->results_id == $student->id )) {
        		if($student) {
	        		if($pin->count <= 5) {
	        			$pin->update([
		            		'count' => $pin->count + 1,
		            		'results_id' => $student->id,
		            		'use_status' => 'used'
		            	]);
	        			return view('pages.result', compact('student'));
		        		
	        		} else{
	        			return back()->withErrors(['msg' => 'PIN has expired!!']);
	        		}

	        	} else {
	        		return back()->withErrors(['msg' => 'Invalid Examination Number']);
	        	}
        	} else {
        		return back()->withErrors(['msg' => 'PIN used by another user!']);
        	}
        	
        } else {
        	return back()->withErrors(['msg' => 'Invalid PIN and Serial Number']);
        }
        
    
    // Commit the queries!
    //DB::commit();
        
        return back()->with('success', 'New student registered successfully.');
    }
}
