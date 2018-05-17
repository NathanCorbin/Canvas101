$(document).ready(function() {
    var table = $('#engagementTable').DataTable({
        "bUseRendered": true
    });

    $('#engagementTable tbody').on( 'click', 'tr', '.clickable', function () {
    	//var row = $(this).closest('tr');
        //var id = row.find('td:eq(1)').text();
        var data = table.row(this).data();
        
        var id = data[0];
        var name = data[1];
        var lastLogin = data[2];
        var timeElapsed = data[3];
        
        alert(id + ' ' + name + ' ' + lastLogin + ' ' + timeElapsed);
    	$('#showModal').click();
    });
});