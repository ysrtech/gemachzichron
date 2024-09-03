<x-pdf-layout>
<header style="width:900px;text-align:center">
<div style="width:900px;text-align:center">
                <img style="width: 200px;margin-bottom:20px" class="img-rounded" src="{{URL::asset('/img/logo.png')}}">
            </div>
            <div >
            <h2>Transaction Report for <b>{{$member->first_name.' '.$member->last_name }}</b></h2>
            <br />
                <b>Print Date: </b> {{ date('Y-m-d H:i:s') }}<br />
                Total Membership Payments:  <b>${{ $member->membership_payments_total }}</b><br />
                @if($member->loans_payments)
                Total Loan Payments:  <b>${{ $member->loans_payments }}</b><br />
                @endif
              
                <br />
            </div>
            <br />
            
        </header>
        <main style="width:900px;">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($member->transactions as $transaction)
                    @php 
                    {{ if($transaction->status != 1 || $transaction->type != 'Main Transaction') continue; }}
                    @endphp
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->subscription->type }}</td>
                            <td><b>{{ $transaction->process_date }}</td>
                            <td>${{ $transaction->amount }}</td>
                            <td>{{ $transaction->gateway == 'Rotessa' ? 'Bank' : ($transaction->gateway == 'Cardknox' ? 'Credit Card' : 'Manual') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>

    <style>
            

            .panel {
                margin-bottom: 20px;
                background-color: #fff;
                border: 1px solid transparent;
                border-radius: 4px;
                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                box-shadow: 0 1px 1px rgba(0,0,0,.05);
            }

            .panel-default {
                border-color: #ddd;
            }

            .panel-body {
                padding: 15px;
            }

            table {
                width: 100%;
                max-width: 100%;
                margin-bottom: 0px;
                border-spacing: 0;
                border-collapse: collapse;
                background-color: transparent;

            }

            thead  {
                text-align: left;
                display: table-header-group;
                vertical-align: middle;
            }

            th, td  {
                border: 1px solid #ddd;
                padding: 6px;
            }

            .well {
                min-height: 20px;
                padding: 19px;
                margin-bottom: 20px;
                background-color: #f5f5f5;
                border: 1px solid #e3e3e3;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
                box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
            }
        </style>
</x-pdf-layout>