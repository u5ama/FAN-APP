$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    const table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: APP_URL + '/payments',
            type: 'GET',
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'fan', name: 'fan'},
            {data: 'player', name: 'player'},
            {data: 'gift_name', name: 'gift_name'},
            {data: 'gift_price', name: 'gift_price'},
            {data: 'gift_status', name: 'gift_status'},
            {data: 'invoice_status', name: 'invoice_status'},
            {data: 'amount', name: 'amount'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        drawCallback: function () {
            funTooltip()
        },
        language: {
            processing: '<div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Loading...</span></div>'
        },
        order: [[0, 'ASC']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
    })
})
