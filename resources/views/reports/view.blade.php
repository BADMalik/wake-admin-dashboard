
@extends('layouts.app', ['page' => __('Reports'), 'pageSlug' => 'reports'])

@section('content')
    <style>
        table {
            overflow: hidden;
        }
        table th, table td {
            padding: 0.25em 0.5em;
            text-align: left;
            vertical-align: top;
        }
        .small-cells td {
            padding: 1px 7px !important;
        }
        table tr {
            transition: all 1s ease-in-out;
        }
        table td {
            transition: all 1s ease-in-out;
        }
        table tr.slide-out {
            transform: translateX(100%);
        }

        /* loader */
        .loader {
 
            display: none;
            border: 4px solid #ebebf0;
            border-radius: 50%;
            border-top: 4px solid #2e3d62;
            border-bottom: 4px solid #2c3a5b;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
            margin-left: 30px;
        }

        /* Safari */
        @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }

        .dropup {
            display: inline-block;
        }

    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<div>
    <div class="dropdown">
        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{$current}}
        </a>

        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        @foreach ($sheets as $sheet)
            <a class="dropdown-item" href="javascript:void(0)" onClick="loadSheet('{{$sheet}}');">{{$sheet}}</a>
        @endforeach
        </div>
    </div>
    <div class="loader"></div>
</div>
    <table class="table table-striped table-borderless table-sm">
        <thead>
            <tr class="slide-out" id="heading-row">
            @foreach ($headings as $heading)
                <th>{{$heading}}</th>
            @endforeach
            </tr>
        </thead>
        <tbody id="table-body">
        @foreach ($rows as $row)
            <tr class="slide-out small-cells">
            @forelse ($row as $data)
                <td>{{$data}}</td>
                @empty
                <td></td>
            @endforelse
            </tr>   
        @endforeach
        </tbody>
    </table>
</body>

<script>

    var loadedRows = Array.from(document.querySelectorAll('tr'));

    function slideOut(row) {
        row.classList.add('slide-out');
    }


    function slideIn(row, index) {
        setTimeout(function() {
            row.classList.remove('slide-out');
            row.classList.remove('small-cells');
        }, (index + 5) * 100);  
    }

    function slideOutAnimation(row, index) {
        setTimeout(function() { 
            row.classList.add('slide-out');
        }, (index + 5) * 50); 
    }

    loadedRows.forEach(slideIn);

    function loadSheet(month){
        document.getElementById('dropdownMenuLink').innerText = month;
        let url = `/reports/${month}`;
        
        $.ajax(
            {
                url: url,
                beforeSend: function () {
                    $('.loader').css('display', 'inline-block');
                    loadedRows.forEach(slideOutAnimation);
                },
                success: function(response){
                    var data = response.data;   
                    var headings = '';
                    data.headings.forEach(function (heading) {
                        headings += `<th>${heading}</th>`;
                    });
                    var rows = '';
                    data.rows.forEach(function (row) {
                        rows += `<tr class="slide-out small-cells">`;
                        var data = '';
                        if(row.length === 0){ data += '<td></td>'} else {
                        row.forEach(function (rowdata) {
                            data += `<td>${rowdata}</td>`;
                        }); }
                        rows += data;
                        rows += `</tr>`;
                    });

                    
                    
                    document.getElementById('heading-row').innerHTML = headings;
                    document.getElementById('table-body').innerHTML  = rows;
                    loadedRows = Array.from(document.querySelectorAll('tr'));

                    loadedRows.forEach(slideIn);

                    $('.loader').hide();
                }
            }
        );
    }


</script>
@endsection