<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use App\Services\PdfWrapper;


class MemberController extends Controller
{
    public function index(Request $request)
    {

    $results = [
    'filters' => $request->all('search', 'archived', 'membership_since', 'membership_type', 'plan_type_id', 'active_membership', 'sort'),
    'members' => Member::search($request->search)
        ->filterWithTrashed($request->archived)
        ->filterNull($request->only('membership_since'))
        ->filterBoolean($request->only('active_membership'))
        ->filter($request->only('membership_type', 'plan_type_id','membership_due'))
        ->sort($request->sort)
        ->withMembershipPaymentsTotal()
        ->withLoansCount()
        ->withLoansTotal()
        ->withLoansPayments()
        ->with('planType')
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->paginate()
    ];

        return Inertia::render('Members/Index', $results);
    }

    public function create()
    {
        return Inertia::render('Members/Edit');
    }

    public function store(CreateMemberRequest $request)
    {
        $member = Member::create($request->validated());

        return redirect()
            ->route('members.show', $member)
            ->snackbar('Member created.');
    }

    public function show(Member $member)
    {
        return Inertia::render('Members/Show', [
            'member' => $member
                ->load('planType')
                ->append('membership_payments_total')
        ]);
    }

    public function edit(Member $member)
    {
        return Inertia::render('Members/Edit', [
            'member' => $member
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return redirect()
            ->route('members.show', $member)
            ->snackbar('Member updated.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return back()->snackbar('Member archived.');
    }

    public function restore(Member $member)
    {
        $member->restore();

        return back()->snackbar('Member restored.');
    }

    public function transactionsreport(Member $member){

        $member = $member->load(['transactions' => fn($q) => $q->with('subscription:id,type')->orderByDesc('process_date')]);

        $filename = 'gemachhakehilos_report_'.$member->id.'.pdf';

        $pdfGenerator = new Browsershot();

        $headerHtml =  view('pdfs._header')->render();
        $footerHtml =  view('pdfs._footer')->render();
        $bodyHtml = view('pdfs.transactions_report',['member' => $member])->render();

        //return $bodyHtml;

        $pdf = Browsershot::html($bodyHtml)
        ->margins(15, 15, 15, 15)
        ->showBrowserHeaderAndFooter()
        ->headerHtml($headerHtml)
        ->footerHtml($footerHtml)
        ->waitUntilNetworkIdle()
        ->pdf();

        Storage::put('pdf_reports/' .$filename, $pdf);

        $headers = [
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Content-type"        => "application/pdf",
            "Content-Disposition" => "attachment; filename=$filename",
            "Expires"             => "0",
            "Pragma"              => "no-cache",
            "Content-Length" => strlen($pdf)
        ];



        //
        return Storage::download('pdf_reports/' .$filename, $filename, $headers);

    }
}
