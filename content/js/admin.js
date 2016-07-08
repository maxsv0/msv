$(document).ready(function() {
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('.tab-content').resize();
    })
     
    var hash = window.location.hash;
	$('.nav-tabs a[href="' + hash + '"]').tab('show');
	
	
	$('#uploadModal').on('shown.bs.modal', function() {
	   	clearUploadModal();
	})
});

function removeLink(x) {
	$('#path-'+x).val('');
	$('#img-'+x).css('opacity', "0.2");
	$('#value-'+x).css('text-decoration', "line-through");
	
    return true;
}

function uploadFrameLoad(iframe) {
    var doc = iframe.contentDocument || iframe.contentWindow.document;
    var path = doc.body.innerHTML;
    
    if (path) {
    	$("#uploadFilePath").val(path);
    	$("#uploadAlert").addClass("alert alert-success").html("File successfully saved");
    	
    	$("#uploadPreview").removeClass("hide");
    	$("#uploadPreview img").attr("src", path);
    	
    	
    } else {
    	$("#uploadAlert").addClass("alert alert-danger").html("Error saving file");
    }
    
    $("#uploadStatus").removeClass("hide");
}


function clearUploadModal() {
	$("#uploadPreview").addClass("hide");
	$("#uploadAlert").addClass("hide");
	$("#iUploadFile").attr("src", "");
}

function openUploadModal(x) {
	$("#iUploadField").val(x);
	
	$("#uploadModal").modal('show');
}

function closeUploadModal(x) {
	var id = $("#iUploadField").val();
	var path = $("#uploadFilePath").val();
    	
	if (id && path) {
		$("#img-"+id).attr("src", path);
		$("#value-"+id).html(path);
		$("#path-"+id).val(path);
		
		$("#uploadModal").modal('hide');
	}
	

}
