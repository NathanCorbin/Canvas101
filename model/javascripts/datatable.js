$(document).ready(function() {
    var table = $('#engagementTable').DataTable({
        columnDefs: 
        [{ 
            type: 'natural', 
            targets: [0, 1, 2, 3, 4]
        },
        {
            targets: [6],
            visible: false
        },
        {
            targets: [2, 3, 4, 5],
            searchable: false
        },
        {
            targets: [0, 1, 2, 3, 4, 5, 6],
            className: 'mdl-data-table__cell--non-numeric'
        }],
        aaSorting: [[3, 'desc']],
        bLengthChange: false,
        responsive: true
    });

    $('#engagementTable tbody').on( 'click', '.clickable', function (e) {
        e.preventDefault();
        
        data = table.row(this).data();
        
        var firstName = lastName = "";
        var email = data[6];
        firstName = data[1].split(" ")[0];

        if(data[1].split(" ").length > 1)
            lastName = data[1].split(" ")[1];
        else if(data[1].split(" ").length > 2)
            lastName = data[1].split(" ")[2];
        
        document.getElementById("first_name").value = firstName;
        document.getElementById("last_name").value = lastName;
        document.getElementById("email").value = email;
       
        $('#showModal').click();
    });
});
