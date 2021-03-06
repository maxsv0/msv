$(document).ready(function() {
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('.tab-content').resize();
    })
     
    var hash = window.location.hash;
	$('.nav-tabs a[href="' + hash + '"]').tab('show');
	
	
	$('#uploadModal').on('shown.bs.modal', function() {
	   	clearUploadModal();
	})
	
	// bind auto form submit on file change
	$("#iUploadFile").change(function (){
		var fileName = $(this).val();
		if (fileName != '') {
			// submit form.
			$("#fUploadForm")[0].submit();
		}
     });
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
    	
    	$("#btnUploadSave").trigger('click');
    } else {
    	$("#uploadAlert").addClass("alert alert-danger").html("Error saving file");
    }
    
    $("#uploadStatus").removeClass("hide");
}


function clearUploadModal() {
	$("#fUploadForm")[0].reset();
	$("#uploadPreview").addClass("hide");
	$("#uploadAlert").addClass("hide");
	$("#iUploadFile").attr("src", "");
}

function openUploadModal(x) {
	clearUploadModal();
	
	$("#iUploadField").val(x);
	
	$("#uploadModal").modal('show');
	
   	$("#iUploadFile").trigger('click'); 
}

function closeUploadModal(x) {
	var id = $("#iUploadField").val();
	var path = $("#uploadFilePath").val();
    	
	if (id && path) {
		$("#img-"+id).attr("src", path);
		$("#value-"+id).html(path);
		$("#path-"+id).val(path);
		$("#alert-"+id).hide();
		
		$("#uploadModal").modal('hide');
	}
}



function openPicLibraryModal(x) {
	$("#iUploadField").val(x);
	
	var pic_path = $("#img-"+x).attr("src");
	var pic_path = pic_path.substr(0,pic_path.indexOf('?'));
	
	if (pic_path && pic_path.lastIndexOf("data", 0) !== 0) {
		$("#picPreview").attr("src", pic_path);
	} else {
		var pic_path = $("#img-pic").attr("src");
		if (pic_path) {
			$("#picPreview").attr("src", pic_path);
		} else {
			var path = $("#uploadFilePath").val();
			if (path) {
				$("#picPreview").attr("src", path);
			}
		}
	} 
	
	var ext = pic_path.split('.').pop();
	if (ext == "jpg") {
		// browser req
		ext = "jpeg";
	}
	console.log("File: "+$("#picPreview").attr("src")+", type: "+ext);
	
	$("#type-"+x).val(ext);
	
	var aspect = $("#aspectRatio-"+x).val();
	var imgWidth = $("#width-"+x).val();
	var imgHeight = $("#height-"+x).val();
	
	console.log("aspect:"+aspect+", imgWidth:"+imgWidth+", imgHeight:"+imgHeight);
	
	$("#libraryModal").modal('show');
	
	var cropBoxData;
    var canvasData;
	
	$('#libraryModal').on('shown.bs.modal', function () {
		$('#picPreview').cropper({
			aspectRatio: aspect,
	        responsive: true,
	        movable: true,
	        zoomable: true,
	        rotatable: true,
	        scalable: true,
			built: function () {
				$('#picPreview').cropper('setCropBoxData', { width: imgWidth, height: imgHeight });
			}
		});
	});
}


function closePicLibraryModal() {
	
	
	$('#picPreview').cropper('getCroppedCanvas').toBlob(function (blob) {
		var formData = new FormData();
		var field = $("#iUploadField").val();
		var table = $("#iUploadTable").val();
		var id = $("#itemID").val();
		
		formData.append('uploadFile', blob, field);
		formData.append('table', table);
		formData.append('field', field);
		formData.append('itemID', id);
	
		$.ajax('/api/uploadpic/', {
			method: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (path) {
				console.log('Upload success');
				console.log(path);
				
				$("#uploadFilePath").val(path);
				$("#img-"+field).attr("src", path+"?"+Math.random());
				$("#value-"+field).html(path);
				$("#path-"+field).val(path);
			},
			error: function () {
			  console.log('Upload error');
			}
			});
		}, "image/"+$("#type-"+$("#iUploadField").val()).val(), 1);
	
	
	$("#libraryModal").modal('hide');
}
