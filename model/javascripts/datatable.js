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

    var adminTable = $('#adminTable').DataTable();

    $('#adminTable tbody').on( 'click', '.delete-user', function (e) {
        e.preventDefault();
        var name = adminTable.row(this).data()[0];

        if(name != 'admin') {
            if(confirm("Are you sure you want to delete " + name + "?")) {
                $.ajax({
                    type: "POST",
                    data: {"delete": true, "delUser": name},
                    dataType: "text",
                    success: function(data) {
                        location.href = window.location.href;
                    }
                });
            }
        }
    });

    $('#adminTable tbody').on( 'click', '.edit', function (e) {

        var username = adminTable.row(this).data()[0];
        var apiKey = adminTable.row(this).data()[1];

        document.getElementById("username").value = username;
        document.getElementById("api_key").value = apiKey;

        $('#showModal').click();
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