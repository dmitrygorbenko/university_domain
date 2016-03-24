<?php
Header("Content-type: text/javascript");

Header("Expires: Wed, 11 Nov 1998 11:11:11 GMT\n".
"Cache-Control: no-cache\n".
"Cache-Control: must-revalidate\n".
"Pragma: no-cache");
?>
	function MM_findObj(n, d) {
		var p, i, x;

		if (!d)
			d=document;

		if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
			d = parent.frames[n.substring(p+1)].document;
			n = n.substring(0,p);
		}

		if (!(x = d[n]) && d.all)
			x = d.all[n];

		for (i=0; !x && i < d.forms.length; i++)
			x = d.forms[i][n];

		for(i=0; !x && d.layers && i < d.layers.length; i++)
			x = MM_findObj(n, d.layers[i].document);

		if (!x && d.getElementById)
			x = d.getElementById(n);

		return x;
	}

	function MM_swapImage() {
		var i, j=0, x, a = MM_swapImage.arguments;
		document.MM_sr=new Array;
		for(i=0; i < (a.length - 2); i+=3)
			if ((x = MM_findObj(a[i])) != null) {
				document.MM_sr[j++] = x;
				if (!x.oSrc)
					x.oSrc = x.src;
				x.src = a[i+2];
			}
	}

	function MM_swapImgRestore() {
		var i, x, a=document.MM_sr;
		for(i=0; a && i < a.length && (x = a[i]) && x.oSrc; i++)
			x.src = x.oSrc;
	}

// ******************    NS Servers list   ****************************

	function checkall_ns_servers(val) {
		ch_count = document.getElementById("ns_servers_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("ns_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_ns_servers() {
		frm = document.ns_remove_form;
		ch_count = document.getElementById("ns_servers_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("ns_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("ns_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select NS servers !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}

// ******************    MX Servers list   ****************************

	function checkall_mx_servers(val) {
		ch_count = document.getElementById("mx_servers_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("mx_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_mx_servers() {
		frm = document.mx_remove_form;
		ch_count = document.getElementById("mx_servers_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("mx_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("mx_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select MX servers !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}

// ******************    Delegation Zones list   ****************************

	function checkall_delegation_zones(val) {
		ch_count = document.getElementById("delegation_zones_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("delegation_zone_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_delegation_zones() {
		frm = document.delegation_zone_remove_form;
		ch_count = document.getElementById("delegation_zones_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("delegation_zone_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("delegation_zone_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select Delegation Zones !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}

// ******************    Clients list   ****************************

	function checkall_clients(val) {
		ch_count = document.getElementById("clients_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("client_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_clients() {
		frm = document.client_remove_form;
		ch_count = document.getElementById("clients_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("client_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("client_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select clients !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}
// ******************    Zones list   ****************************

	function checkall_zones(val) {
		ch_count = document.getElementById("zones_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("zone_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_zones() {
		frm = document.zone_remove_form;
		ch_count = document.getElementById("zones_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("zone_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("zone_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select zones !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}

// ******************    Subdomains list   ****************************

	function checkall_subdomains(val) {
		ch_count = document.getElementById("subdomains_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("subdomain_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_subdomains() {
		frm = document.subdomain_remove_form;
		ch_count = document.getElementById("subdomains_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("subdomain_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("subdomain_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select subdomains !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}

// ******************    Redirectors list   ****************************

	function checkall_redirectors(val) {
		ch_count = document.getElementById("redirectors_count");
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("redirector_checkbox_" + i);
			ch_box.checked = val;
		}
	}

	function remove_redirectors() {
		frm = document.redirector_remove_form;
		ch_count = document.getElementById("redirectors_count");
		ch_got = 0;
		url = "";
		for( i=0 ; i < ch_count.title ; i++) {
			ch_box = document.getElementById("redirector_checkbox_" + i);
			if (ch_box.checked == "1") {
				elem_id = document.getElementById("redirector_" + i);
				url = url + elem_id.title + ":";
				ch_got++;
			}
		}
		if (ch_got == 0) {
			alert("Select redirectors !");
			return;
		}
		frm.ids.value = url;
		frm.submit();
	}


// ******************    Common functions   ****************************

	function update_page(url, id_tag, arg) {
		tag = document.getElementById(id_tag);
		document.location = url + "?" + arg + "=" + tag.value;
	}

	function reply_message(id, folder) {
		url = "reply.php?id=" + id + "&folder=" + folder;
		document.location = url;
	}

	function OpenTree() {
		libwindow=window.open("ftp_select.php", "_blank", "menubar=no, width=470, height=350, top=100, left=100, scrollbars=yes");
	}

	function OpenID() {
		mywin = window.open('id_select.php', '_blank', 'width=500, height=500, top=50, left=50, menubar=no,  scrollbars=yes');
	}

	function AddAddress(strType, strAddress) {
		obj = eval('document.ComposeForm.'+strType);
		if (obj.value == '')
			obj.value = strAddress;
		else
			obj.value = obj.value + ', ' + strAddress;
	}

	function CopyText(dir) {
		window.opener.document.forms[0].homedir.value = dir;
		self.close();
	}

	function CopyID(uid, gid) {
		window.opener.document.forms[0].uid.value = uid;
		window.opener.document.forms[0].gid.value = gid;
		self.close();
	}

	function Check_Confirm() {

		ns_count = document.getElementById("ns_count");
		mx_count = document.getElementById("mx_count");

		var primary_count = 0;
		var ns_server_count = 0;
		var mx_server_count = 0;

		for (i=0; i < ns_count.title; i++) {
			ns_use = document.getElementById("ns_checkbox_" + i);
			ns_role = document.getElementById("ns_role_" + i);

			if (ns_use.checked == true) {
				if (ns_role.value == "primary")
					primary_count++;

				ns_server_count++;
			}
		}

		if (ns_server_count == 0) {
			alert("Вы не выбрали NS сервера. Минимум два NS сервера");
			return;
		}
		if (ns_server_count == 1) {
			alert("Вы выбрали один NS сервер. Минимум два NS сервера");
			return;
		}
		if (primary_count == 0) {
			alert("Вы не выбрали первичный NS сервер");
			return;
		}
		if (primary_count > 1) {
			alert("Вы выбрали более одного первичного NS сервера");
			return;
		}

		for (i=0; i < mx_count.title; i++) {
			mx_use = document.getElementById("mx_checkbox_" + i);
			mx_prior = document.getElementById("mx_prior_" + i);

			if (mx_use.checked == true) {
				if (mx_prior.value == "") {
					alert("Вы не указали приоритет MX сервера");
					return;
				}
				if (mx_prior.value < 0) {
					alert("Вы указали не верный приоритет для MX сервера");
					return;
				}
				if (mx_prior.value > 65535) {
					alert("Вы указали не верный приоритет для MX сервера");
					return;
				}
				mx_server_count++;
			}
		}
		if (mx_server_count == 0) {
			alert("Вы не выбрали MX сервер. Минимум один MX сервер");
			return;
		}

		for (i=0; i < mx_count.title - 1; i++) {
			mx_prior_i = document.getElementById("mx_prior_" + i);
			for (k=i+1; k < mx_count.title; k++) {
				mx_prior_k = document.getElementById("mx_prior_" + k);
				if (mx_prior_i.value == mx_prior_k.value) {
					alert("Вы указали два одинаковых приоритета для MX серверов");
					return;
				}
			}
		}

		document.delegation_zone_manage.submit();
	}
