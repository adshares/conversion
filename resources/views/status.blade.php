@extends('main')
@section('title', 'Conversion status')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center mt-4">@yield('title')</h1>
            </div>
        </div>
        <div class="row">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Address</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Key</th>
                </tr>
                </thead>
                <tbody>
                {{--<tr>--}}
                {{--<th scope="row">23-05-2018 09:44:25</th>--}}
                {{--<td>0x9c717a19aa86349769d9cc6815ab60ca2c987a8f</td>--}}
                {{--<td>21 340.6789</td>--}}
                {{--<td>0x0987654321</td>--}}
                {{--</tr>--}}
                </tbody>
            </table>
        </div>
    </div>
@endsection