<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckResultRequest;
use App\Services\PinService;
use App\Services\ResultService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function __construct(
        private readonly PinService $pinService,
        private readonly ResultService $resultService
    ) {}

    /**
     * Display the home page.
     */
    public function home(): View
    {
        return view('pages.index');
    }

    /**
     * Check student result using PIN and examination number.
     */
    public function check(CheckResultRequest $request): View|RedirectResponse
    {
        $result = $this->resultService->findResultByExamNumber($request->reg_number);

        if (! $result) {
            return back()->withErrors(['msg' => 'Invalid Examination Number']);
        }

        $pinValidation = $this->pinService->validateAndUsePinForResult(
            $request->pin,
            $request->serial_number,
            $result
        );

        if (! $pinValidation['success']) {
            return back()->withErrors(['msg' => $pinValidation['message']]);
        }

        return view('pages.result', ['student' => $result]);
    }
}
