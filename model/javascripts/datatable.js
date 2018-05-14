$(document).ready(function() {
    $('#engagementTable').DataTable();

    $('.clickable').click(function(){
    	var row = $(this).closest('tr');
    	var id = row.find('td:eq(1)').text();
        alert(id);
    	$('#showModal').click();
    });
});