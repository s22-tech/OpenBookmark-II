function openAll() {
	$('.bookmark_href').each(function(index) {
		//alert(index + ': ' + $(this).attr('href'));
		window.open($(this).attr('href'),'_blank');
	  });

	  return false;
}

function reloadclose() {
	window.opener.location.reload();
//   window.parent.location.reload();
	self.close();
}  // open(location, '_self').close();

function bookmarknew(folderid) {
	bookmark_new = window.open('/bookmarks/new_bookmark.php?folderid=' + folderid, 'bookmarknew', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=550,height=600');
}

function bookmarkedit(bmlist) {
	if (bmlist == '') {
		alert('No Bookmarks selected.');
	}
	else {
		bookmark_edit = window.open('/bookmarks/edit_bookmark.php?bmlist=' + bmlist, 'bookmarkedit', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=550,height=650');
	}
}

function bookmarkmove(bmlist) {
	if (bmlist == '') {
		alert('No Bookmarks selected.');
	}
	else {
		bookmark_move = window.open('/bookmarks/move_bookmark.php', 'bmlist', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=450');
	}
}

function bookmarkdelete(bmlist) {
	if (bmlist == '') {
		alert('No Bookmarks selected.');
	}
	else {
		bookmark_delete = window.open('/bookmarks/delete_bookmark.php?bmlist=' + bmlist, 'bookmarkdelete', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=450');
	}
}

function foldernew(folderid) {
	folder_new = window.open('/folders/new_folder.php?folderid=' + folderid, 'foldernew', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=200');
}

function folderedit(folderid) {
	if (folderid == '' || folderid == '0') {
		alert('No folder selected -- folderedit()');
	}
	else {
		folder_edit = window.open('/folders/edit_folder.php?folderid=' + folderid, 'folderedit', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=220');
	}
}

function foldermove(folderid) {
	if (folderid == '' || folderid == '0') {
		alert("No folder selected -- foldermove()");
	}
	else {
		folder_move = window.open('/folders/move_folder.php', 'foldermove', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=450');
	}
}

function folderdelete(folderid) {
	if (folderid == '' || folderid == '0') {
		alert("No folder selected -- folderdelete()");
	}
	else {
		folder_delete = window.open('/folders/delete_folder.php?folderid=' + folderid, 'folderdelete', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=200');
	}
}

function selectfolder(url) {
	select_folder = window.open('/folders/select_folder.php' + url, 'selectfolder', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=450');
}

function chpw() {
	chpw_window = window.open('/change_password.php', 'chpw', 'toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=200');
}

function checkselected() {
	var i;
	var parameter = '';
	for (i = 0 ; i < window.document.forms['bookmarks'].elements.length ; i++) {
		if (window.document.forms['bookmarks'].elements[i].checked == true) {
			parameter = parameter + window.document.forms['bookmarks'].elements[i].name + '_';
		}
	}
	result = parameter.replace(/_$/, '');
	return result;
}

/* This function is from the following location:
   http://www.squarefree.com/bookmarklets/
*/

function selectthem(boxes, stat) {
	var x,k,f,j;
	x = document.forms;

	for (k = 0; k < x.length; ++k) {
		f = x[k];
		for (j = 0; j < f.length; ++j) {
			if (f[j].type.toLowerCase() == 'checkbox') {
				if (boxes == 'all') {
					f[j].checked = true ;
				}
				else if (boxes == 'none') {
					f[j].checked = false ;
				}
				else if (boxes == 'toggle') {
					f[j].checked = !f[j].checked ;
				}
				else if (boxes == 'checkall') {
					f[j].checked = stat;
				}
			}
		}
	}
}
