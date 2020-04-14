/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/

var filesSelected, filesQueued;
/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
function preLoad() {
	if (!this.support.loading) {
		alert("You need the Flash Player 9.028 or above to use SWFUpload.");
		return false;
	}
}
function loadFailed() {
	alert("Something went wrong while loading SWFUpload. If this were a real application we'd clean up and then give you an alternative");
}

function fileQueued(file) {
	//alert('fileQueued');
    try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		//progress.setStatus("Pending...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}

}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadStart(file) {
	try {
        // Проверка размера перед отправкой
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("Uploading...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}
	//alert('Залушка. Обработка загрузки.');
	return true;
}
// Старт загрузки очереди изображений с масштабированием
function uploadResizeStart(file, swfu) //numFilesSelected, numFilesQueued
{//alert('uploadResizedStart :: ' + swfu.getFile(0).ID);
    try {
        //if (filesQueued > 0) {
        /*swfu.startResizedUpload(file, //swfu.getFile(0).ID this.customSettings.thumbnail_width,this.customSettings.thumbnail_height,
                 1024, 1024, SWFUpload.RESIZE_ENCODING.JPEG,
                 85, //this.customSettings.thumbnail_quality,
                false);*/
        this.startResizedUpload(this.getFile(0).ID, //this.customSettings.thumbnail_width,this.customSettings.thumbnail_height,
             '1024', '1024', SWFUpload.RESIZE_ENCODING.JPEG,
             '85', //this.customSettings.thumbnail_quality,
             false);
        //}
    } catch (ex) {
        swfu.debug(ex);
    }
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	//alert('Uploading');
    try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("Uploading...");
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("Complete.");
		progress.toggleCancel(false);
        //if (serverData.substring(0, 7) === "FILEID:") {}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			progress.setStatus("Cancelled");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
    var queue = document.getElementById("fsUploadProgress");
    //queue.visibility = 'visible';
    queue.style.display = 'block';
    // Передаем переменные наружу
    filesSelected = numFilesSelected;
    filesQueued = numFilesQueued;
    /*try {
        if (numFilesQueued > 0) {
            this.startResizedUpload(this.getFile(0).ID, 1024, 1024, SWFUpload.RESIZE_ENCODING.JPEG, 85, false);
        }
    } catch (ex) {
        this.debug(ex);
    }*/
}

function uploadComplete(file) {
    try {
        /*  I want the next upload to continue automatically so I'll call startUpload here */
        if (this.getStats().files_queued > 0) {
            this.startResizedUpload(this.getFile(0).ID, this.customSettings.thumbnail_width, this.customSettings.thumbnail_height, SWFUpload.RESIZE_ENCODING.JPEG, this.customSettings.thumbnail_quality, false);
        } else {
            var progress = new FileProgress(file,  this.customSettings.upload_target);
            progress.setComplete();
            progress.setStatus("All images received.");
            progress.toggleCancel(false);
        }
    } catch (ex) {
        this.debug(ex);
    }
}

/*function btnStartResizedUpload(numFilesSelected, numFilesQueued) //numFilesSelected, numFilesQueued
{
    try {
        if (numFilesQueued > 0) {
            swfu.startResizedUpload(swfu.getFile(0).ID, swfu.customSettings.thumbnail_width,
            swfu.customSettings.thumbnail_height, SWFUpload.RESIZE_ENCODING.JPEG,
            swfu.customSettings.thumbnail_quality, false);
        }
    } catch (ex) {
        swfu.debug(ex);
    }

    return false;
}*/
